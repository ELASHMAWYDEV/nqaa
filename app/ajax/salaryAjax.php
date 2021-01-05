<?php

class salaryAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();

        isset($_POST['get_salary_by_id']) ? $this->get_salary_by_id($_POST['id']) : null;
        isset($_POST['get_salary']) ? $this->getSalary() : null;


        //prevent users accessing this page manually
        if ($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
    }



    public function get_salary_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM salary WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        if ($stmt->rowCount() == '1') {
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


    public function getSalary()
    {


        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $salary_date = isset($_POST['salary_date']) ? $_POST['salary_date'] : "";
        $create_date = !empty($_POST['create_date']) ? date("Y-m-d", mktime(0, 0, 0, explode("/", $_POST['create_date'])[1], explode("/", $_POST['create_date'])[0], explode("/", $_POST['create_date'])[2])) : "";
        $employee_name = isset($_POST['employee_name']) ? $_POST['employee_name'] : "";

        $start = ($page - 1) * 10;


        //discounts
        $sql = "SELECT salary.*, users.name FROM salary 
                LEFT JOIN users ON salary.employee_id = users.id 
                WHERE
                salary.salary_date LIKE '%$salary_date%' AND 
                salary.create_date LIKE '%$create_date%' AND 
                users.name LIKE '%$employee_name%'
                ORDER BY id DESC
                LIMIT $start, 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() != '0') {

            $salary = $stmt->fetchAll();
            foreach ($salary as $oneSalary) {

                $oneSalary->create_date = date("d/m/Y h:ia", strtotime($oneSalary->create_date));
                $oneSalary->totalBeforeDiscounts = $oneSalary->salary_value + $oneSalary->extra_work;

                $oneSalary->totalAfterDiscounts = $oneSalary->totalBeforeDiscounts - $oneSalary->discounts;
            }

            $this->data->salary = $salary;
        } else {
            $this->data->salary = [];
            $this->data->errors[] = 'لا يوجد رواتب لعرضها';
        }

        //Get the total count
        $sql = "SELECT COUNT(*) AS numOfResults FROM salary
                WHERE
                salary.salary_date LIKE '%$salary_date%' AND 
                salary.create_date LIKE '%$create_date%' AND 
                users.name LIKE '%$employee_name%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;

        $this->data->page_count = ceil($this->data->numOfResults / 10);
        $this->data->role = $_SESSION['lvl'];

        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
