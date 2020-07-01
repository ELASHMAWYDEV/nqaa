<?php

class Database
{
    private $host;
    private $db_name;
    private $user;
    private $pass;
    protected $db;
    public $stmt;

    /*
    *
    *
    *
    *   Initializing The Database Connection
    *
    *
    */
    public function __construct()
    {
        $this->host = HOST;
        $this->db_name = DB_NAME;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        try
        {
            $this->db = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8;', $this->user, $this->pass,[
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

        }
        catch(PDOException $e)
        {
            die("Error<br>" . print_r($e->getMessage()));
        }
    }

    
}

