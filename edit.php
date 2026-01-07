<?php
    require 'db.php';

    $id = (int)($_GET['id'] ?? 0);
    if($id <= 0){
        header('location:employee.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM employees where id = :id');
    $stmt->execute(['id' => $id]);
    $emp = $stmt->fetch();

    if(!$emp){header('Location:index.php'); exit;}

    $name = $emp['name'];
    $email = $emp['email'];
    $hireDate = $emp['hire_date'];
    $image = $emp['image'];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $hireDate = trim($_POST['hireDate']);
        $imagename = $image;

        if($name === '' || $email === '' || $hireDate === ''){
            $errors = 'جميع الحقول مطلوبة';
        }

        if(!empty($_FILES['image']['name'])){
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir);

            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imagename  = uniqid().'.'.$ext;
            $target = $uploadDir. $imagename;

            if(move_uploaded_file($_FILES['image']['tmp_name'], $target)){
                if($image && file_exists("uploads/$image")) unlink("uploads/$image");
            } else  $errors = "فشل رفع الصورة";
        }

        if(empty($errors)){
            $stmt = $pdo->prepare('UPDATE employees SET name=:name, email=:email, hire_date=:hireDate, image=:image where id =:id');

            $stmt->execute(['name' => $name, 'email' => $email, 'hireDate' => $hireDate, 'image' => $imagename, 'id' => $id]);
            header('Location:index.php');
            exit;
        }
    }
?>


<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
      <h1>تعديل بيانات موظف</h1>
    </header>
    <main class="main">
        <div class="add-form_cont">
         <form class="add-form"  method="POST" action="" enctype="multipart/form-data">
          <label>اسم الموظف</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name)?>" required/>
          
            <br>
          <label>ألايميل</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email)?>" required/>
          
            <br>
          <label>تاريخ التوظيف</label>
            <input type="date" name="hireDate" value="<?= htmlspecialchars($hireDate)?>" required/>
          
            <br>
          <label>الصورة الحالية</label>
            <?php if($image):?>
                <img style="width: 100px;;" src="uploads/<?= htmlspecialchars($image)?>"/>
            <?php else: ?>    
                <p>لاتوجد صوؤة</p>
            <?php endif; ?>
            <br>
            <label>تغيير الصورة</label>
            <input type="file" name="image" accept="image/*">
            <br>
          <button class="btn" type="submit" name="addBtn" >حفظ التعديلات</button>
          <a class="" href="index.php">الغاء</a>
        </form>
       </div> 
    </main>
</body>
</html>