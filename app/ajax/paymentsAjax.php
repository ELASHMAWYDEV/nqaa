<?php

class paymentsAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
        
        isset($_POST['get_payment_by_id']) ? $this->get_payment_by_id($_POST['id']) : null;


        //prevent users accessing this page manually
        if($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
        
    }



    public function get_payment_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM payments WHERE id = $id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() == '1') {
            $payment = $stmt->fetch();
            $this->output->cash = $payment->cash;
            $this->output->net = $payment->net;
            $this->output->payments = $payment->payments;
            $this->output->advance = $payment->advance;
            $this->data->advance_maker_id = $payment->advance_maker_id;


        } else {
            $this->data->errors[] = "لا يوجد مبلغ بهذا الرقم";
        }

        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}