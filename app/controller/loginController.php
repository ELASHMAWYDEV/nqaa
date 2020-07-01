<?php

class loginController extends Controller
{
    
    

    public function __construct()
    {
        parent::__construct();

        //check if logged in
        $this->loggedIn();

        //login on submitting form
        isset($_POST['user'],$_POST['pass']) ? $this->login($_POST['user'], $_POST['pass']) : null;
        
        
        //get the view page and pass all params
        $this->view('login');
        $this->view->title = "شركة نقاء | تسجيل الدخول";
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        
    }

    public function loggedIn()
    {
        if(isset($_SESSION['lvl']))
        {
            header('location: ' . ROOT_URL . 'stats');
        }
    }


    public function login($user, $pass)
    {
        $pass = md5($pass);
        if(filter_var($user, FILTER_VALIDATE_EMAIL))
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :user AND pass = :pass LIMIT 1");
        else
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :user AND pass = :pass LIMIT 1");

        $stmt->execute([
            'user' => $user,
            'pass' => $pass
        ]);
        if($stmt->rowCount() == '1') {
            $data = $stmt->fetch();
            $_SESSION['id'] = $data->id;
            $_SESSION['lvl'] = $data->lvl;
            $_SESSION['username'] = $data->username;
            $_SESSION['email'] = $data->email;
            $_SESSION['name'] = $data->name;
            $_SESSION['phone'] = $data->phone;
            $_SESSION['pass'] = $data->pass;
            $updateLogin = $this->db->prepare("UPDATE users SET login_date = now() WHERE id = " . $_SESSION['id'] . " LIMIT 1");
            $updateLogin->execute();
            array_push($this->success, 'تم تسجيل الدخول بنجاح');
            $this->redirect('stats', '1');
            
        }
        else
        {
            array_push($this->errors, 'هناك خطأ في كلمة المرور أو البريد الالكتروني');
        }
    }
}