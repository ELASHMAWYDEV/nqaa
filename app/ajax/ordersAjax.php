<?php

class ordersAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();


        isset($_POST['get_order_by_id']) ? $this->get_order_by_id($_POST['id']) : null;
        isset($_POST['get_orders']) ? $this->getOrders() : null;

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
                orders.region LIKE '%$region%' AND
                orders.type LIKE '%$type%' AND
                orders.technical LIKE '%$technical%' AND
                orders.status LIKE '%$status%' AND
                orders.date LIKE '%$date%'
                ORDER BY id DESC
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
            orders.status LIKE '%$status%' AND
            orders.date LIKE '%$date%'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;
        }
        $this->data->page_count = ceil($this->data->numOfResults / 10);
        $this->data->role = $_SESSION['lvl'];


        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
