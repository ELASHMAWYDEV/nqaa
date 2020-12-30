<?php

class regionsAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();

        isset($_POST['get_region_by_id']) ? $this->get_region_by_id($_POST['id']) : null;
        isset($_POST['get_regions']) ? $this->getRegions() : null;


        //prevent useregionsom accessing this page manually
        if ($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
    }



    public function get_region_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM regions WHERE id = $id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() == '1') {
            $region = $stmt->fetch();
            $this->output->region = $region->region;
        } else {
            $this->data->errors[] = "لا يوجد حي بهذا الاسم";
        }

        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }



    public function getRegions()
    {

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $region = isset($_POST['region']) ? $_POST['region'] : "";

        $start = ($page - 1) * 10;

        $sql = "SELECT * FROM regions 
                WHERE
                regions.region LIKE '%$region%'
                ORDER BY id DESC LIMIT $start, 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() != '0') {
            $this->data->regions = $stmt->fetchAll();
        } else {
            $this->errors[] = 'لا يوجد أحياء لعرضها';
        }

        //Get the total count
        $sql = "SELECT COUNT(*) AS numOfResults FROM regions";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;

        $this->data->page_count = ceil($this->data->numOfResults / 10);
        $this->data->role = $_SESSION['lvl'];

        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
