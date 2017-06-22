<?php
/************
* INDEX.PHP *
************/

// Gestion User session.
session_start();

// Fichiers inclus + initialisation connection à la bdd
include_once('./inc/required.php');
isKnown(); // Verif cookie utilisateur se souvenir de moi
// requete pour afficher les genres années popularités

$sql= "SELECT genres,year,popularity FROM movies_full ORDER BY year ASC";

$query = $pdo->prepare($sql);
$query->execute();
$films = $query->fetchAll();

$title = 'Accueil';
$success = false;

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

// Boucle qui cherchera tous les films dans la BDD
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

// Requête pour afficher les 12 premiers films
$sql = "SELECT * FROM movies_full ORDER BY RAND() LIMIT 12";
$query = $pdo->prepare($sql);
        $query->execute();
        $movies = $query->fetchAll();


//requete de recherche avec options

if(!empty($_POST['submit']))
{

  $search_content = trim(strip_tags($_POST['search-content']));
  $category = $_POST['category'];
  $year_of_prod = $_POST['year-of-prod'];
  $ratings = $_POST['ratings'];
  debugg($category);


   $sql = "SELECT * FROM movies_full WHERE 1=1 ";

   if(!empty($category)){
     foreach ($category as $cate ) {
       $sql .= "AND genres = $cate ";
     }
   }

   if(!empty($year_of_prod)){
     foreach($year_of_prod as $year){
       $sql .= "AND year = $year ";
     }
   }

   if(!empty($ratings)){
     foreach($ratings as $rate){
       $sql .= "AND rating = $rate ";
     }
   }

   if(!empty($search_content)){

     $sql .= "AND title LIKE '%$search_content%'
              OR plot LIKE '%$search_content%'
              OR directors LIKE '%$search_content%'
              OR cast LIKE '%$search_content%'
              OR writers LIKE '%$search_content%'
              ";

   }
           $query = $pdo->prepare($sql);
           $query->execute();
           $results = $query->fetchAll();

           $success = true;

} else {

  //aucun élément de requete
}

?>


<div class="container-fluid">
  <div class="row">
    <div class="col-xl-10 col-lg-10 col-sm-12">
      <div class="movies_grid">
        <?php
        if($success==false){
//Afficher les 10 films au hasard
          foreach ($movies as $movie) {
            $slug = $movie['slug'];
            afficherImage($movie);
          }

        } else {
//Afficher les résultats de recherche
          foreach($results as $result){

            afficherImage($result);
          }
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
          <form class="" action="" method="post">
            <input type="text" class="form-control w-100" name="search-content" placeholder="Rechercher...">
            <span class="input-group-btn">
              <input class="btn btn-secondary" type="submit" name="submit"><i class="fa fa-search" aria-hidden="true"></i>
            </span>
        </div>
        <!-- SOUS-MENU : Filtres -->
        <div class="sub-category">
          <i class="fa fa-angle-down float-right btn_filter" id="filter" aria-hidden="true"></i>
          <h3>Filtres</h3>
        </div>
        <div class="hidden"id="options">
          <label for="erase" class="delete_all">Tout effacer</label>
          <input type="checkbox" id="erase-all" name="erase" value="" class="delete_all">
          <ul class="search_category">
            <?php foreach($genres as $genre){ ?>
              <li>
                <label for="category"><input type="checkbox" class="category-box" name="category[]" value="<?php echo $genre ?>"/><?php echo $genre ?></label>
              </li>
              <?php  } ?>
            </ul>
          </div>

          <!-- SOUS-MENU : Année -->
          <div class="sub-category">
            <i class="fa fa-angle-down float-right btn_filter" aria-hidden="true"></i>
            <h3>Années</h3>
          </div>
          <ul class="search_category">
            <?php foreach($prods as $prod){ ?>
              <li>
                <label for="years"><input class="category-box" type="checkbox" name="year-of-prod[]" value="<?php echo $prod ?>"><?php echo $prod ?></label>
              </li>
            <?php  } ?>
          </ul>

          <!-- SOUS-MENU : Popularité -->
          <div class="sub-category">
            <i class="fa fa-angle-down float-right btn_filter" aria-hidden="true"></i>
            <h3>Popularité</h3>
          </div>
          <label for="pops">Popularités</label>
          <label for="ratings">10 à 25</label>
          <input class="category-box" type="checkbox" name="ratings[]" value="<?php echo '10 à 25' ?>">
          <label for="ratings">26 à 50</label>
          <input class="category-box" type="checkbox" name="ratings[]" value="<?php echo '26 à 50' ?>">
          <label for="ratings">51 à 75</label>
          <input class="category-box" type="checkbox" name="ratings[]" value="<?php echo '51 à 75' ?>">
          <label for="ratings">76 à 100</label>
          <input class="category-box" type="checkbox" name="ratings[]" value="<?php echo '76 à 100' ?>">
        </div>
      </form>
    </div>
  </div>
</div>

<?php
function afficherImage($movie) {
  echo '<a href="detail.php?slug='.$movie['slug'].'"><img class="vignette" src="inc/img/posters/'.$movie['id'].'.jpg"  /></a>' ;
}

include('./inc/footer.php'); ?>
