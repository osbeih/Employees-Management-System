<?php
    require 'db.php';

    $errors = [];

    if(isset($_POST['addBtn'])){
      $name = trim($_POST['name']);
      $email = trim($_POST['email']);
      $hireDate = trim($_POST['hireDate']);
      $imagename = null;

      if($name === '' && $email === '' && $hireDate === ''){
        $errors = 'جميع الحقول مطلوبة';
      }

      if(!empty($_FILES['image']['name'])){
        $uploadsDir = 'uploads/';
        if(!is_dir($uploadsDir)) mkdir($uploadsDir);

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imagename = uniqid().'.' .$ext;
        $target = $uploadsDir . $imagename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $errors[] = 'فشل رفع الصورة.';
        }
      }

      if(empty($errors)){
        $stmt = $pdo->prepare('INSERT INTO employees (name, email, hire_date, image)
        values (:name, :email, :hireDate, :image)');

        $stmt->execute(['name' => $name, 'email' => $email, 'hireDate' => $hireDate, 'image' => $imagename]);

        header('Location:index.php');
        exit;
      }
    }

              

    $employees = $pdo->query('SELECT * FROM EMPLOYEES')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ادارة الموظفين</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <header class="header">
      <div>
      <h3>ادارة الموظفين</h3>
    </div>
      <nav>
      <ul>
        <li><a href="attendance.php">ادارة الحضور</a></li>
        <li><a href="index.php">الموظفين</a></li>
        <li><a href="report.php">التقارير</a></li>
      </ul>
    </nav>
    </header>
    <main>
      <form method="POST">
        <button name="add-btn" class="btn addEmp-btn" type="submit">اضافة موظف</button>
      </form>
        <?php if(isset($_POST['add-btn'])):?>
          <form class="add-form add-form_cont" method="POST" action="" enctype="multipart/form-data">
          <h4>اضافة موظف</h4>

        
            <label>اسم الموظف</label>
            <input type="text" name="name" required/>
            <br>
            <label>ألايميل</label>
            <input type="email" name="email" required/>
         

          <br>
            <label>تاريخ التوظيف</label>
            <input type="date" name="hireDate" required/>
          
          <br>
          
            <label>صورة الموظف:</label>
            <input type="file" name="image" accept="image/*"/>
          <br>
          <div class="action">
            <button class="add-btn btn" type="submit" name="addBtn" onclick="return confirm('انت متاكد من اضافة الموظف؟')">حفظ</button>
            <a style="width:100px;text-align:center;height: 22px;background-color:red;" class="btn link" href="index.php">الغاء</a>
          </div>
        </form>
        <?php endif;?>  
      <div class="employees">
        <table class="table">
          <caption>الموظفين المسجلين</caption>
          <thead>
            <tr>
              <th>#</th>
              <th>الاسم</th>
              <th>ألايميل</th>
              <th>تاريخ التوظيف</th>
              <th>صورة الموظف</th>
              <th>
                اجراءات
              </th>
          </tr>
          </thead>
          <tbody>
            <?php foreach($employees as $emp):?>
              <tr>
                <?php if(empty($employees)):?>
                  <P class="para">لايوجد موظفين</P>
                <?php endif;?>  
                <td><?= htmlspecialchars($emp['id'])?></td>
                <td><?=htmlspecialchars($emp['name'])?></td>
                <td><?=htmlspecialchars($emp['email'])?></td>
                <td><?=htmlspecialchars($emp['hire_date'])?></td>
                <td>
                  <img style="width:50px" src="uploads/<?= htmlspecialchars($emp['image'])?>">
                </td>
                <td class="td-btn">
                  <a class="edit-btn link" href="edit.php?id=<?= htmlspecialchars($emp['id'])?>">تعديل</a>
                  <a class="delete-btn link" href="delete.php?id=<?= htmlspecialchars($emp['id'])?>" onclick="return confirm('هل انت متأكد من حذف الموظف')">حذف</a>
                </td>
              </tr>
            <?php endforeach;?>  
          </tbody>
        </table>
      </div>
    </main>
  </body>
</html>
