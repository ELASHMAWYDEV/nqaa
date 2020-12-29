<?php

class regularAjax extends Ajax
{
  public function __construct()
  {
    parent::__construct();


    isset($_POST['get_regular']) ? $this->getRegular() : null;

    //prevent users from accessing this page manually
    if ($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
  }



  public function getRegular()
  {
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $old_order_id = isset($_POST['old_order_id']) ? $_POST['old_order_id'] : "";
    $name = isset($_POST['name']) ? $_POST['name'] : "";
    $phone = isset($_POST['phone']) ? $_POST['phone'] : "";
    $region = isset($_POST['region']) ? $_POST['region'] : "";
    $next_date = isset($_POST['next_date']) ? date("Y-m-d", strtotime($_POST['next_date'])) : "";

    $start = ($page - 1) * 10;


    $sql = "SELECT regular.*, orders.type, orders.status, regions.region FROM regular 
        LEFT JOIN orders ON regular.old_order_id = orders.id 
        LEFT JOIN regions ON regular.region = regions.id
        WHERE
        regular.old_order_id LIKE '%$old_order_id%' AND 
        regular.name LIKE '%$name%' AND 
        regular.phone LIKE '%$phone%' AND 
        regular.region LIKE '%$region%' AND
        regular.next_date LIKE '%$next_date%'
        ORDER BY id DESC LIMIT $start, 10";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $this->data->regular = [];
    $this->data->numOfResults = 0;

    if ($stmt->rowCount() == '0') {
      $this->data->errors[] = "لا يوجد طلبات صيانة دورية لعرضها";
    } else {
      $this->data->regular = $stmt->fetchAll();

      foreach ($this->data->regular as $order) {
        $order->next_date = date("d/m/Y", strtotime($order->next_date));
      }

      //Get the total count
      $sql = "SELECT COUNT(*) AS numOfResults FROM regular  
              WHERE 
              regular.old_order_id LIKE '%$old_order_id%' AND 
              regular.name LIKE '%$name%' AND 
              regular.phone LIKE '%$phone%' AND 
              regular.region LIKE '%$region%' AND
              regular.next_date LIKE '%$next_date%'";

      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;
    }
    $this->data->page_count = ceil($this->data->numOfResults / 10);
    $this->data->role = $_SESSION['lvl'];


    echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  }
}
