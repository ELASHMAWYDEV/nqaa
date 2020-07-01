<?php

class productsController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        //role
        $this->role('فني') ? header('location: stats') : null;


        isset($_POST['delete_products']) ? $this->deleteproducts($_POST['id']) : null;
        isset($_POST['add_products']) ? $this->addproducts($_POST['name'], $_POST['price']) : null;

        $this->loggedIn();
        $this->view('products');
        $this->view->title = 'المنتجات';
        $this->getProducts();
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        $this->view->pagination();
    }



    public function getProducts()
    {

        $sql = "SELECT * FROM products ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $this->view->products = $stmt->fetchAll();
    }




    public function addproducts($name, $price)
    {
        $sql = "INSERT INTO products (name, price) VALUES (:name, :price)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'price' => $price
        ]);

        if($stmt->rowCount() != '0') {
            $this->success[] = "تم اضافة المنتج بنجاح";
        } else {
            $this->errors[] = "حدث خطأ ما";
        } 
        $this->redirect('products', '1');
    }


    public function deleteproducts($products_id)
    {
        $sql = "DELETE FROM products WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$products_id]);

        if($stmt->rowCount() != '0') {
            $this->success[] = "تم حذف المنتج بنجاح";
        } else {
            $this->errors[] = "حدث خطأ ما";
        } 
        $this->redirect('products', '1');
    }

    

}