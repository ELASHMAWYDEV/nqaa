<?php

class productsAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();

        isset($_POST['get_product_by_id']) ? $this->get_product_by_id($_POST['id']) : null;
        isset($_POST['get_products']) ? $this->getProducts() : null;


        //prevent users accessing this page manually
        if ($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
    }



    public function get_product_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM products WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        if ($stmt->rowCount() == '1') {
            $product = $stmt->fetch();
            $this->output->name = $product->name;
            $this->output->price = $product->price;
        } else {
            $this->data->errors[] = "لا يوجد منتج بهذا الاسم";
        }

        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    public function getProducts()
    {
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $name = isset($_POST['name']) ? $_POST['name'] : "";

        $start = ($page - 1) * 10;

        $sql = "SELECT * FROM products
                WHERE 
                products.name LIKE '%$name%' 
                ORDER BY id DESC LIMIT $start,10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $this->data->products = $stmt->fetchAll();

        //Get the total count
        $sql = "SELECT COUNT(*) AS numOfResults FROM products";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->data->numOfResults = $stmt->fetchAll()[0]->numOfResults;

        $this->data->page_count = ceil($this->data->numOfResults / 10);
        $this->data->role = $_SESSION['lvl'];

        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
