<?php

class paymentsController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        //role
        $this->role('فني') ? header('location: stats') :null;

        isset($_POST['add_payment']) ? $this->addPayment($_POST['cash'], $_POST['net'], $_POST['payments'], $_POST['advance'], $_POST['advance_maker_id']) : null;
        isset($_POST['delete_payment']) ? $this->deletePayment($_POST['id']) : null;

        $this->loggedIn();
        $this->view('payments');
        $this->view->title = "شركة نقاء | المبلغ اليومي";
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
     

        //payments
        $sql = "SELECT * FROM payments ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() != '0') {

            $payments = $stmt->fetchAll();
            foreach ($payments as $payment) {

                $payment->create_date = date("d/m/Y h:ia", strtotime($payment->create_date));
                $payment->total = ( $payment->cash + $payment->net ) - ( $payment->payments + $payment->advance );

                if ($payment->advance != 0 && !empty($payment->advance)) {

                    foreach ( $this->getUsers() as $user ) {
                        
                        if ($payment->advance_maker_id == $user->id) {

                            $payment->advance = $payment->advance . ' ← ' . $user->name;
                        break;

                        }
                    }

                }

            }

            $this->view->payments = $payments;

        } else {
            $this->view->payments = [];
            $this->errors[] = 'لا يوجد مبالغ يومية لعرضها';
        }


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

        if ( is_numeric($cash) && is_numeric($net) && is_numeric($payments) && is_numeric($advance)) {
        
            $sql = "INSERT INTO payments (cash, net, payments, advance, advance_maker_id) VALUES ( ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([number_format($cash,0,'.',''), number_format($net,0,'.',''), number_format($payments,0,'.',''), number_format($advance,0,'.',''), $id]);


            if ( $stmt->rowCount() == '1') {
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


}