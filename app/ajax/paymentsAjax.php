<?php

class paymentsAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();

        isset($_POST['get_payment_by_id']) ? $this->get_payment_by_id($_POST['id']) : null;
        isset($_POST['get_payments']) ? $this->getPayments() : null;


        //prevent users accessing this page manually
        if ($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
    }



    public function get_payment_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM payments WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        if ($stmt->rowCount() == '1') {
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


    public function getPayments()
    {

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $advance_maker_id = isset($_POST['advance_maker_id']) ? $_POST['advance_maker_id'] : "";
        $create_date = !empty($_POST['create_date']) ? date("Y-m-d", mktime(0, 0, 0, explode("/", $_POST['create_date'])[1], explode("/", $_POST['create_date'])[0], explode("/", $_POST['create_date'])[2])) : "";

        $start = ($page - 1) * 10;

        //payments
        $sql = "SELECT payments.*, users.name FROM payments
                LEFT JOIN users ON payments.advance_maker_id = users.id 
                WHERE 
                payments.advance_maker_id LIKE '%$advance_maker_id%' AND 
                payments.create_date LIKE '%$create_date%'
                ORDER BY id DESC 
                LIMIT $start, 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->data->payments = [];

        if ($stmt->rowCount() != '0') {

            $payments = $stmt->fetchAll();
            foreach ($payments as $payment) {

                $payment->create_date = date("d/m/Y h:ia", strtotime($payment->create_date));
                $payment->total = ($payment->cash + $payment->net) - ($payment->payments + $payment->advance);

                if ($payment->advance != 0 && !empty($payment->advance)) {
                    $payment->advance = $payment->advance . ' ← ' . $payment->name;
                }
            }
            $this->data->payments = $payments;
        } else {
            $this->data->errors[] = 'لا يوجد مبالغ يومية لعرضها';
        }


        //Get the total count
        $sql = "SELECT COUNT(*) AS numOfResults FROM payments 
                WHERE 
                payments.advance_maker_id LIKE '%$advance_maker_id%' AND 
                payments.create_date LIKE '%$create_date%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;

        $this->data->page_count = ceil($this->data->numOfResults / 10);
        $this->data->role = $_SESSION['lvl'];

        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
