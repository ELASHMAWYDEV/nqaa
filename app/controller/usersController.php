<?php

class usersController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        //role
        !$this->role('مدير') ? header('location: stats') :null;

        isset($_POST['add_new_user']) ? $this->add_new_user() : null;
        isset($_POST['change_user_password']) ? $this->change_user_password($_POST['id'], $_POST['pass1'], $_POST['pass2']) : null;



        $this->loggedIn();
        $this->view('users');
        $this->view->title = "شركة نقاء | المستخدمين";
        $this->getUsers();
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        $this->view->pagination();


    }

    public function getUsers()
    {
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() != '0')
        {
            $users = $stmt->fetchAll();

            foreach ($users as $user) {
                !empty($user->login_date) ? $user->login_date = date("d/m/Y h:ia", strtotime($user->login_date)) : null;
            }

            $this->view->data = $users;
        }
        else
        {
            array_push($this->errors, 'لا يوجد مستخدمين لعرضهم');
        }
    }



    public function add_new_user()
    {
        $lvl = $_POST['lvl'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $name = $_POST['name'];
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];


        
        //Common Errors
        if(empty($lvl)) $this->errors[] = 'يجب اختيار المستوي';
        if(empty($username)) $this->errors[] = 'يجب كتابة اسم المستخدم';
        if(empty($email)) $this->errors[] = 'يجب كتابة البريد الالكتروني';
        if(empty($phone)) $this->errors[] = 'يجب كتابة رقم الهاتف';
        if(empty($name)) $this->errors[] = 'يجب كتابة الاسم';
        if(empty($pass1)) $this->errors[] = 'يجب كتابة كلمة المرور';
        if(empty($pass2)) $this->errors[] = 'يجب تأكيد كلمة المرور';
        if(!empty($pass2) && !empty($pass1) && $pass1 != $pass2) $this->errors[] = 'يجب أن تكون كلمتي المرور متطابقتين';
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $this->data->errors[] = 'هذا البريد الالكتروني غير صالح';
  
        //check if username or email exist in Database
        $emailCheck = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $emailCheck = $this->db->prepare($emailCheck);
        $emailCheck->execute([$email]);
        if($emailCheck->rowCount() != '0') {
            $this->errors[] = 'البريد الالكتروني مسجل من قبل، يرجي استخدام بريد الكتروني مختلف';
        }

        $usernameCheck = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $usernameCheck = $this->db->prepare($usernameCheck);
        $usernameCheck->execute([$username]);
        if($usernameCheck->rowCount() != '0') {
            $this->errors[] = 'اسم المستخدم مسجل من قبل، يرجي استخدام اسم مستخدم مختلف';
        }


        if (count($this->errors) == 0)
        {
            $sql = "INSERT INTO users (username,email,`name`,phone,pass,lvl) VALUES (:username,:email,:name,:phone,:pass,:lvl)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'name' => $name,
                'phone' => $phone,
                'pass' => md5($pass1),
                'lvl' => $lvl
            ]);
            $this->success[] = 'تم اضافة المستخدم الجديد بنجاح';
            $this->redirect('users', '1');

        }

    }



    public function change_user_password($id, $pass1, $pass2)
    {

        if(!empty($pass1) && !empty($pass2) && $pass1 == $pass2)
        {
            $sql = "UPDATE users SET pass = :pass WHERE id = $id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'pass' => md5($pass1)
            ]);


            if ($stmt->rowCount() == '1') {
                $this->success = ["تم تغيير كلمة المرور للمستخدم رقم #$id بنجاح"];
                $this->redirect('users', '2');
            } else {
                $this->errors = ['حدث خطأ ما'];
            }

        } else if ($pass1 != $pass2) {
            $this->errors = ['يجب أن تكون كلمتي المرور متطابقتين'];
        } else {
            $this->errors = ['يجب كتابة كلمة المرور وتأكيدها'];
        }

    }
    
}
