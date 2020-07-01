<?php

class usersAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
        
        //prevent users from accessing this page manually
        if($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
        
    }




}