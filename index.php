<?php
/************
* INDEX.PHP *
************/

// Gestion User session.
session_start();

// Fichiers inclus + initialisation connection à la bdd
include_once('./inc/required.php');

// Verif cookie utilisateur se souvenir de moi
isKnown();

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
  $ratings = ($_POST['ratings']);




   $sql = "SELECT * FROM movies_full WHERE 1=1 ";

   if(!empty($category)){
     foreach ($category as $cate ) {
       $sql .= "AND genres = '$cate' ";
     }
   }

   if(!empty($year_of_prod))
   {
     foreach($year_of_prod as $year)
     {

         if(($year > 1899) && ($year < 1925) ){
           $sql .= "AND year BETWEEN '1900' AND '1924' ";
         }
         if(($year >= 1925) && ($year < 1950)){
           $sql .= "AND year BETWEEN '1925' AND '1949' ";
         }

         if(($year >= 1950) && ($year < 1975)){
           $sql .= "AND year BETWEEN '1950' AND '1974' ";
         }

         if(($year >= 1975) && ($year < 2000)){
           $sql .= "AND year BETWEEN '1975' AND '1999' ";
         }

         if(($year >= 2000) && ($year < 2017)){
           $sql .= "AND year BETWEEN '2000' AND '2017' ";
         }
       }
   }

   if(!empty($ratings))
   {
     foreach($ratings as $rate)
     {
       if(($rate === '0 à 25'))
       {
         $sql .= "AND popularity BETWEEN '0' AND '25' ";
       }
       if(($rate === '25 à 50'))
       {
         $sql .= "AND popularity BETWEEN '25' AND '50' ";
       }
       if(($rate === '50 à 75'))
       {
         $sql .= "AND popularity BETWEEN '50' AND '75' ";
       }
       if(($rate === '75 à 100'))
       {
         $sql .= "AND popularity BETWEEN '75' AND '100' ";
       }
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
          //  debugg($sql);
          //  die('here');
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
            <?php

            $annees = array(
              '1900/1925' => '1900 à 1925',
              '1925/1950' => '1925 à 1950',
              '1950/1975' => '1950 à 1975',
              '1975/2000' => '1975 à 2000',
              '2000/2017' => '2000 à 2017'
            );



              foreach($annees as $annee => $key)
              { ?>

                <li>

                <label for="years"><input class="category-box" type="checkbox" name="year-of-prod[]" value="<?php echo $key ?>"><?php echo $key ?></label>
              </li>

            <?php
              } ?>
          </ul>

          <!-- SOUS-MENU : Popularité -->
          <div class="sub-category">
            <i class="fa fa-angle-down float-right btn_filter" aria-hidden="true"></i>
            <h3>Popularité</h3>
          </div>

          <label for="pops">Popularités</label>

          <?php $popus =array(

                '0 à 25' => '0 à 25',
                '25 à 50' => '25 à 50',
                '50 à 75' => '50 à 75',
                '75 à 100' => '75 à 100'
          );
          foreach($popus as $popu)
          { ?>
            <label for="ratings"><?php echo $popu ?></label>
            <input class="category-box" type="checkbox" name="ratings[]" value="<?php echo $popu ?>">

        <?php  } ?>

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
