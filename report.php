
<?php

  require 'db.php';

  if(isset($_GET['from_date']) && isset($_GET['to_date'])){
    $from = $_GET['from_date'];
    $to  = $_GET['to_date'];

    $stmt = $pdo->prepare("SELECT EMPLOYEES.NAME,
    SUM(ATTENDANCE.STATUS = 'present') as present_days,
    SUM(ATTENDANCE.STATUS = 'absent') as absent_days,
    SUM(ATTENDANCE.STATUS = 'leave') as leave_days
    FROM ATTENDANCE
    JOIN EMPLOYEES ON ATTENDANCE.EMPLOYEE_ID = EMPLOYEES.ID
    WHERE ATTENDANCE_DATE BETWEEN :FROM AND :TO
    GROUP BY ATTENDANCE.EMPLOYEE_ID
    ");

    $stmt->execute(['FROM' => $from, 'TO' => $to]);
    $report = $stmt->fetchAll();

   
  }

?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>التقارير</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <header class="header">
      <div>
      <h3>تقارير الموظفين</h3>
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
      <section>
        <form class="form report-form" action="" method="get">
          <h4>اختر فترة زمنية </h4>
          <label>من:</label>
          <input type="date" name="from_date" required>
          <br>
          <label>الى:</label>
          <input type="date" name="to_date" required>
          <br>
          <button class="btn" style="background-color:blue" type="submit">عرض التقرير</button>
        </form>
      </section>
      <section>
        <?php if(!empty($report)):?>
          <?php if(count($report) < 1):?>
            <h3>لايوجد سجل خلال هذا التاريخ</h3>
          <?php endif;?>  
          <table class="table"  cellpadding="8">
            <tr>
              <th>اسم الموظف</th>
              <th>حاضر</th>
              <th>غائب</th>
              <th>اجازة</th>
            </tr>

            <?php foreach($report as $rep): ?>
              <tr>
                <td><?= htmlspecialchars($rep['NAME'])?></td>
                <td><?= htmlspecialchars($rep['present_days'])?></td>
                <td><?= htmlspecialchars($rep['absent_days'])?></td>
                <td><?= htmlspecialchars($rep['leave_days'])?></td>
              </tr>
            <?php endforeach;?>  
          </table>
        <?php endif;?>
      </section>
    </main>
  </body>
</html>
