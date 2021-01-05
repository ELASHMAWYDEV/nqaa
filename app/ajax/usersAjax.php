<?php

class usersAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();

        isset($_POST['edit_user']) ? $this->edit_user(json_decode($_POST['data'])) : null;
        isset($_POST['get_user_by_id']) ? $this->get_user_by_id(json_decode($_POST['id'])) : null;
        isset($_POST['delete_user_by_id']) ? $this->delete_user_by_id(json_decode($_POST['id'])) : null;
        isset($_POST['get_users']) ? $this->getUsers() : null;


        //prevent users from accessing this page manually
        if ($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
    }




    public function getUsers()
    {

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $username = isset($_POST['username']) ? $_POST['username'] : "";
        $name = isset($_POST['name']) ? $_POST['name'] : "";
        $email = isset($_POST['email']) ? $_POST['email'] : "";
        $phone = isset($_POST['phone']) ? $_POST['phone'] : "";
        $id = isset($_POST['id']) ? $_POST['id'] : "";
        $lvl = isset($_POST['lvl']) ? $_POST['lvl'] : "";

        $start = ($page - 1) * 10;

        $sql = "SELECT * FROM users 
                WHERE
                users.username LIKE '%$username%' AND
                users.name LIKE '%$name%' AND
                users.email LIKE '%$email%' AND
                users.phone LIKE '%$phone%' AND
                users.id LIKE '%$id%' AND
                users.lvl LIKE '%$lvl%'
                ORDER BY id DESC LIMIT $start, 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->data->users = [];

        if ($stmt->rowCount() != '0') {
            $users = $stmt->fetchAll();

            foreach ($users as $user) {
                !empty($user->login_date) ? $user->login_date = date("d/m/Y h:ia", strtotime($user->login_date)) : null;
            }

            $this->data->users = $users;
        } else {
            $this->data->errors = 'لا يوجد مستخدمين لعرضهم';
        }

        //Get the total count
        $sql = "SELECT COUNT(*) AS numOfResults FROM users 
                WHERE
                users.username LIKE '%$username%' AND
                users.name LIKE '%$name%' AND
                users.email LIKE '%$email%' AND
                users.phone LIKE '%$phone%' AND
                users.id LIKE '%$id%' AND
                users.lvl LIKE '%$lvl%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;

        $this->data->page_count = ceil($this->data->numOfResults / 10);
        $this->data->role = $_SESSION['lvl'];

        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function edit_user($data)
    {
        $this->data = $data;
        $this->output = $this->data->output;

        if (
            !empty($this->output->username) && !empty($this->output->email)
            && !empty($this->output->phone) && !empty($this->output->name) && !empty($this->data->lvl)
        ) {
            $sql = "UPDATE users SET username = :username, email = :email, `name` = :name, phone = :phone, lvl = :lvl WHERE id = " . $this->data->id . " LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'username' => $this->output->username,
                'email' => $this->output->email,
                'name' => $this->output->name,
                'phone' => $this->output->phone,
                'lvl' => $this->data->lvl
            ]);

            if ($stmt->rowCount() == '1') {
                $this->data->success[] = 'تم حفظ البيانات بنجاح';
                $this->data->reload = 'true';
            } else {
                $this->data->errors[] = 'لم تقم بأي تغيير للحفظ';
            }
        } else {
            $this->data->errors[] = 'يجب ملئ جميع البيانات';
        }


        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }










    public function get_user_by_id($id)
    {
        $this->id = $id;
        $this->output = $this->data->output;
        $sql = "SELECT * FROM users WHERE id = $id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() == '1') {
            $user = $stmt->fetch();
            $this->output->email = $user->email;
            $this->output->username = $user->username;
            $this->output->name = $user->name;
            $this->output->phone = $user->phone;
            $this->data->lvl = $user->lvl;
        } else {
            $this->data->errors[] = 'لا يوجد مشرف بهذا الرقم';
            $this->data->reload = 'true';
        }
        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    public function delete_user_by_id($id)
    {
        $this->id = $id;
        $sql = "DELETE FROM users WHERE id = $id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() == '1') {
            $this->data->success[] = 'تم حذف المشرف رقم ' . $id . ' بنجاح';
            $this->data->reload = 'true';
        } else {
            $this->data->errors[] = 'هناك خطأ في رقم المشرف أو تم حذف بالفعل';
        }
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
