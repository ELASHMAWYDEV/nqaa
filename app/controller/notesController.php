<?php

class notesController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        //role
        $this->role('فني') ? header('location: stats') :null;

        isset($_POST['update_note']) ? $this->takeAction($_POST['note_id']) : null;
        isset($_POST['add_note']) ? $this->addNote($_POST['note'], $_SESSION['id']) : null;
        isset($_POST['edit_note']) ? $this->editNote($_POST['id'], $_POST['note']) : null;
        isset($_POST['delete_note']) ? $this->deleteNote($_POST['id']) : null;


        $this->loggedIn();
        $this->view('notes');
        $this->view->title = "شركة نقاء | الملاحظات";
        $this->getNotes();
        $this->getUsers();
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        $this->view->pagination();

    }


    public function getnotes()
    {
        //notes
        $sql = "SELECT * FROM notes ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() != '0')
        {
            $this->notes = $stmt->fetchAll();
            // $allDone = false;
            // foreach($this->notes as $note) {
            //     if(!empty($note->status)) {
            //         $allDone = true;
            //     }
            // }
            // if($allDone == true) {
            //     array_push($this->errors, 'تم الاطلاع علي جميع الملاحظات بالفعل');
            // }
        }
        else
        {
            array_push($this->errors, 'لا يوجد ملاحظات لعرضها');
        }

        //users
        $sql = "SELECT * FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() != '0')
        {
            $this->users = $stmt->fetchAll();
        }
        else
        {
            array_push($this->errors, 'لا يوجد مستخدمين مسجلين في الموقع لعرضهم');
        }

        //store data
        foreach($this->notes as $notes)
        {
            foreach($this->users as $users)
            {
                if($notes->note_taker_id == $users->id) {
                    $notes->note_taker_name = $users->name;
                    break;
                }
            }
            !isset($notes->note_taker_name) ? $notes->note_taker_name = 'غير موجود' : null;
            $notes->create_date = date("d/m/Y h:ia", strtotime($notes->create_date));
        }

        //send data to the view
        $this->view->notes = $this->notes;
    }



    public function takeAction($note_id)
    {


        $sql = "UPDATE notes SET `status` = 'تم الاطلاع' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$note_id]);
        if($stmt->rowCount() == '1')
        {
            array_push($this->success, "تم تحديث حالة الملاحظة رقم #$note_id الي \"تم الاطلاع\" بنجاح");
        }
        else
        {
            array_push($this->errors, 'حدث خطأ ما');
        }
        
    }


    public function getUsers()
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() != '0')
        {
            $this->view->users = $stmt->fetchAll();
        }
        else
        {
            array_push($this->errors, 'لا يوجد مستخدمين لعرضهم');
        }
    }

    public function addNote($note, $note_taker_id)
    {
        $sql = "INSERT INTO notes (note, note_taker_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$note, $note_taker_id]);

        if($stmt->rowCount() != '0') {
            $this->success[] = "تم اضافة الملاحظة بنجاح";
        } else {
            $this->errors[] = "حدث خطأ ما";
        } 
        $this->redirect('notes', '1');
    }


    public function editNote($id, $note)
    {

        $sql = "UPDATE notes SET note = :note WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'note' => $note,
            
        ]);

        if ($stmt->rowCount() == '1') {
            $this->success[] = "تم تعديل الملاحظة بنجاح";
            $this->redirect('notes', '2');
        } else {
            $this->errors[] = "حدث خطأ ما";
        }
    }


    public function deleteNote($id)
    {
        $sql = "DELETE FROM notes WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() == '1') {
            $this->success[] = "تم حذف الملاحظة رقم #$id بنجاح";
            $this->redirect('notes', '2');
        } else {
            $this->errors[] = "حدث خطأ ما";   
        }
    }
}