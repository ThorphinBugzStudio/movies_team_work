<?php
if(file_exists('inc/pdo.php')){
  include('inc/pdo.php');
} else {
  include('inc/pdo-thorphin.php');
}
include('inc/function.php');

// requete pour afficher les catégories

$sql= "SELECT genres FROM movies_full";

$query = $pdo->prepare($sql);
$query->execute();
$films = $query->fetchAll();


$genres = array(

);

foreach($films as $film){
$categories_by_film = $film['genres'];

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





$title = 'Accueil';

// requete pour afficher les 10 premiers films

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
  echo '<a href="detail.php?id= '.$movie['id'].'"><img class="vignette img-fluid" src="inc/img/posters/'.$movie['id'].'.jpg"  />' ;
}

include('inc/footer.php');

 ?>
