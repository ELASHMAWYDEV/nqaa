<?php

class optionsController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        //role
        !$this->role('مدير') ? header('location: stats') :null;

        isset($_POST['update_work_hours_1']) ? $this->updateWorkHours_1($_POST['start_1'], $_POST['finish_1']) : null;
        isset($_POST['update_work_hours_2']) ? $this->updateWorkHours_2($_POST['start_2'], $_POST['finish_2']) : null;
        isset($_POST['update_regular_months']) ? $this->updateRegularMonths($_POST['regular_months']) : null;
        
        $this->loggedIn();
        $this->view('options');
        $this->getWorkHours();
        $this->getRegularMonths();
        $this->view->title = "شركة نقاء | الإعدادات";
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
    }


    public function getWorkHours()
    {

        //1
        $sqlStart = "SELECT * FROM options WHERE `option` = 'start_hour_1' LIMIT 1";
        $sqlFinish = "SELECT * FROM options WHERE `option` = 'finish_hour_1' LIMIT 1";

        $stmtStart = $this->db->prepare($sqlStart);
        $stmtFinish = $this->db->prepare($sqlFinish);

        $stmtStart->execute();
        $stmtFinish->execute();

        $start = $stmtStart->fetch();
        $finish = $stmtFinish->fetch();

        $this->view->start_hour_1 = $start->value;
        $this->view->finish_hour_1 = $finish->value;

        //2
        $sqlStart = "SELECT * FROM options WHERE `option` = 'start_hour_2' LIMIT 1";
        $sqlFinish = "SELECT * FROM options WHERE `option` = 'finish_hour_2' LIMIT 1";

        $stmtStart = $this->db->prepare($sqlStart);
        $stmtFinish = $this->db->prepare($sqlFinish);

        $stmtStart->execute();
        $stmtFinish->execute();

        $start = $stmtStart->fetch();
        $finish = $stmtFinish->fetch();

        $this->view->start_hour_2 = $start->value;
        $this->view->finish_hour_2 = $finish->value;


    }

    public function updateWorkHours_1($start, $finish)
    {
        $sqlStart = "UPDATE options SET `value` = ? WHERE `option` = 'start_hour_1' LIMIT 1";
        $sqlFinish = "UPDATE options SET `value` = ? WHERE `option` = 'finish_hour_1' LIMIT 1";

        $stmtStart = $this->db->prepare($sqlStart);
        $stmtFinish = $this->db->prepare($sqlFinish);

        $stmtStart->execute([$start]);
        $stmtFinish->execute([$finish]);

        if ($stmtStart->rowCount() != '0' || $stmtFinish->rowCount() != '0') {
            $this->success[] = 'تم تحديث مواعيد العمل بنجاح';
        } else {
            $this->errors[] = 'لم تقم بإجراء أي تغيير';
        }
    }


    public function updateWorkHours_2($start, $finish)
    {
        $sqlStart = "UPDATE options SET `value` = ? WHERE `option` = 'start_hour_2' LIMIT 1";
        $sqlFinish = "UPDATE options SET `value` = ? WHERE `option` = 'finish_hour_2' LIMIT 1";

        $stmtStart = $this->db->prepare($sqlStart);
        $stmtFinish = $this->db->prepare($sqlFinish);

        $stmtStart->execute([$start]);
        $stmtFinish->execute([$finish]);

        if ($stmtStart->rowCount() != '0' || $stmtFinish->rowCount() != '0') {
            $this->success[] = 'تم تحديث مواعيد العمل بنجاح';
        } else {
            $this->errors[] = 'لم تقم بإجراء أي تغيير';
        }
    }


    public function getRegularMonths()
    {

        $sql = "SELECT * FROM options WHERE `option` = 'regular_months' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $months = $stmt->fetch()->value;

        $this->view->regular_months = $months;
    }
    


    public function updateRegularMonths($months)
    {

        $sql = "UPDATE options SET value = ? WHERE `option` = 'regular_months' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$months]);

        if ($stmt->rowCount() != '0') {
            $this->success[] = 'تم تحديث شهور الصيانة الدورية بنجاح';
        } else {
            $this->errors[] = 'لم تقم بإجراء أي تغيير';
        }
    }
}