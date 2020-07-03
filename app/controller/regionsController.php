<?php

class regionsController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        //role
        !$this->role('مدير') ? header('location: stats') :null;

        isset($_POST['add_region']) ? $this->addRegion($_POST['region']) : null;
        isset($_POST['delete_region']) ? $this->deleteRegion($_POST['id']) : null;
        isset($_POST['edit_region']) ? $this->editRegion($_POST['id'], $_POST['name']) : null;

        $this->loggedIn();
        $this->view('regions');
        $this->view->title = 'الأحياء / المناطق';
        $this->getRegions();
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        $this->view->pagination();
    }


    public function getRegions()
    {
        $sql = "SELECT * FROM regions ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() != '0') {
            $this->view->regions = $stmt->fetchAll();
        } else {
            $this->errors[] = 'لا يوجد أحياء لعرضها';
        }
    }
    

    public function addRegion($region)
    {
        //if region exists
        $sql = "SELECT * FROM regions WHERE region = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$region]);
        if ($stmt->rowCount() != '0') {
            $this->errors[] = "هذا الحي تم ادخاله من قبل ولا يمكن ادخاله مرة أخري";
            return; 
        }

        $sql = "INSERT INTO regions (region) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$region]);

        if ($stmt->rowCount() != '0') {
            $this->success[] = "تمت اضافة الحي : $region بنجاح";
        } else {
            $this->errors[] = 'حدث خطأ ما';
        }
    }


    public function deleteRegion($id)
    {
        $sql = "DELETE FROM regions WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() != '0') {
            $this->success[] = "تم حذف الحي رقم #$id بنجاح";
        } else {
            $this->errors[] = 'حدث خطأ ما';
        }
    }


    public function editRegion($id, $name)
    {

        $sql = "UPDATE regions SET region = :region WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'region' => $name
        ]);

        if ($stmt->rowCount() == '1') {
            $this->success[] = "تم تعديل اسم بنجاح";
            $this->redirect('regions', '2');
        } else {
            $this->errors[] = "حدث خطأ ما";
        }

    }


}