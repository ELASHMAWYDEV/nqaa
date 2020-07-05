<?php

class Application
{
    protected $controller = '';
    protected $ajax = '';


    public function __construct()
    {
        session_start();
        $this->parseUrl();
        if(file_exists(CONTROLLER . $this->controller . '.php'))
        {
            $this->controller = new $this->controller;

        }
        else if(file_exists(AJAX . $this->ajax . '.php'))
        {
            $this->ajax = new $this->ajax;
        }
        else
        {
            echo 'No such directory !<br>redirecting....';
            header('Refresh: 0; url=' . ROOT_URL . 'login');
        }
    }


    protected function parseUrl()
    {
        $request = trim($_SERVER['REQUEST_URI'], '/');
        if(!empty($request))
        {
            $url = explode('/', $request);
            //edit $url[index] if the root is a subdirectory to the main website
            
            if(empty(ROOT_FOLDER)) {
                $index = 0;
            } else {
                $index = count(explode(ROOT_URL, '/'));
            }

            if(isset($url[$index]) && $url[$index] != 'ajax') {

                $this->controller =  $url[$index] . 'Controller';

            } else if($url[$index] == 'ajax' && isset($url[$index + 1])) {

                $this->ajax = $url[$index + 1] . 'Ajax';

            } else {

                $this->controller = 'loginController';
            }
        }
    }





   
}