<?php

class ordersAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();


        isset($_POST['get_order_by_id']) ? $this->get_order_by_id($_POST['id']) : null;
        isset($_POST['get_orders']) ? $this->getOrders() : null;
        isset($_POST['print_orders']) ? $this->printOrders() : null;

        //prevent users from accessing this page manually
        if ($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
    }


    public function get_order_by_id($id)
    {

        $sql = "SELECT * FROM orders WHERE id = $id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() == '1') {
            $order = $stmt->fetch();
            $this->output->name = $order->name ? $order->name : '';
            $this->output->phone = $order->phone ? $order->phone : '';
            $this->data->type = $order->type ? $order->type : '';
            $this->data->region = $order->region ? $order->region : '';
            $this->data->date = $order->date ? $order->date : '';
            $this->data->time = $order->time ? $order->time : '';
            $this->output->address = $order->address ? $order->address : '';
            $this->data->notes = $order->notes ? $order->notes : '';
            $this->output->money = $order->money ? $order->money : '';
            $this->data->status = $order->status ? $order->status : '';
            $this->data->technical = $order->technical ? $order->technical : '';
            $this->data->details = $order->details ? $order->details : '';
            $this->output->invoice_num = $order->invoice_num ? $order->invoice_num : '';
            $this->data->appointment_status = $order->appointment_status ? $order->appointment_status : '';
        } else {
            $this->data->errors[] = "لا يوجد طلب بهذا الرقم #$id";
            $this->data->reload = 'true';
        }
        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function getOrders()
    {
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $id = isset($_POST['id']) ? $_POST['id'] : "";
        $name = isset($_POST['name']) ? $_POST['name'] : "";
        $phone = isset($_POST['phone']) ? $_POST['phone'] : "";
        $region = isset($_POST['region']) ? $_POST['region'] : "";
        $type = isset($_POST['type']) ? $_POST['type'] : "";
        $technical = isset($_POST['technical']) ? $_POST['technical'] : "";
        $status = isset($_POST['status']) ? $_POST['status'] : "";
        $date = isset($_POST['date']) ? $_POST['date'] : "";
        $appointment_status = isset($_POST['appointment_status']) ? $_POST['appointment_status'] : "";
        $invoice_num = isset($_POST['invoice_num']) ? $_POST['invoice_num'] : "";

        $start = ($page - 1) * 10;

        $this->data->start = $start;
        $sql = "SELECT orders.*, users.phone AS technical_phone, users.name AS technical, regions.region AS region
                FROM orders
                LEFT JOIN users ON orders.technical = users.id 
                LEFT JOIN regions ON orders.region = regions.id
                WHERE 
                orders.id LIKE '%$id%' AND 
                orders.name LIKE '%$name%' AND 
                orders.phone LIKE '%$phone%' AND 
                regions.region LIKE '%$region%' AND
                orders.type LIKE '%$type%' AND
                orders.technical LIKE '%$technical%' AND
                orders.status LIKE '$status%' AND
                orders.date LIKE '%$date%' AND
                orders.appointment_status LIKE '%$appointment_status%' " .
            (!empty($invoice_num) ? "AND orders.invoice_num LIKE '%$invoice_num%' " : "") .
            "ORDER BY id DESC
                LIMIT $start, 10";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->data->orders = [];
        $this->data->numOfResults = 0;


        if ($stmt->rowCount() == '0') {
            $this->data->errors[] = 'لا يوجد طلبات لعرضها';
        } else {
            $this->data->orders = $stmt->fetchAll();

            foreach ($this->data->orders as $order) {
                $order->create_date = date("d/m/Y h:ia", strtotime($order->create_date));
            }

            //Get the total count
            $sql = "SELECT COUNT(*) AS numOfResults FROM orders  WHERE 
            orders.id LIKE '%$id%' AND 
            orders.name LIKE '%$name%' AND 
            orders.phone LIKE '%$phone%' AND 
            orders.region LIKE '%$region%' AND
            orders.type LIKE '%$type%' AND
            orders.technical LIKE '%$technical%' AND
            orders.status LIKE '$status%' AND
            orders.date LIKE '%$date%' AND
            orders.appointment_status LIKE '%$appointment_status%' AND
            orders.invoice_num LIKE '%$invoice_num%'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;
        }
        $this->data->page_count = ceil($this->data->numOfResults / 10);
        $this->data->role = $_SESSION['lvl'];


        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function printOrders()
    {

        //Set header information to export data in excel format
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=orders_' . date("d_m_Y") . ".csv");

        $output = fopen("php://output", "w");
        //Get the start & end date
        $start = isset($_POST['start_date']) && $_POST['start_date'] != "null" ? $_POST['start_date'] : date("d/m/Y");
        $end = isset($_POST['end_date']) && $_POST['end_date'] != "null" ? $_POST['end_date'] : date("d/m/Y");

        $sql = "SELECT orders.*, users.phone AS technical_phone, users.name AS technical, regions.region AS region
                FROM orders
                LEFT JOIN users ON orders.technical = users.id 
                LEFT JOIN regions ON orders.region = regions.id
                ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == '0') {
            $this->errors[] = 'لا يوجد طلبات لعرضها';
        } else {
            $orders = $stmt->fetchAll();

            //Print Heading
            fputcsv($output, [
                "#", "الاسم", "الجوال", "نوع الطلب", "الحي / المنطقة", "العنوان بالتفصيل",
                "التاريخ", "الوقت", "ملاحظات العميل", "حالة الموعد", "المبلغ", "الفني المسؤول",
                "تفاصيل الطلب", "الحالة",
            ]);

            $finalOrders = [];

            foreach ($orders as $order) {
                $order->create_date = date("d/m/Y h:ia", strtotime($order->create_date));

                $dateArray = explode("/", $order->date);
                $startDateArray = explode("/", $start);
                $endDateArray = explode("/", $end);

                if (
                    mktime(0, 0, 0, $startDateArray[1], $startDateArray[0], $startDateArray[2])
                    <= mktime(0, 0, 0, $dateArray[1], $dateArray[0], $dateArray[2])
                    &&
                    mktime(0, 0, 0, $endDateArray[1], $endDateArray[0], $endDateArray[2])
                    >= mktime(0, 0, 0, $dateArray[1], $dateArray[0], $dateArray[2])
                ) {
                    $finalOrders[] = $order;
                }
            }

            foreach ($finalOrders as $order) {
                fputcsv($output, [
                    $order->id, $order->name, $order->phone, $order->type, $order->region, $order->address,
                    $order->date, $order->time, $order->notes, $order->appointment_status, $order->money, $order->technical,
                    $order->details, $order->status
                ]);
            }
        }

        fclose($output);
    }
}
