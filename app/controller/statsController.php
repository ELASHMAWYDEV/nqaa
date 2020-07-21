<?php

class statsController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->loggedIn();
        $this->view('stats');
        $this->getTechnicals();
        $this->getOrdersStats();

        //احصائيات الفني
        if($this->role('فني')) {
            $this->getTechnicalStats($_SESSION['id']);
        } else {
            isset($_POST['technical_stats']) ? $this->getTechnicalStats($_POST['technical']) : $this->getTechnicalStats();
        }


        isset($_POST['payment_stats']) ? $this->getPaymentsStats($_POST['period']) : $this->getPaymentsStats();

        $this->view->title = "شركة نقاء | الاحصائيات";
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();


    }



    public function getTechnicals()
    {
        $sql = "SELECT * FROM users WHERE lvl = 'فني'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $this->view->technicals = $stmt->fetchAll();
    }

    


    public function getOrdersStats()
    {



        $this->view->today1_finish = $this->view->today2_finish = 
        $this->view->today1_nonfinish = $this->view->today2_nonfinish =
        $this->view->yesterday1_finish = $this->view->yesterday2_finish = 
        $this->view->yesterday1_nonfinish = $this->view->yesterday2_nonfinish =
        $this->view->thismonth1_finish = $this->view->thismonth2_finish =
        $this->view->thismonth1_nonfinish = $this->view->thismonth2_nonfinish =
        $this->view->lastmonth1_finish = $this->view->lastmonth2_finish = 
        $this->view->lastmonth1_nonfinish = $this->view->lastmonth2_nonfinish = 0;



        $sql = "SELECT * FROM orders";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == '0') {
            $this->errors[] = 'لا يوجد طلبات لعرض الاحصائيات الخاصة بها';
            
        } else {
            $orders = $stmt->fetchAll();

            foreach($orders as $order) {

                //1 => تركيب
                //2 => صيانة

                ////Today
                //today1_finish
                if (strtotime($order->create_date) > strtotime("today") && $order->status == 'Finish' && $order->type == 'تركيب') {
                    $this->view->today1_finish += 1;   
                }

                //today1_finish
                if (strtotime($order->create_date) > strtotime("today") && $order->status == 'Finish' && $order->type == 'صيانة') {
                    $this->view->today2_finish += 1;   
                }
                
                //today1_nonfinish
                if (strtotime($order->create_date) > strtotime("today") && $order->status == 'Not Finish' && $order->type == 'تركيب') {
                    $this->view->today1_nonfinish += 1;   
                }

                //today1_nonfinish
                if (strtotime($order->create_date) > strtotime("today") && $order->status == 'Not Finish' && $order->type == 'صيانة') {
                    $this->view->today2_nonfinish += 1;   
                }

                ////Yesterday

                //yesterday1_finish
                if (strtotime($order->create_date) > strtotime("yesterday") && strtotime($order->create_date) < strtotime("today") && $order->status == 'Finish' && $order->type == 'تركيب') {
                    $this->view->yesterday1_finish += 1;   
                }

                //yesterday2_finish
                if (strtotime($order->create_date) > strtotime("yesterday") && strtotime($order->create_date) < strtotime("today") && $order->status == 'Finish' && $order->type == 'صيانة') {
                    $this->view->yesterday2_finish += 1;   
                }
                
                //yesterday1_nonfinish
                if (strtotime($order->create_date) > strtotime("yesterday") && strtotime($order->create_date) < strtotime("today") && $order->status == 'Not Finish' && $order->type == 'تركيب') {
                    $this->view->yesterday1_nonfinish += 1;   
                }

                //yesterday2_nonfinish
                if (strtotime($order->create_date) > strtotime("yesterday") && strtotime($order->create_date) < strtotime("today") && $order->status == 'Not Finish' && $order->type == 'صيانة') {
                    $this->view->yesterday2_nonfinish += 1;   
                }

                /*---------Months---------*/


                ////thismonth
                //thismonth1_finish
                if (strtotime($order->create_date) > strtotime("first day of this month") && $order->status == 'Finish' && $order->type == 'تركيب') {
                    $this->view->thismonth1_finish += 1;   
                }

                //thismonth1_finish
                if (strtotime($order->create_date) > strtotime("first day of this month") && $order->status == 'Finish' && $order->type == 'صيانة') {
                    $this->view->thismonth2_finish += 1;   
                }
                
                //thismonth1_nonfinish
                if (strtotime($order->create_date) >= strtotime("first day of this month") && $order->status == 'Not Finish' && $order->type == 'تركيب') {
                    $this->view->thismonth1_nonfinish += 1;   
                }

                //thismonth1_nonfinish
                if (strtotime($order->create_date) >= strtotime("first day of this month") && $order->status == 'Not Finish' && $order->type == 'صيانة') {
                    $this->view->thismonth2_nonfinish += 1;   
                }

                ////lastmonth

                //lastmonth1_finish
                if (strtotime($order->create_date) > strtotime("first day of last month") && strtotime($order->create_date) < strtotime("first day of this month") && $order->status == 'Finish' && $order->type == 'تركيب') {
                    $this->view->lastmonth1_finish += 1;   
                }

                //lastmonth2_finish
                if (strtotime($order->create_date) > strtotime("first day of last month") && strtotime($order->create_date) < strtotime("first day of this month") && $order->status == 'Finish' && $order->type == 'صيانة') {
                    $this->view->lastmonth2_finish += 1;   
                }
                
                //lastmonth1_nonfinish
                if (strtotime($order->create_date) > strtotime("first day of last month") && strtotime($order->create_date) < strtotime("first day of this month") && $order->status == 'Not Finish' && $order->type == 'تركيب') {
                    $this->view->lastmonth1_nonfinish += 1;   
                }

                //lastmonth2_nonfinish
                if (strtotime($order->create_date) > strtotime("first day of last month") && strtotime($order->create_date) < strtotime("first day of this month") && $order->status == 'Not Finish' && $order->type == 'صيانة') {
                    $this->view->lastmonth2_nonfinish += 1;   
                }
            }
        }
    }

    public function getTechnicalStats($technical = '')
    {
        
        $this->view->technical = $technical; 

        $this->view->techy_today1_finish = $this->view->techy_today2_finish = 
        $this->view->techy_today1_nonfinish = $this->view->techy_today2_nonfinish =
        $this->view->techy_yesterday1_finish = $this->view->techy_yesterday2_finish = 
        $this->view->techy_yesterday1_nonfinish = $this->view->techy_yesterday2_nonfinish =
        $this->view->techy_thismonth1_finish = $this->view->techy_thismonth2_finish =
        $this->view->techy_thismonth1_nonfinish = $this->view->techy_thismonth2_nonfinish =
        $this->view->techy_lastmonth1_finish = $this->view->techy_lastmonth2_finish = 
        $this->view->techy_lastmonth1_nonfinish = $this->view->techy_lastmonth2_nonfinish = 0;

        $sql = "SELECT * FROM orders WHERE technical = '$technical'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == '0') {
            $this->errors[] = 'لا يوجد طلبات خاصة بالفني المختار لعرض الاحصائيات الخاصة به';
        } else {
            $orders = $stmt->fetchAll();


            foreach($orders as $order) {

                //1 => تركيب
                //2 => صيانة

                ////Today
                //today1_finish
                if (strtotime($order->create_date) > strtotime("today") && $order->status == 'Finish' && $order->type == 'تركيب') {
                    $this->view->techy_today1_finish += 1;   
                }

                //today1_finish
                if (strtotime($order->create_date) > strtotime("today") && $order->status == 'Finish' && $order->type == 'صيانة') {
                    $this->view->techy_today2_finish += 1;   
                }
                
                //today1_nonfinish
                if (strtotime($order->create_date) > strtotime("today") && $order->status == 'Not Finish' && $order->type == 'تركيب') {
                    $this->view->techy_today1_nonfinish += 1;   
                }

                //today1_nonfinish
                if (strtotime($order->create_date) > strtotime("today") && $order->status == 'Not Finish' && $order->type == 'صيانة') {
                    $this->view->techy_today2_nonfinish += 1;   
                }

                ////Yesterday

                //yesterday1_finish
                if (strtotime($order->create_date) > strtotime("yesterday") && $order->status == 'Finish' && $order->type == 'تركيب') {
                    $this->view->techy_yesterday1_finish += 1;   
                }

                //yesterday2_finish
                if (strtotime($order->create_date) > strtotime("yesterday") && $order->status == 'Finish' && $order->type == 'صيانة') {
                    $this->view->techy_yesterday2_finish += 1;   
                }
                
                //yesterday1_nonfinish
                if (strtotime($order->create_date) > strtotime("yesterday") && $order->status == 'Not Finish' && $order->type == 'تركيب') {
                    $this->view->techy_yesterday1_nonfinish += 1;   
                }

                //yesterday2_nonfinish
                if (strtotime($order->create_date) > strtotime("yesterday") && $order->status == 'Not Finish' && $order->type == 'صيانة') {
                    $this->view->techy_yesterday2_nonfinish += 1;   
                }

                /*---------Months---------*/


                ////thismonth
                //thismonth1_finish
                if (strtotime($order->create_date) > strtotime("first day of this month") && $order->status == 'Finish' && $order->type == 'تركيب') {
                    $this->view->techy_thismonth1_finish += 1;   
                }

                //thismonth1_finish
                if (strtotime($order->create_date) > strtotime("first day of this month") && $order->status == 'Finish' && $order->type == 'صيانة') {
                    $this->view->techy_thismonth2_finish += 1;   
                }
                
                //thismonth1_nonfinish
                if (strtotime($order->create_date) > strtotime("first day of this month") && $order->status == 'Not Finish' && $order->type == 'تركيب') {
                    $this->view->techy_thismonth1_nonfinish += 1;   
                }

                //thismonth1_nonfinish
                if (strtotime($order->create_date) > strtotime("first day of this month") && $order->status == 'Not Finish' && $order->type == 'صيانة') {
                    $this->view->techy_thismonth2_nonfinish += 1;   
                }

                ////lastmonth

                //lastmonth1_finish
                if (strtotime($order->create_date) > strtotime("first day of last month") && strtotime($order->create_date) < strtotime("first day of this month") && $order->status == 'Finish' && $order->type == 'تركيب') {
                    $this->view->techy_lastmonth1_finish += 1;   
                }

                //lastmonth2_finish
                if (strtotime($order->create_date) > strtotime("first day of last month") && strtotime($order->create_date) < strtotime("first day of this month") && $order->status == 'Finish' && $order->type == 'صيانة') {
                    $this->view->techy_lastmonth2_finish += 1;   
                }
                
                //lastmonth1_nonfinish
                if (strtotime($order->create_date) > strtotime("first day of last month") && strtotime($order->create_date) < strtotime("first day of this month") && $order->status == 'Not Finish' && $order->type == 'تركيب') {
                    $this->view->techy_lastmonth1_nonfinish += 1;   
                }

                //lastmonth2_nonfinish
                if (strtotime($order->create_date) > strtotime("first day of last month") && strtotime($order->create_date) < strtotime("first day of this month") && $order->status == 'Not Finish' && $order->type == 'صيانة') {
                    $this->view->techy_lastmonth2_nonfinish += 1;   
                }
            }
        }
    }



    public function getPaymentsStats($period = 'today')
    {
        $this->view->period = $period;

        $this->view->cash = $this->view->net =
        $this->view->payments = $this->view->advance = 
        $this->view->total = 0;

        $sql = "SELECT * FROM payments";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $payments = $stmt->fetchAll();

        switch($period) {
            case 'today':
                foreach($payments as $payment) {
                    if (strtotime($payment->create_date) > strtotime("today")) {
                        $this->view->cash += $payment->cash;
                        $this->view->net += $payment->net;                        
                        $this->view->payments += $payment->payments;
                        $this->view->advance += $payment->advance;
                        $this->view->total += ( $payment->cash + $payment->net ) - ( $payment->payments + $payment->advance );

                    }
                }
            break;
            case 'yesterday':
                foreach($payments as $payment) {
                    if (strtotime($payment->create_date) > strtotime("yesterday") && strtotime($payment->create_date) < strtotime("today")) {
                        $this->view->cash += $payment->cash;
                        $this->view->net += $payment->net;                        
                        $this->view->payments += $payment->payments;
                        $this->view->advance += $payment->advance;
                        $this->view->total += ( $payment->cash + $payment->net ) - ( $payment->payments + $payment->advance );

                    }
                }
            break;
            case 'this_month':
                foreach($payments as $payment) {
                    if (strtotime($payment->create_date) > strtotime("first day of this month")) {
                        $this->view->cash += $payment->cash;
                        $this->view->net += $payment->net;                        
                        $this->view->payments += $payment->payments;
                        $this->view->advance += $payment->advance;
                        $this->view->total += ( $payment->cash + $payment->net ) - ( $payment->payments + $payment->advance );

                    }
                }
            break;
            case 'last_month':
                foreach($payments as $payment) {
                    if (strtotime($payment->create_date) > strtotime("first day of last month") && strtotime($payment->create_date) < strtotime("first day of this month")) {
                        $this->view->cash += $payment->cash;
                        $this->view->net += $payment->net;                        
                        $this->view->payments += $payment->payments;
                        $this->view->advance += $payment->advance;
                        $this->view->total += ( $payment->cash + $payment->net ) - ( $payment->payments + $payment->advance );

                    }
                }
                
        }
    }

}