<?php

class notesAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
        
        isset($_POST['get_note_by_id']) ? $this->get_note_by_id($_POST['id']) : null;


        //prevent users accessing this page manually
        if($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
        
    }



    public function get_note_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM notes WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        if($stmt->rowCount() == '1') {
            $note = $stmt->fetch();
            $this->output->note = $note->note;

        } else {
            $this->data->errors[] = "لا توجد ملاحظة بهذا الرقم";
        }

        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}