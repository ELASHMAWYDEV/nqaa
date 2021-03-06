<?php

class paymentsController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        //role
        $this->role('فني') ? header('location: stats') : null;

        isset($_POST['add_payment']) ? $this->addPayment($_POST['cash'], $_POST['net'], $_POST['payments'], $_POST['advance'], $_POST['advance_maker_id']) : null;
        isset($_POST['delete_payment']) ? $this->deletePayment($_POST['id']) : null;
        isset($_POST['edit_payment']) ? $this->editPayment($_POST['id']) : null;


        $this->loggedIn();
        $this->view('payments');
        $this->view->title = "مؤسسة نقاء | المبلغ اليومي";
        $this->getPayemnts();
        $this->getUsers();
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        $this->view->pagination(10);
    }



    public function getPayemnts()
    {


        $limit =  $this->role('مشرف') ? 5 : 10;
        //payments
        $sql = "SELECT payments.*, users.name FROM payments
                LEFT JOIN users ON payments.advance_maker_id = users.id ORDER BY id DESC LIMIT 0, $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->view->payments = [];

        if ($stmt->rowCount() != '0') {

            $payments = $stmt->fetchAll();
            foreach ($payments as $payment) {

                $payment->create_date = date("d/m/Y h:ia", strtotime($payment->create_date));
                $payment->total = ($payment->cash + $payment->net) - ($payment->payments + $payment->advance);

                if ($payment->advance != 0 && !empty($payment->advance)) {
                    $payment->advance = $payment->advance . ' ← ' . $payment->name;
                }
            }
            $this->view->payments = $payments;
        } else {
            $this->errors[] = 'لا يوجد مبالغ يومية لعرضها';
        }


        //Get the total count
        $sql = "SELECT COUNT(*) AS numOfResults FROM payments";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->view->numOfResults = $stmt->fetchAll()[0]->numOfResults;

        $this->role('مشرف') && $this->view->numOfResults = 5;
    }


    public function getUsers()
    {
        $Sql = "SELECT * FROM users";
        $stmt = $this->db->prepare($Sql);
        $stmt->execute();
        $users = $stmt->fetchAll();

        $this->view->users = $users;

        return $users;
    }


    public function addPayment($cash, $net, $payments, $advance, $id)
    {

        if (is_numeric($cash) && is_numeric($net) && is_numeric($payments) && is_numeric($advance)) {

            $sql = "INSERT INTO payments (cash, net, payments, advance, advance_maker_id) VALUES ( ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([number_format($cash, 0, '.', ''), number_format($net, 0, '.', ''), number_format($payments, 0, '.', ''), number_format($advance, 0, '.', ''), $id]);


            if ($stmt->rowCount() == '1') {
                $this->success[] = 'تم حفظ المبلغ بنجاح';
                $this->redirect('payments', '2');
            } else {
                $this->errors[] = 'حدث خطأ ما';
            }
        } else {

            $this->errors[] = 'يجب أن تكون جميع المدخلات أرقام انجليزية';
        }
    }


    public function deletePayment($id)
    {

        $sql = "DELETE FROM payments WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() != '0') {
            $this->success[] = "تم حذف المبلغ رقم #$id بنجاح";
            $this->redirect('payments', '2');
        } else {
            $this->errors[] = "حدث خطأ ما";
        }
    }


    public function editPayment($id)
    {

        $sql = "UPDATE payments SET cash =  :cash, net = :net, payments = :payments, advance = :advance, advance_maker_id = :advance_maker_id WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'cash' => $_POST['cash'],
            'net' => $_POST['net'],
            'payments' => $_POST['payments'],
            'advance' => $_POST['advance'],
            'advance_maker_id' => $_POST['advance_maker_id']

        ]);

        if ($stmt->rowCount() == '1') {
            $this->success[] = "تم تعديل المبلغ بنجاح";
            $this->redirect('payments', '2');
        } else {
            $this->errors[] = "حدث خطأ ما";
        }
    }
}
