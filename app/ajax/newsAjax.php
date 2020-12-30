<?php

class newsAjax extends Ajax
{
  public function __construct()
  {
    parent::__construct();

    isset($_POST['get_news']) ? $this->getNews() : null;

    //prevent users from accessing this page manually
    if ($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
  }


  public function getNews()
  {
    $page = isset($_POST['page']) ? $_POST['page'] : 1;

    $start = ($page - 1) * 10;

    $sql = "SELECT * FROM news ORDER BY id DESC LIMIT $start, 10";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $this->data->news = [];
    if ($stmt->rowCount() != '0') {

      $this->news = $stmt->fetchAll();

      foreach ($this->news as $news) {
        $news->create_date = date("d/m/Y h:ia", strtotime($news->create_date));
      }
      $this->data->news = $this->news;
    } else {
      $this->data->errors[] = "لا يوجد أخبار معروضة حاليا";
    }

    //Get the total count
    $sql = "SELECT COUNT(*) AS numOfResults FROM news";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;

    $this->data->page_count = ceil($this->data->numOfResults / 10);
    $this->data->role = $_SESSION['lvl'];

    echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  }
}
