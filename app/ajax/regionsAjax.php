<?php

class regionsAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
        
        isset($_POST['get_region_by_id']) ? $this->get_region_by_id($_POST['id']) : null;


        //prevent useregionsom accessing this page manually
        if($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
        
    }



    public function get_region_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM regions WHERE id = $id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() == '1') {
            $region = $stmt->fetch();
            $this->output->region = $region->region;
        } else {
            $this->data->errors[] = "لا يوجد حي بهذا الاسم";
        }

        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}