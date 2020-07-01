<?php

class logoutController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_destroy();
        array_push($this->success, "تم تسجيل الخروج بنجاح");


        $this->view('login');
        $this->view->title = "جاري تسجيل الخروج";
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        $this->redirect('login', '2');
    }


}