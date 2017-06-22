<?php
/************
* INDEX.PHP *
************/

// Gestion User session.
session_start();

// Fichiers inclus + initialisation connection à la bdd
include_once('./inc/required.php');

// requete pour afficher les genres années popularités

$sql= "SELECT genres,year,popularity FROM movies_full";

$query = $pdo->prepare($sql);
$query->execute();
$films = $query->fetchAll();

$title = 'Accueil';

// Gestion des erreurs de formulaire
if(!empty($_POST['submitForm']))
{
   // Redirige vers index.php et reset $_SESSION si bouton pressé
   if ($_POST['submitForm'] == 'Deconnection')
   {
      header('Location: ./deco.php');
      exit;
   }
}

include('./inc/header.php');

// utilisateur connecté
if (isConnected())
{
   echo '<h4>Utilisateur connecté : '.$_SESSION['user']['pseudo'].'</h4>'; ?>
   <?php if ($_SESSION['user']['rule'] == 'admin'): ?>
      <h5>Vous disposez des droits d'administration</h5>
   <?php endif; ?>
   <?php if ($_SESSION['user']['email_verified'] == false): ?>
      <h5>Merci de confirmer votre adresse mail</h5>
   <?php endif; ?>

   <form class="" action="index.php" method="post">

   <input type="submit" name="submitForm" value="Deconnection" class="btn-warning mt-2">

   </form>

   <?php } else { ?>
      <h4>Merci de vous connecter</h4>
   <?php }



// Affichage dynamique des années de prod des films

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


// generation dynamique d'un array contenant les genres de films à partir de la bdd
$genres = array();

foreach($films as $film)
{
   $categories_by_film = $film['genres'];

   $cats = explode( ",", $categories_by_film);

   // test si le genre recuperé existe déjà dans l'array | si non l'ajoute
   // n'ajoute pas en cas de genre vide ou N/A
   foreach($cats as $cat)
   {
      $exist = false;
      $cat = trim($cat);

      foreach($genres as $genre)
      {
         if($genre==$cat)
         {
            $exist=true;
         }
      }
      // cas ou le genre n'existe pas
      if($exist==false)
      {
         $genres[$cat] = $cat;
      }
   }
}

// requete pour afficher les 10 premiers films
$sql = "SELECT id FROM movies_full ORDER BY RAND() LIMIT 10 ";
$query = $pdo->prepare($sql);
        $query->execute();
        $movies = $query->fetchAll();

?>


<!-- //requete de recherche avec options

if(!empty($_POST['submit']))
{
  $search_content = trim(strip_tags($_POST['search-content']));
  $options = trim(strip_tags($_POST['category']));

}

$sql = "SELECT * FROM movies_full WHERE 1=1 AND "; -->



<div class="container-fluid row justify-content-center mx-auto">
  <div class="movies_grid row mx-auto">
    <?php
    foreach ($movies as $movie) {
      $id = $movie['id'];
      afficherImage($movie);
    } ?>
</div>

<div class="container row justify-content-center mx-auto">
  <a class="btn btn_more_movies" href="index.php" role="button">+ de films !</a>
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





      </div>
  </form>
</div>


<?php
function afficherImage($movie) {
  echo '<a href="detail.php?id= '.$movie['id'].'"><img class="vignette" src="inc/img/posters/'.$movie['id'].'.jpg"  /></a>' ;
}

include('./inc/footer.php'); ?>
