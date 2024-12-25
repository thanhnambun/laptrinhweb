<?php include '../../config/db_connect.php'; ?>

<?php
$id = $_GET['user_id'];
$connect->query("DELETE FROM users WHERE user_id = $id");

header("Location: ../manager_user.php");
exit();
?>