<?php
/************
* INDEX.PHP *
************/

// Gestion User session.
session_start();

// Fichiers inclus + initialisation connection à la bdd
include_once('./inc/required.php');

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


// requete pour afficher les catégories
$sql = "SELECT genres FROM movies_full";
$query = $pdo->prepare($sql);
$query->execute();
$films = $query->fetchAll();


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
  echo '<a href="detail.php?id= '.$movie['id'].'"><img class="vignette" src="inc/img/posters/'.$movie['id'].'.jpg"  /></a>' ;
}

include('./inc/footer.php'); ?>
