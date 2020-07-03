<?php

class regularController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        //role
        $this->role('فني') ? header('location: stats') : null;

        isset($_POST['id']) ? $this->takeAction($_POST['id']) : null;
        isset($_POST['delete_regular']) ? $this->deleteRegular($_POST['id']) : null;

        $this->loggedIn();
        $this->view('regular');
        $this->view->title = 'الصيانة الدورية';
        $this->getRegular();
        $this->getRegions();
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        $this->view->pagination();
    }



    public function getRegular()
    {

        $sql = "SELECT * FROM regular ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $regulars = $stmt->fetchAll();
        
        if ($stmt->rowCount() == '0') {
            $this->errors[] = "لا يوجد طلبات صيانة دورية لعرضها";
        } else {
            
            foreach ($regulars as $regular) {
                $regular->next_date = date("d/m/Y", strtotime($regular->next_date));
            }
            $this->view->regulars = $regulars;
        }
    }


    public function getRegions()
    {
        $sql = "SELECT * FROM regions ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $this->view->regions = $stmt->fetchAll();
    }

    public function takeAction($id)
    {

        $sql = "UPDATE regular SET `action` = 'تم انشاء طلب للعميل' WHERE id = '$id' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == '1') {
            $this->success[] = "تم تحديث حالة الطلب بنجاح";
            $this->redirect('regular', '2');
        } else {
            $this->errors[] = "حدث خطأ ما";
        }
    }



    public function deleteRegular($regular_id)
    {
        $sql = "DELETE FROM regular WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$regular_id]);

        if($stmt->rowCount() != '0') {
            $this->success[] = "تم حذف طلب الصيانة الدورية بنجاح";
        } else {
            $this->errors[] = "حدث خطأ ما";
        } 
        $this->redirect('regular', '1');
    }

}