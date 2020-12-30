<?php

class newsController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        //role
        !$this->role('مدير') ? header('location: stats') : null;

        isset($_POST['delete_news']) ? $this->deleteNews($_POST['id']) : null;
        isset($_POST['add_news']) ? $this->addNews($_POST['news']) : null;

        $this->loggedIn();
        $this->view('news');
        $this->view->title = "شركة نقاء | الأخبار";
        $this->getNews();
        $this->view->renderHeader();
        $this->view->renderFile();
        $this->view->viewMessages($this->errors, $this->success);
        $this->view->renderFooter();
        $this->view->pagination();
    }

    public function getNews()
    {
        $sql = "SELECT * FROM news ORDER BY id DESC LIMIT 0, 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() != '0') {

            $this->news = $stmt->fetchAll();

            foreach ($this->news as $news) {
                $news->create_date = date("d/m/Y h:ia", strtotime($news->create_date));
            }
            $this->view->news = $this->news;
        } else {
            $this->errors[] = "لا يوجد أخبار معروضة حاليا";
        }

        //Get the total count
        $sql = "SELECT COUNT(*) AS numOfResults FROM news";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $this->view->numOfResults = $stmt->fetchAll()[0]->numOfResults;
    }


    public function addNews($news)
    {
        $sql = "INSERT INTO news (news) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$news]);

        if ($stmt->rowCount() != '0') {
            $this->success[] = "تم اضافة الخبر بنجاح";
        } else {
            $this->errors[] = "حدث خطأ ما";
        }
        $this->redirect('news', '1');
    }


    public function deleteNews($news_id)
    {
        $sql = "DELETE FROM news WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$news_id]);

        if ($stmt->rowCount() != '0') {
            $this->success[] = "تم حذف الخبر بنجاح";
        } else {
            $this->errors[] = "حدث خطأ ما";
        }
        $this->redirect('news', '1');
    }
}
