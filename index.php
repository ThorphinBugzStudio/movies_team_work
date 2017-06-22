<?php
if(file_exists('inc/pdo.php')){
  include('inc/pdo.php');
} else {
  include('inc/pdo-thorphin.php');
}
include('inc/function.php');


// Requête pour afficher les catégories
$sql= "SELECT genres FROM movies_full";

$query = $pdo->prepare($sql);
$query->execute();
$films = $query->fetchAll();

$genres = array(
);

// Boucle qui cherchera tous les films dans la BDD
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

// Requête pour afficher les 12 premiers films
$sql = "SELECT id FROM movies_full ORDER BY RAND() LIMIT 12";
$query = $pdo->prepare($sql);
        $query->execute();
        $movies = $query->fetchAll();

include('inc/header.php'); ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-10 col-lg-10 col-sm-12">
      <div class="movies_grid">
        <?php
        foreach ($movies as $movie) {
          $id = $movie['id'];
          afficherImage($movie);
        }
        ?>
      </div>
      <!-- Bouton "+ de films !" -->
      <div class="justify-content-center btn_more">
        <a class="btn btn_more_movies" href="index.php" role="button">+ de films !</a>
      </div>

    </div>

    <div class="col-xl-2 col-lg-2 col-sm-12">
      <!-- WIDGET : Rechercher -->
      <div class="search-form">
        <div class="widget_title row">
          <i class="fa fa-angle-double-right" aria-hidden="true"></i>
          <h2 class="my-auto">Rechercher</h2>
        </div>

        <div class="input-group">
          <input type="text" class="form-control" name="search-content" placeholder="Rechercher...">
          <span class="input-group-btn">
            <button class="btn btn-secondary" type="button" name="search"><i class="fa fa-search" aria-hidden="true"></i></button>
          </span>
        </div>
        <!-- SOUS-MENU : Filtres -->
        <div class="sub-category">
          <i class="fa fa-angle-down float-right btn_filter" aria-hidden="true"></i>
          <h3>Filtres</h3>
        </div>
        <div class="hidden options">
          <label for="erase" class="delete_all">Tout effacer</label>
          <input type="checkbox" name="all" value="" class="delete_all">
          <ul class="search_category">
            <?php foreach($genres as $genre){ ?>
              <li>
                <label for="category"><input type="checkbox" name="<?php echo $genre ?>" value=""/><?php echo $genre ?></label>

              </li>
              <?php  } ?>
            </ul>
          </div>

          <!-- SOUS-MENU : Année -->
          <div class="sub-category">
            <i class="fa fa-angle-down float-right btn_filter" aria-hidden="true"></i>
            <h3>Années</h3>
          </div>

          <!-- SOUS-MENU : Popularité -->
          <div class="sub-category">
            <i class="fa fa-angle-down float-right btn_filter" aria-hidden="true"></i>
            <h3>Popularité</h3>
          </div>

        </div>
    </div>
  </div>
</div>

<?php
function afficherImage($movie) {
  echo '<a href="detail.php?id= '.$movie['id'].'"><img class="vignette img-fluid" src="inc/img/posters/'.$movie['id'].'.jpg"  />' ;
}

include('inc/footer.php');

 ?>
