<?php
include('inc/required.php');
session_start();

$user = $_SESSION['user']['id'];

$sql = "SELECT * FROM movies_user_note AS mun
LEFT JOIN movies_full AS mf
ON mun.id_user = $user
AND mf.id = mun.id_movie ";

$query = $pdo->prepare($sql);
$query->execute();
$result = $query->fetchAll();

debug($result);

 ?>
