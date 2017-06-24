<?php
include('inc/required.php');
session_start();

$user = $_SESSION['user']['id'];

$sql = "SELECT * FROM movies_user_note AS mun
INNER JOIN movies_full AS mf
WHERE mf.id = mun.id_movie AND mun.id_user = $user";

$query = $pdo->prepare($sql);
$query->execute();
$result = $query->fetchAll();

debug($result);

 ?>
