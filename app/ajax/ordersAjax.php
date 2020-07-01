<?php

class ordersAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
        

        isset($_POST['get_order_by_id']) ? $this->get_order_by_id($_POST['id']) : null;
        
        //prevent users from accessing this page manually
        if($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
        
    }


    public function get_order_by_id($id)
    {

        $sql = "SELECT * FROM orders WHERE id = $id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() == '1')
        {
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

            
        }
        else
        {
            $this->data->errors[] = "لا يوجد طلب بهذا الرقم #$id";
            $this->data->reload = 'true';
            
        }
        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}
