<?php

if(file_exists('inc/pdo.php')){
  include('inc/pdo.php');
} else {
  include('inc/pdo-thorphin.php');
}
include('inc/function.php');

// requete pour afficher les 10 premiers films

$sql = "SELECT id FROM movies_full ORDER BY RAND() LIMIT 10 ";
$query = $pdo->prepare($sql);
        $query->execute();
        $movies = $query->fetch();

foreach ($movies as $movie) {
  $id = $movie['id'];
  afficherImage($movie);
}

// requete pour afficher les catégories

$sql= "SELECT genres FROM movies_full";

$query = $pdo->prepare($sql);
$query->execute();
$movies = $query->fetchAll();


$genres = array(

);

foreach($movies as $movie){
$categories_by_film = $movie['genres'];

$cats = explode( ",", $categories_by_film);

foreach($cats as $cat){
  $exist = false;
  $cat = trim($cat);
  
  foreach($genres as $genre){
    if($genre==$cat){
      $exist=true;
    }
  }
  if($exist==false){
    $genres[$cat] = $cat;
  }
}
}
// debugg($genres);




include('inc/header.php'); ?>

<div class="search-form">
  <input type="submit" name="search" value="rechercher">
  <input type="text" name="search-content" value="">
  <input type="button" name="filter" value="filtres">
</div>

<div class="hidden options">
  <label for="erase">Tout effacer</label>
  <input type="checkbox" name="all" value="">

  <label for="categories">Categories</label>

    <?php foreach($genres as $genre){ ?>

      <label for="category"><?php echo $genre ?></label>
      <input type="checkbox" name="<?php echo $genre ?>" value="">

  <?php  } ?>






</div>

<?php
function afficherImage($movie) {
  echo '<a href="detail.php?id= '.$movie['id'].'"><img class="vignette" src="inc/img/posters/'.$movie['id'].'.jpg"  />' ;
}

include('inc/footer.php');

 ?>
