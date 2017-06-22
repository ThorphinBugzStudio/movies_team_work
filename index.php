<?php
if(file_exists('inc/pdo.php')){
  include('inc/pdo.php');
} else {
  include('inc/pdo-thorphin.php');
}
include('inc/function.php');

// requete pour afficher les genres années popularités

$sql= "SELECT genres,year,popularity FROM movies_full";

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

// foreach pour afficher les années
$prods = array();

foreach($films as $film){

$year_by_film = $film['year'];

$years = explode( ",", $year_by_film);


foreach($years as $year){
  $exist = false;
  $year = trim($year);

  foreach($prods as $prod){
    if($prod == $year){
      $exist = true;
      //  die('here');
    }
  }
  if($exist == false){

    $prods[$year] = $year;
  }
}
}



$title = 'Accueil';

// requete pour afficher les 10 premiers films

$sql = "SELECT id FROM movies_full ORDER BY RAND() LIMIT 10 ";
$query = $pdo->prepare($sql);
        $query->execute();
        $movies = $query->fetchAll();

//requete de recherche avec options

if(!empty($_POST['submit']))
{
  $search_content = trim(strip_tags($_POST['search-content']));
  $options = trim(strip_tags($_POST['category']));

}

$sql = "SELECT * FROM movies_full WHERE 1=1 AND ";

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
  <form class="" action="index.html" method="post">

    <input type="submit" name="submit" value="rechercher">
    <input type="text" name="search-content" value="">
    <input id="filter" type="button" name="filter" value="filtres">

    <div id="options" class="hidden">
      <label for="erase">Tout effacer</label>
      <input id="erase-all" type="checkbox" name="erase" value="">

      <label for="categories">Categories</label>

      <?php foreach($genres as $genre){ ?>

        <label for="category"><?php echo $genre ?></label>
        <input class="category-box" type="checkbox" name="category" value="">

      <?php  } ?>

      <?php foreach($prods as $prod){ ?>

        <label for="years"><?php echo $prod ?></label>
        <input class="category-box" type="checkbox" name="year-of-prod" value="">

    <?php  } ?>

      <label for="pops">Popularités</label>
      <label for="popularities">10 à 25</label>
      <input class="category-box" type="checkbox" name="popularities" value="">
      <label for="popularities">26 à 50</label>
      <input class="category-box" type="checkbox" name="popularities" value="">
      <label for="popularities">51 à 75</label>
      <input class="category-box" type="checkbox" name="popularities" value="">
      <label for="popularities">76 à 100</label>
      <input class="category-box" type="checkbox" name="popularities" value="">

      </div>
  </form>
</div>


<?php
function afficherImage($movie) {
  echo '<a href="detail.php?id= '.$movie['id'].'"><img class="vignette img-fluid" src="inc/img/posters/'.$movie['id'].'.jpg"  />' ;
}

include('inc/footer.php');

 ?>
