<?php

class commonAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
        
        isset($_POST['update_account_settings']) ? $this->update_account_settings(json_decode($_POST['data'])) : null;
        isset($_POST['change_account_password']) ? $this->change_account_password(json_decode($_POST['data'])) : null;
        
        //prevent users from accessing this page manually
        if($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
    }


    public function update_account_settings($data)
    {
        $this->data = $data;
        $this->output = $this->data->output;
        $id = $_SESSION['id'];
        
        if (!empty($this->output->username) && !empty($this->output->email)
            && !empty($this->output->phone) && !empty($this->output->name))
        {
            $sql = "UPDATE users SET username = :username, email = :email, `name` = :name, phone = :phone WHERE id = $id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'username' => $this->output->username,
                'email' => $this->output->email,
                'name' => $this->output->name,
                'phone' => $this->output->phone
            ]);

            if ($stmt->rowCount() == '1')
            {
                $this->data->success = ['تم حفظ البيانات بنجاح'];
                $_SESSION['username'] = $this->output->username;
                $_SESSION['email'] = $this->output->email;
                $_SESSION['name'] = $this->output->name;
                $_SESSION['phone'] = $this->output->phone;
                $this->data->reload = 'true';
                
            }
            else
            {
                $this->data->errors = ['لم تقم بأي تغيير للحفظ'];
            }
            
            $this->data->output = $this->output;
            echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        }
        else
        {
            $sql = "SELECT * FROM users WHERE id = $id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $userData = $stmt->fetch();
            $this->output->username = $userData->username; 
            $this->output->email = $userData->email; 
            $this->output->name = $userData->name; 
            $this->output->phone = $userData->phone; 
            array_push($this->data->errors,'يجب ملئ جميع البيانات أولا');
            
            $this->data->output = $this->output;
            echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }




    /************************************/

    public function change_account_password($data)
    {
        $this->data = $data;
        $this->output = $this->data->output;
        $id = $_SESSION['id'];


        if(!empty($this->output->pass1) && !empty($this->output) && $this->output->pass1 == $this->output->pass2)
        {
            $sql = "UPDATE users SET pass = :pass WHERE id = $id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'pass' => md5($this->output->pass1)
            ]);
            if ($stmt->rowCount() == '1')
            {
                $this->data->success = ['تم تغيير كلمة المرور بنجاح'];
                $_SESSION['pass'] = $this->output->pass1;
            }
            else
            {
                $this->data->errors = ['حدث خطأ ما'];
            }
        }
        else if ($this->output->pass1 != $this->output->pass2)
        {
            $this->data->errors = ['يجب أن تكون كلمتي المرور متطابقتين'];
        }
        else
        {
            $this->data->errors = ['يجب كتابة كلمة المرور وتأكيدها'];
        }
        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    /************************************/
}