<?php

class notesAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();

        isset($_POST['get_note_by_id']) ? $this->get_note_by_id($_POST['id']) : null;
        isset($_POST['get_notes']) ? $this->getNotes() : null;


        //prevent users accessing this page manually
        if ($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
    }



    public function get_note_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM notes WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        if ($stmt->rowCount() == '1') {
            $note = $stmt->fetch();
            $this->output->note = $note->note;
        } else {
            $this->data->errors[] = "لا توجد ملاحظة بهذا الرقم";
        }

        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    public function getNotes()
    {

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $note_taker_id = isset($_POST['note_taker_id']) ? $_POST['note_taker_id'] : "";
        $create_date = isset($_POST['create_date']) ? date("d/m/Y", strtotime($_POST['create_date'])) : "";
        $note = isset($_POST['note']) ? $_POST['note'] : "";
        
        $start = ($page - 1) * 10;

        //notes
        $sql = "SELECT notes.*, users.name AS note_taker_name FROM notes 
                LEFT JOIN users ON notes.note_taker_id = users.id 
                WHERE 
                notes.note_taker_id LIKE '%$note_taker_id%' AND 
                notes.create_date LIKE '%$create_date%' AND 
                notes.note LIKE '%$note%'
                ORDER BY id DESC
                LIMIT $start, 10
                ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() != '0') {
            $this->notes = $stmt->fetchAll();
        } else {
            array_push($this->errors, 'لا يوجد ملاحظات لعرضها');
        }

        if ($stmt->rowCount() != '0') {
            $this->users = $stmt->fetchAll();
        } else {
            array_push($this->errors, 'لا يوجد مستخدمين مسجلين في الموقع لعرضهم');
        }

        //store data
        foreach ($this->notes as $notes) {
            !isset($notes->note_taker_name) ? $notes->note_taker_name = 'غير موجود' : null;
            $notes->create_date = date("d/m/Y h:ia", strtotime($notes->create_date));
        }

        //send data to the data
        $this->data->notes = $this->notes;

        //Get the total count
        $sql = "SELECT COUNT(*) AS numOfResults FROM notes                 
                WHERE 
                notes.note_taker_id LIKE '%$note_taker_id%' AND 
                notes.create_date LIKE '%$create_date%' AND 
                notes.note LIKE '%$note%'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;

        $this->data->page_count = ceil($this->data->numOfResults / 10);
        $this->data->role = $_SESSION['lvl'];

        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
