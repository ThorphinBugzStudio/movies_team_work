<?php
include_once('./inc/required.php');
include('inc/header.php');
session_start();

  if(!empty($_GET['slug'])) {
    $slug = $_GET['slug'];

    $sql = "SELECT * FROM movies_full WHERE slug = :slug";
    $query = $pdo->prepare($sql);
    $query->bindValue(':slug',$slug, PDO::PARAM_STR);
    $query->execute();
    $movie = $query->fetch();

    if (empty($movie)){
      header('Location: redirection404.php');
    } else {
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
  } else {
   header('Location: redirection404.php');
  }

if ( !empty($_POST['btn-sub']) ) {

setcookie('favoris', $movie['id'] , (time() + 3600));

    header('Location: favoris.php');
}



?>
<div class="rating rating2">
  <a href="note.php?id=<?php echo $movie['id'] ?>&note=1&slug=<?php echo $slug ?>" title="Give 1 stars">★</a>
    <a href="note.php?id=<?php echo $movie['id'] ?>&note=2&slug=<?php echo $slug ?>" title="Give 2 stars">★</a>
      <a href="note.php?id=<?php echo $movie['id'] ?>&note=3&slug=<?php echo $slug ?>" title="Give 3 stars">★</a>
        <a href="note.php?id=<?php echo $movie['id'] ?>&note=4&slug=<?php echo $slug ?>" title="Give 4 stars">★</a>
          <a href="note.php?id=<?php echo $movie['id'] ?>&note=5&slug=<?php echo $slug ?>" title="Give 5 stars">★</a>

</div>

<form action="" method="POST">
    <input type="submit" name="btn-sub" class="btn btn-primary" value="favoris" /><br>
</form>


<!-- Facebook -->
<!-- Include the SDK JavaScript on your page once, ideally right after the opening <body> tag. -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.9";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Placez ce code où vous voulez que le plug-in apparaisse sur votre page. -->
  <div class="fb-share-button" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse">Partager</a></div>

<!-- twitter -->
<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

<!-- linkedin -->
<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: fr_FR</script>
<script type="IN/Share"></script>

<!-- google + -->
<!-- Placez cette balise où vous souhaitez faire apparaître le gadget bouton "Partager". -->
<div class="g-plus" data-action="share" data-annotation="none"></div>

<!-- Placez cette ballise après la dernière balise Partager. -->
<script type="text/javascript">
  window.___gcfg = {lang: 'fr'};

  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>


<?php include('inc/footer.php'); ?>
