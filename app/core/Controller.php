<?php

class Controller extends Database
{
    protected $view;
    public $success = [];
    public $errors = [];

    public function __construct()
    {
        parent::__construct();
        
    }


    //rendering the route file
    public function view($viewName)
    {
        $this->view = new View($viewName);
        return $this->view;
    }


    //check if user is not logged in => redirect to login page
    public function loggedIn()
    {
        if(!isset($_SESSION['lvl']))
        {
            header('location: ' . ROOT_URL . 'login');
        }
    }


    public function redirect($controller = '', $interval = '0', $header = false)
    {
        if(!$header)
            echo "<META HTTP-EQUIV='refresh' content='" . $interval . ";URL=" . ROOT_URL . $controller . "'>";
        else
            header('location: ' . ROOT_URL . $controller);
    }

    //show the latest news at the start of every page except the log in page
    public function showNews()
    {
        $showNews = '';
        $sql = "SELECT * FROM news ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() != '0')
        {
            
            $this->news = $stmt->fetchAll();

            foreach($this->news as $news) {
                $news->create_date = date("d/m/Y h:ia", strtotime($news->create_date));
            }
            
            $showNews ="<div class='show-news-box'><h2>الأخبار الجديدة</h2><ul>";
            foreach($this->news as $news) {
                $showNews .= "<li>- $news->news<span>$news->create_date</span></li>";
            }
            $showNews .= "</ul></div>";


        }
        return $showNews;
    }

    public function role($lvl)
    {
        if ($lvl == $_SESSION['lvl']) return true;
        else return false;
    }


}