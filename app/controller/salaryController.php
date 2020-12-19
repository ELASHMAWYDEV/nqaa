<?php

class salaryController extends Controller
{

  public function __construct()
  {
    parent::__construct();

    //role
    !$this->role('مدير') ? header('location: stats') : null;

    isset($_POST['add_salary']) ? $this->addSalary($_POST['salary_value'], $_POST['extra_work'], $_POST['discounts'], $_POST['advance'], $_POST['employee_id'], $_POST['salary_date']) : null;
    isset($_POST['delete_salary']) ? $this->deleteSalary($_POST['id']) : null;
    isset($_POST['edit_salary']) ? $this->editSalary($_POST['id']) : null;


    $this->loggedIn();
    $this->view('salary');
    $this->view->title = "شركة نقاء | الرواتب";
    $this->getSalary();
    $this->getUsers();
    $this->view->renderHeader();
    $this->view->renderFile();
    $this->view->viewMessages($this->errors, $this->success);
    $this->view->renderFooter();
    $this->view->pagination(10);
  }



  public function getSalary()
  {


    //discounts
    $sql = "SELECT salary.*, users.name FROM salary LEFT JOIN users ON salary.employee_id = users.id ORDER BY id DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() != '0') {

      $salary = $stmt->fetchAll();
      foreach ($salary as $oneSalary) {

        $oneSalary->create_date = date("d/m/Y h:ia", strtotime($oneSalary->create_date));
        $oneSalary->totalBeforeDiscounts = $oneSalary->salary_value + $oneSalary->extra_work;

        $oneSalary->totalAfterDiscounts = $oneSalary->totalBeforeDiscounts - $oneSalary->discounts;
      }

      $this->view->salary = $salary;
    } else {
      $this->view->salary = [];
      $this->errors[] = 'لا يوجد رواتب لعرضها';
    }
  }


  public function getUsers()
  {
    $Sql = "SELECT * FROM users";
    $stmt = $this->db->prepare($Sql);
    $stmt->execute();
    $users = $stmt->fetchAll();

    $this->view->users = $users;

    return $users;
  }


  public function addSalary($salary_value, $extra_work, $discounts, $advance, $id, $salary_date)
  {

    if(!$id) {
      return $this->errors[] = "يجب اختيار الموظف";
    }

    if (is_numeric($salary_value) && is_numeric($extra_work) && is_numeric($discounts) && is_numeric($advance)) {

      $sql = "INSERT INTO salary (salary_value, extra_work, discounts, advance, employee_id, salary_date) VALUES ( ?, ?, ?, ?, ?, ?)";
      $stmt = $this->db->prepare($sql);
      $stmt->execute([number_format($salary_value, 0, '.', ''), number_format($extra_work, 0, '.', ''), number_format($discounts, 0, '.', ''), number_format($advance, 0, '.', ''), $id, $salary_date]);


      if ($stmt->rowCount() == '1') {
        $this->success[] = 'تم حفظ المبلغ بنجاح';
        $this->redirect('salary', '2');
      } else {
        $this->errors[] = 'حدث خطأ ما';
      }
    } else {

      $this->errors[] = 'يجب أن تكون جميع المدخلات أرقام انجليزية';
    }
  }


  public function deleteSalary($id)
  {

    $sql = "DELETE FROM salary WHERE id = ? LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() != '0') {
      $this->success[] = "تم حذف الراتب رقم #$id بنجاح";
      $this->redirect('salary', '2');
    } else {
      $this->errors[] = "حدث خطأ ما";
    }
  }


  public function editSalary($id)
  {

    $sql = "UPDATE salary SET salary_value =  :salary_value, extra_work = :extra_work, discounts = :discounts, advance = :advance, employee_id = :employee_id WHERE id = :id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
      'id' => $id,
      'salary_value' => $_POST['salary_value'],
      'extra_work' => $_POST['extra_work'],
      'discounts' => $_POST['discounts'],
      'advance' => $_POST['advance'],
      'employee_id' => $_POST['employee_id']

    ]);

    if ($stmt->rowCount() == '1') {
      $this->success[] = "تم تعديل الراتب بنجاح";
      $this->redirect('salary', '2');
    } else {
      $this->errors[] = "حدث خطأ ما";
    }
  }
}
