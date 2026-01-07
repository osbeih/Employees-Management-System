<?php 
require 'db.php';


$employees = $pdo->query("SELECT id,name FROM employees")->fetchAll();


$id = isset($_GET['emp_id']) ? (int)$_GET['emp_id'] : 0;

if (isset($_POST['save'])) {

    $date = trim($_POST['attendance_date']);
    $statue = trim($_POST['statue']);

    if ($date !== '' && $statue !== '') {

        $stmt = $pdo->prepare("
            INSERT INTO attendance (employee_id, attendance_date, status)
            VALUES (:id, :date, :statue)
        ");

        $stmt->execute([
            'id' => $id,
            'date' => $date,
            'statue' => $statue
        ]);

        header("Location: attendance.php");
        exit;
    }
}


$empstat = $pdo->query("
    SELECT 
        attendance.id AS id,
        employees.name AS name,
        attendance.attendance_date AS attendance_date,
        attendance.status AS status
    FROM attendance
    INNER JOIN employees 
    ON employees.id = attendance.employee_id
")->fetchAll();


if (isset($_GET['emps_delete'])) {

    $delete_id = isset($_GET['emps_id']) ? (int)$_GET['emps_id'] : 0;

    $stmt = $pdo->prepare("DELETE FROM attendance WHERE id = :id");
    $stmt->execute(['id' => $delete_id]);

    header("Location: attendance.php");
    exit;
}


$editData = null;

if (isset($_GET['edit-emps'])) {

    $edit_id = isset($_GET['emps_id']) ? (int)$_GET['emps_id'] : 0;

    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE id = :id");
    $stmt->execute(['id' => $edit_id]);
    $editData = $stmt->fetch();
}


if (isset($_POST['save-edit'])) {

    $id_edit = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    $newdate = trim($_POST['attendance_date']);
    $newStatue = trim($_POST['statue']);

    $stmt = $pdo->prepare("
        UPDATE attendance
        SET attendance_date = :newdate,
            status = :newStatue
        WHERE id = :id
    ");

    $stmt->execute([
        'newdate' => $newdate,
        'newStatue' => $newStatue,
        'id' => $id_edit
    ]);

    header("Location: attendance.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ادارة الحضور</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div>
        <h3>ادارة حضور الموظفين</h3>
    </div>
    <nav>
        <ul>
            <li><a href="attendance.php">ادارة الحضور</a></li>
            <li><a href="index.php">الموظفين</a></li>
            <li><a href="report.php">التقارير</a></li>
        </ul>
    </nav>
</header>

<?php if(isset($_GET['attendance'])): ?>
    <form class="form" method="POST" action="">
        <label>التاريخ:</label>
        <input type="date" name="attendance_date" required>

        <br>
        <label>حالة الحضور:</label>
        <select name="statue">
            <option>present</option>
            <option>absent</option>
            <option>leave</option>
        </select>

        <br>
        <div class="div-btn">
            <button name="save" class="btn" type="submit" onclick="return confirm('هل تريد حفظ السجل؟')">اضافة السجل</button>
            <a style="height: 22px;background-color:red" class="link btn" href="attendance.php" class="btn">الغاء</a>
        </div>
    </form>
<?php endif; ?>

<main>
    <table class="table float-table">
        <caption>قائمة الموظفين</caption>
        <thead>
            <tr>
                <th>رقم الموظف</th>
                <th>اسم الموظف</th>
                <th>اجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($employees as $emp): ?>
            <tr>
                <td><?= htmlspecialchars($emp['id']) ?></td>
                <td><?= htmlspecialchars($emp['name']) ?></td>
                <td>
                    <form method="get">
                        <input type="hidden" name="emp_id" value="<?= htmlspecialchars($emp['id']) ?>">
                        <button class="btn" name="attendance">اضافة سجل حضور</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>

    
    <?php if($editData): ?>
        <form class="form" method="POST" action="">
            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($editData['id']) ?>">

            <label>التاريخ:</label>
            <input name="attendance_date" type="date" value="<?= htmlspecialchars($editData['attendance_date']) ?>" required>

            <br>
            <label>حالة الحضور:</label>
            <select name="statue">
                <option <?= $editData['status'] === 'present' ? 'selected' : '' ?>>present</option>
                <option <?= $editData['status'] === 'absent' ? 'selected' : '' ?>>absent</option>
                <option <?= $editData['status'] === 'leave' ? 'selected' : '' ?>>leave</option>
            </select>

            <br>
            <div class="div-btn">
                <button name="save-edit" class="btn" type="submit">حفظ التعديلات</button>
                <a style="height: 22px;background-color:red" class="link btn" href="attendance.php" class="btn">الغاء</a>
            </div>
        </form>
    <?php endif; ?>

   
    <table class="table">
        <caption>السجلات</caption>
        <thead>
            <tr>
                <th>اسم الموظف</th>
                <th>التاريخ</th>
                <th>حالة الحضور</th>
                <th>اجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($empstat as $emps): ?>
            <tr>
                <td><?= htmlspecialchars($emps['name']) ?></td>
                <td><?= htmlspecialchars($emps['attendance_date']) ?></td>
                <td><?= htmlspecialchars($emps['status']) ?></td>
                <td>
                    <form action="" method="get">
                        <input type="hidden" name="emps_id" value="<?= htmlspecialchars($emps['id']) ?>">
                        <button class="btn" name="edit-emps">تعديل</button>
                        <button class="btn" name="emps_delete" onclick="return confirm('هل تريد حذف السجل؟')">حذف</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>    
        </tbody>
    </table>

</main>

</body>
</html>
