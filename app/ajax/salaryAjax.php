<?php

class salaryAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
        
        isset($_POST['get_salary_by_id']) ? $this->get_salary_by_id($_POST['id']) : null;


        //prevent users accessing this page manually
        if($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
        
    }



    public function get_salary_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM salary WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        if($stmt->rowCount() == '1') {
            $salary = $stmt->fetch();
            $this->output->salary_value = $salary->salary_value;
            $this->output->extra_work = $salary->extra_work;
            $this->output->advance = $salary->advance;
            $this->output->discounts = $salary->discounts;
            $this->output->salary_date = $salary->salary_date;
            $this->data->employee_id = $salary->employee_id;


        } else {
            $this->data->errors[] = "لا يوجد راتب بهذا الرقم";
        }

        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}