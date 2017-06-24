<?php
include('inc/required.php');
session_start();

if(!empty($_GET['id']) && !empty($_GET['note']) && !empty($_GET['slug'])) {

  $idfilm = trim(strip_tags($_GET['id'])) ;
  $note = trim(strip_tags($_GET['note'])) ;
  $slug = trim(strip_tags($_GET['slug'])) ;

  $sql = "SELECT * FROM movies_user_note WHERE id_user= :id_user AND id_movie= :movie_id";
      $query = $pdo->prepare($sql);
      $query->bindValue(':id_user',$_SESSION['user']['id'], PDO::PARAM_INT);
      $query->bindValue(':movie_id',$idfilm, PDO::PARAM_INT);
      $query->execute();
      $result = $query->fetch();

  if(empty($result)){
    $sql= "INSERT INTO movies_user_note (id_movie, id_user, note, created_at)
          VALUES (:id_movie , :id_user, :note,NOW())";

          $query = $pdo->prepare($sql);
          $query->bindValue(':id_movie',$idfilm, PDO::PARAM_INT);
          $query->bindValue(':id_user',$_SESSION['user']['id'], PDO::PARAM_INT);
          $query->bindValue(':note',$note, PDO::PARAM_INT);
          $query->execute();
  } else {
    echo 'tu as déjà noté trou dbal';
  }
}

header('location:detail.php?slug='.$slug);
 ?>
