<?php 

include "connect.php";

$cmd = "SELECT * FROM products";
$stmt = $dbh->prepare($cmd);
$stmt->execute();
$menu = $stmt->fetchAll();

echo json_encode($menu);