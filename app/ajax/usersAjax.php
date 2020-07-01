<?php

class usersAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
        
        isset($_POST['edit_user']) ? $this->edit_user(json_decode($_POST['data'])) : null;
        isset($_POST['get_user_by_id']) ? $this->get_user_by_id(json_decode($_POST['id'])) : null;
        isset($_POST['delete_user_by_id']) ? $this->delete_user_by_id(json_decode($_POST['id'])) : null;


        //prevent users from accessing this page manually
        if($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
        
    }





    public function edit_user($data)
    {
        $this->data = $data;
        $this->output = $this->data->output;
        
        if (!empty($this->output->username) && !empty($this->output->email)
            && !empty($this->output->phone) && !empty($this->output->name) && !empty($this->data->lvl))
        {
            $sql = "UPDATE users SET username = :username, email = :email, `name` = :name, phone = :phone, lvl = :lvl WHERE id = " . $this->data->id . " LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'username' => $this->output->username,
                'email' => $this->output->email,
                'name' => $this->output->name,
                'phone' => $this->output->phone,
                'lvl' => $this->data->lvl
            ]);

            if ($stmt->rowCount() == '1')
            {
                $this->data->success[] = 'تم حفظ البيانات بنجاح';
                $this->data->reload = 'true';
            }
            else
            {
                $this->data->errors[] = 'لم تقم بأي تغيير للحفظ';
            }

        }
        else
        {
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
        if($stmt->rowCount() == '1')
        {
            $user = $stmt->fetch();
            $this->output->email = $user->email;
            $this->output->username = $user->username;
            $this->output->name = $user->name;
            $this->output->phone = $user->phone;
            $this->data->lvl = $user->lvl;            

        }
        else
        {
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
        if($stmt->rowCount() == '1')
        {
            $this->data->success[] = 'تم حذف المشرف رقم ' . $id . ' بنجاح';
            $this->data->reload = 'true';
        }
        else
        {
            $this->data->errors[] = 'هناك خطأ في رقم المشرف أو تم حذف بالفعل';
        }
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}