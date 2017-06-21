<?php
include('inc/pdo.php');

if(!empty($_GET['slug'])) {
  $slug = $_GET['slug'];
}

$replace = str_replace ( '-', ' ', $slug);
$dateprod = substr($replace, -4);
$title = substr($replace, 0, -4);

$sql = "SELECT * FROM movies_full WHERE title = :title && year = :dateprod ";
$query = $pdo->prepare($sql);
$query->bindValue(':title',$title, PDO::PARAM_STR);
$query->bindValue(':dateprod',$dateprod, PDO::PARAM_STR);
        $query->execute();
        $movies = $query->fetchAll();


foreach ($movies as $movie) {

    echo '<img class="vignette" src="inc/img/posters/'.$movie['id'].'.jpg" />';
    echo '<p> titre: '.$movie['title'].'</p>';
    echo '<p> années de parution: '.$movie['year'].'</p>';
    echo '<p> directors: '.$movie['directors'].'</p>';
    echo '<p> genre: '.$movie['genres'].'</p>';
    echo '<p> résumé: '.$movie['plot'].'</p>';
    echo '<p> casting: '.$movie['cast'].'</p>';
    echo '<p> auteurs: '.$movie['writers'].'</p>';
    echo '<p> durée: '.$movie['runtime'].' minutes'.'</p>';
    echo '<p> classification du film: '.$movie['mpaa'].'</p>';
    echo '<p> note: '.$movie['rating'].'/100'.'</p>';
    echo '<p> popularité: '.$movie['popularity'].'</p>';

}
?>
