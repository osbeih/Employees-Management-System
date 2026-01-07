<?php
require 'db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {

    $stmt = $pdo->prepare('SELECT image FROM employees WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $employee = $stmt->fetch();

    if ($employee && $employee['image'] && file_exists("uploads/" . $employee['image'])) {
        unlink("uploads/" . $employee['image']);
    }

    $stmt = $pdo->prepare('DELETE FROM employees WHERE id = :id');
    $stmt->execute(['id' => $id]);
}
header('Location: index.php');
exit;
?>