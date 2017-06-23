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

// Requête pour afficher les 20 premiers films
$sql = "SELECT * FROM movies_full ORDER BY RAND() LIMIT 20";
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
  //debugg($_POST['ratings']);



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
           $sql .= "AND year BETWEEN '1900' AND '1925'";
         }
         if(($year >= 1925) && ($year < 1950)){
           $sql .= "AND year BETWEEN '1925' AND '1949'";
         }

         if(($year >= 1950) && ($year < 1975)){
           $sql .= "AND year BETWEEN '1950' AND '1974'";
         }

         if(($year >= 1975) && ($year < 2000)){
           $sql .= "AND year BETWEEN '1975' AND '1999'";
         }

         if(($year >= 2000) && ($year < 2017)){
           $sql .= "AND year BETWEEN '2000' AND '2017'";
         }
       }
   }

   if(!empty($ratings))
   {
     foreach($ratings as $rate)
     {
       if(($rate >= 0) && ($rate <= 25))
       {
         $sql .= "AND rating BETWEEN '0' AND '25' ";
        //  debugg($sql);
        //  die('here');
       }
       if(($rate >= 25) && ($rate <= 50))
       {
         $sql .= "AND rating BETWEEN '25' AND '50' ";
        //  debugg($sql);
        //  die('here');
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
  <div class="row justify-content-center">
    <div class="col-sm-12 col-md-7 col-lg-8 col-xl-8">
      <!-- Titre : Accueil -->
      <div class="page_title row">
        <i class="fa fa-home" aria-hidden="true"></i>
        <h2 class="my-auto">Accueil</h2>
      </div>
      <div class="movies_grid search-form">
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
        <!-- Bouton "+ de films !" -->
        <div class="justify-content-center btn_more">
          <a class="btn btn_more_movies" href="index.php" role="button">+ de films !</a>
        </div>
      </div>

    </div>

    <div class="widget col-sm-12 col-md-4 col-lg-3 col-xl-3">
      <!-- WIDGET : Rechercher -->
      <div class="search-form">
        <div class="widget_title row">
          <i class="fa fa-angle-double-right" aria-hidden="true"></i>
          <h2 class="my-auto">Rechercher</h2>
        </div>

          <form class="" action="" method="post">
            <div class="container-fluid row justify-content-center mx-auto input-group">
              <input type="text" class="form-control" name="search-content" placeholder="Rechercher...">
              <input class="btn btn-secondary btn-search" type="submit" name="submit" value="&#xf002">
            </div>

            <div class="row justify-content-center">
              <label for="erase" class="delete_all"><input class="checkbox" type="checkbox" id="erase-all" name="erase" value="" class="delete_all">Réinitialiser</label>
            </div>

            <!-- SOUS-MENU : Filtres -->
            <div class="sub-category">
              <i class="fa fa-angle-down float-right btn_filter" id="filter" aria-hidden="true"></i>
              <h3>Filtres</h3>
            </div>
            <div class="hidden" id="options">
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
                <i class="fa fa-angle-down float-right btn_filter" id="years" aria-hidden="true"></i>
                <h3>Années</h3>
              </div>
              <div class="hidden" id="options2">
                <ul class="search_category">
                <?php
                $annees = array(
                  '1900/1925' => '1900 à 1925',
                  '1925/1950' => '1925 à 1950',
                  '1950/1975' => '1950 à 1975',
                  '1975/2000' => '1975 à 2000',
                  '2000/2017' => '2000 à 2017'
                );

                  foreach($annees as $annee => $key) { ?>
                    <li>
                      <label for="years"><input class="category-box" type="checkbox" name="year-of-prod[]" value="<?php echo $key ?>"><?php echo $key ?></label>
                    </li>
                <?php } ?>
                </ul>
              </div>

              <!-- SOUS-MENU : Popularité -->
              <div class="sub-category">
                <i class="fa fa-angle-down float-right btn_filter" id="popularity" aria-hidden="true"></i>
                <h3>Popularité</h3>
              </div>
              <div class="hidden" id="options3">
                <ul class="search_category">
              <!--<label for="pops">Popularités</label>-->

              <?php $popus = array(
                    '0 à 25' => '0 à 25',
                    '25 à 50' => '25 à 50',
                    '50 à 75' => '50 à 75',
                    '75 à 100' => '75 à 100'
              );

              foreach($popus as $popu) { ?>
                  <li>
                    <label for="ratings"><input class="category-box" type="checkbox" name="ratings[]" value="<?php echo $popu ?>"><?php echo $popu ?></label>
                  </li>
            <?php } ?>
              </ul>
              </div>
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
