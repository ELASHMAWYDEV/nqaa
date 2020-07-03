<?php

class productsAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
        
        isset($_POST['get_product_by_id']) ? $this->get_product_by_id($_POST['id']) : null;


        //prevent users accessing this page manually
        if($_SERVER['REQUEST_METHOD'] != 'POST') header('location: ' . ROOT_URL);
        
    }



    public function get_product_by_id($id)
    {

        $this->output = $this->data->output;
        $sql = "SELECT * FROM products WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        if($stmt->rowCount() == '1') {
            $product = $stmt->fetch();
            $this->output->name = $product->name;
            $this->output->price = $product->price;

        } else {
            $this->data->errors[] = "لا يوجد منتج بهذا الاسم";
        }

        $this->data->output = $this->output;
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}