<?php

class formController extends Controller
{
    public function __construct()
    {
        parent::__construct();


        isset($_POST['submit']) ? $this->submitOrder() : null;

        $this->view('form');
        $this->getWorkHours();
        $this->getRegions();
        $this->view->renderFile();
        $this->view->renderFooter();
        $this->view->viewMessages($this->errors, $this->success);

    }


    public function submitOrder()
    {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $type = $_POST['type'];
        $region = $_POST['region'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $address = $_POST['address'];
        $notes = $_POST['notes'];
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];

        $sql = "INSERT INTO orders (name, phone, type, region, date, time, address, notes, `status`, map_lat, map_lng) VALUES (:name, :phone, :type, :region, :date, :time, :address, :notes, 'Not Finish', :map_lat, :map_lng)";
        $stmt = $this->db->prepare($sql);


        //common errors
        if (empty($name)) $this->errors[] = 'الاسم مطلوب';
        if (empty($phone)) $this->errors[] = 'رقم الجوال مطلوب'; 
        if (empty($type)) $this->errors[] = 'يجب اختيار نوع الخدمة ، صيانة /تركيب'; 
        if (empty($region)) $this->errors[] = 'يجب اختيار الحي / المنطقة';  
        if (empty($date)) $this->errors[] = 'يجب اختيار تاريخ الطلب';  
        if (empty($time)) $this->errors[] = 'يجب اختيار وقت الطلب';  
        if (empty($address)) $this->errors[] = 'العنوان مطلوب';  


        if (count($this->errors) == 0) {

            $stmt->execute([
                'name' => $name,
                'phone' => $phone,
                'type' => $type,
                'region' => $region,
                'date' => $date,
                'time' => $time,
                'address' => $address,
                'notes' => $notes,
                'map_lat' => $lat, 
                'map_lng' => $lng
            ]);

            if ($stmt->rowCount() != '0') {

                //add regular order
                $order_id = $this->db->lastInsertId();

                //add to regular orders

                //get the months
                $sql = "SELECT * FROM options WHERE `option` = 'regular_months' LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $months = $stmt->fetch()->value;
                $next_date = date("Y-m-d H:i:s", strtotime("+$months month", time()));

                $sql = "INSERT INTO regular (name, phone, region, address, next_date, old_order_id) VALUES (:name, :phone, :region, :address, :next_date, :old_order_id)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'name' => $name,
                    'phone' => $phone,
                    'region' => $region,
                    'phone' => $phone,
                    'address' => $address,
                    'next_date' => $next_date,
                    'old_order_id' => $order_id
                ]);


                $this->success[] = 'تم ارسال طلبك بنجاح وسوف يتواصل معك أحد موظفينا قريبا';
                $this->redirect('form', '3');
            } else {
                $this->errors[] = 'حدث خطأ ما ، يرجي التواصل مع الادارة';
            }
        }

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


    public function getRegions()
    {
        $sql = "SELECT * FROM regions ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $this->view->regions = $stmt->fetchAll();
    }

}