<?php
include('inc/pdo.php');
include('inc/function.php');

$title = 'Accueil';

$sql = "SELECT id FROM movies_full ORDER BY RAND() LIMIT 10 ";
$query = $pdo->prepare($sql);
        $query->execute();
        $movies = $query->fetchAll();

include('inc/header.php'); ?>

<div class="container-fluid row justify-content-center mx-auto">
  <div class="movies_grid row mx-auto">
    <?php
    foreach ($movies as $movie) {
      $id = $movie['id'];
      afficherImage($movie);
    }
    ?>
  </div>

  <div class="container row justify-content-center mx-auto">
    <a class="btn btn_more_movies" href="index.php" role="button">+ de films !</a>
  </div>
</div>


<?php
function afficherImage($movie) {
  echo '<a href="detail.php?id= '.$movie['id'].'"><img class="vignette img-fluid" src="inc/img/posters/'.$movie['id'].'.jpg"  />' ;
}

include('inc/footer.php');

 ?>
