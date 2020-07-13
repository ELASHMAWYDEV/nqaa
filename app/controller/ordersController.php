<?php

class ordersController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        isset($_POST['edit_order']) ? $this->editOrder($_POST['id']) : null;
        isset($_POST['add_order']) ? $this->addOrder() : null;
        isset($_POST['delete_order']) ? $this->deleteOrder($_POST['id']) : null;

        $this->loggedIn();
        $this->view('orders');
        $this->view->title = 'الطلبات';
        $this->getOrders();
        $this->getTechnicals();
        $this->getRegions();
        $this->getWorkHours();
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        $this->view->pagination();

    }



    public function getOrders()
    {
        $sql = "SELECT * FROM orders ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == '0') {
            $this->errors[] = 'لا يوجد طلبات لعرضها';
        } else {
            $this->view->orders = $stmt->fetchAll();

            //get technical phone
            $sql = "SELECT * FROM users WHERE lvl = 'فني'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $technicals = $stmt->fetchAll();

            //get regions
            $sql = "SELECT * FROM regions";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $regions = $stmt->fetchAll();


            foreach ($this->view->orders as $order) {
                $order->create_date = date("d/m/Y h:ia", strtotime($order->create_date));
                
                foreach ($technicals as $techy) {
                    if ($techy->id == $order->technical) {
                        $order->technical_phone = $techy->phone;
                        $order->technical = $techy->name;
                    }
                }

                foreach ($regions as $region) {
                    if ($region->id == $order->region) $order->region = $region->region;
                }
            }

        }
    }


    public function getTechnicals()
    {
        $sql = "SELECT * FROM users WHERE lvl = 'فني'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $this->view->technicals = $stmt->fetchAll();
    }

    public function getRegions()
    {
        $sql = "SELECT * FROM regions ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $this->view->regions = $stmt->fetchAll();
    }



    public function editOrder($id)
    {

        //get technical
        $sql = "SELECT * FROM orders WHERE id = '$id'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $technical = $stmt->fetch();

        //start
        if(empty($technical->technical) && empty($_POST['technical'])) {
            $sql = "UPDATE orders SET name = :name, phone = :phone, type = :type, region = :region, address = :address, date = :date, time = :time,
                    notes = :notes, money = :money, technical = :technical, details = :details, status = :status, invoice_num = :invoice_num, appointment_status = :appointment_status WHERE id = '$id'";
            $stmt = $this->db->prepare($sql);

        } else if (empty($technical->technical) && !empty($_POST['technical'])) {
            $sql = "UPDATE orders SET name = :name, phone = :phone, type = :type, region = :region, address = :address, date = :date, time = :time,
                    notes = :notes, money = :money, technical = :technical, details = :details, status = :status, invoice_num = :invoice_num, appointment_status = :appointment_status, technical_start = NOW() WHERE id = '$id'";
            $stmt = $this->db->prepare($sql);

        } else if (!empty($technical->technical) && !empty($_POST['status']) && $_POST['status'] == 'Finsih') {
            $sql = "UPDATE orders SET name = :name, phone = :phone, type = :type, region = :region, address = :address, date = :date, time = :time,
                    notes = :notes, money = :money, technical = :technical, details = :details, status = :status, invoice_num = :invoice_num, appointment_status = :appointment_status, technical_finish = NOW() WHERE id = '$id'";
            $stmt = $this->db->prepare($sql);

        } else if (!empty($technical->technical) && !empty($_POST['status']) && $_POST['status'] == 'Not Finish') {
            $sql = "UPDATE orders SET name = :name, phone = :phone, type = :type, region = :region, address = :address, date = :date, time = :time,
                    notes = :notes, money = :money, technical = :technical, details = :details, status = :status, invoice_num = :invoice_num, appointment_status = :appointment_status, technical_finish = NULL WHERE id = '$id'";
            $stmt = $this->db->prepare($sql);

        } else {
            $sql = "UPDATE orders SET name = :name, phone = :phone, type = :type, region = :region, address = :address, date = :date, time = :time,
                    notes = :notes, money = :money, technical = :technical, details = :details, status = :status, invoice_num = :invoice_num, appointment_status = :appointment_status WHERE id = '$id'";
            $stmt = $this->db->prepare($sql);
            
        }

        $stmt->execute([
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'phone' => $_POST['phone'],
            'region' => $_POST['region'],
            'address' => $_POST['address'],
            'date' => $_POST['date'],
            'time' => $_POST['time'],
            'notes' => $_POST['notes'],
            'money' => $_POST['money'],
            'technical' => $_POST['technical'],
            'details' => $_POST['details'],
            'status' => $_POST['status'],
            'invoice_num' => $_POST['invoice_num'],
            'appointment_status' => $_POST['appointment_status']
        ]);

        if ($stmt->rowCount() == '1') {
            $this->success[] = "تم تعديل الطلب رقم $id بنجاح";
            $this->redirect('orders', '3');
        }
        else $this->errors[] = "حدث خطأ ما أو انك لم تقم بأي تعديلات";
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






    public function addOrder()
    {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $type = $_POST['type'];
        $region = $_POST['region'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $address = $_POST['address'];
        $notes = $_POST['notes'];
        $money = $_POST['money'];
        $technical = $_POST['technical'];
        $details = $_POST['details'];
        $status = $_POST['status'];
        
        

        if(empty($technical)) {
            $sql = "INSERT INTO orders (name, phone, type, region, date, time, address, notes, money, technical, details, status) VALUES (:name, :phone, :type, :region, :date, :time, :address, :notes, :money, :technical, :details, :status)";
            $stmt = $this->db->prepare($sql);
        } else {
            $sql = "INSERT INTO orders (name, phone, type, region, date, time, address, notes, money, technical, details, status, technical_start) VALUES (:name, :phone, :type, :region, :date, :time, :address, :notes, :money, :technical, :details, :status, NOW())";
            $stmt = $this->db->prepare($sql);
        }


        //common errors
        if (empty($name)) $this->errors[] = 'الاسم مطلوب';
        if (empty($phone)) $this->errors[] = 'رقم الجوال مطلوب'; 
        if (empty($type)) $this->errors[] = 'يجب اختيار نوع الخدمة ، صيانة /تركيب'; 
        // if (empty($region)) $this->errors[] = 'يجب اختيار الحي / المنطقة';  
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
                'money' => $money,
                'technical' => $technical,
                'details' => $details,
                'status' => $status,
            ]);

            if ($stmt->rowCount() != '0') {
                
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

                $this->success[] = 'تم اضافة الطلب بنجاح';
                $this->redirect('orders', '3');
            } else {
                $this->errors[] = 'حدث خطأ ما ، يرجي التواصل مع الادارة';
            }
        }

    }





    public function deleteOrder($order_id)
    {
        $sql = "DELETE FROM orders WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);

        if($stmt->rowCount() != '0') {
            $this->success[] = "تم حذف الطلب بنجاح";
        } else {
            $this->errors[] = "حدث خطأ ما";
        } 
        $this->redirect('orders', '2');
    }
}