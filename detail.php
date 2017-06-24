<?php
session_start();

include_once('./inc/required.php');
include('inc/header.php');

  if(!empty($_GET['slug'])) {
    $slug = $_GET['slug'];

    $sql = "SELECT * FROM movies_full WHERE slug = :slug";
    $query = $pdo->prepare($sql);
    $query->bindValue(':slug',$slug, PDO::PARAM_STR);
    $query->execute();
    $movie = $query->fetch();

    if (empty($movie)){
      header('Location: redirection404.php');
    } else { ?>

      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-sm-12 col-md-7 col-lg-8 col-xl-8">
            <!-- Titre de la page : Detail -->
            <div class="page_title row">
              <i class="fa fa-video-camera" aria-hidden="true"></i>
              <h2 class="my-auto"><?php echo $movie['title']; ?></h2>
            </div>
            <!-- Contenu de la page -->
            <div class="movies_grid search-form w-100 detail_content">
              <div class="column">
                <img class="vignette vignette2" src="inc/img/posters/<?php echo$movie['id']; ?>.jpg">
                <div class="row py-1">
                  <h3 class="title-detail">Année de production :</h3>
                  <p><?php echo $movie['year']; ?></p>
                </div>
                <div class="row py-1">
                  <h3 class="title-detail">Directeur(s) :</h3>
                  <p><?php echo $movie['directors']; ?></p>
                </div>
                <div class="row py-1">
                  <h3 class="title-detail">Genre(s) :</h3>
                  <p><?php echo $movie['genres']; ?></p>
                </div>
                <div class="row py-1">
                  <h3 class="title-detail">Résumé :</h3>
                  <p><?php echo $movie['plot']; ?></p>
                </div>
                <div class="row py-1">
                  <h3 class="title-detail">Casting :</h3>
                  <p><?php echo $movie['cast']; ?></p>
                </div>
                <div class="row py-1">
                  <h3 class="title-detail">Auteur(s) :</h3>
                  <p><?php echo $movie['writers']; ?></p>
                </div>
                <div class="row py-1">
                  <h3 class="title-detail"><i class="fa fa-clock-o" aria-hidden="true"></i> : </h3>
                  <p><?php echo $movie['runtime']; ?> minutes</p>
                </div>
                <div class="row py-1">
                  <h3 class="title-detail">Classification du film :</h3>
                  <p><?php echo $movie['mpaa']; ?></p>
                </div>
                <div class="row py-1">
                  <h3 class="title-detail">Note :</h3>
                  <p><?php echo $movie['rating']; ?> / 100</p>
                </div>
                <div class="row py-1">
                  <h3 class="title-detail">Popularité :</h3>
                  <p><?php echo $movie['popularity']; ?></p>
                </div>

                <hr>

                <div class="row justify-content-center">
                  <form action="" method="POST">
                    <div class="row">
                      <input type="submit" name="btn-sub" class="btn_cancel" value="Favoris" style="margin-right: 6px;"/><br>
                      <!-- Bouton rapide pour test user_notes.php -->
                      <a href="user_notes.php"><button type="button" class="btn_cancel">Vos notes</button></a>
                    </div>
                  </form>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    <?php }
  } else {
   header('Location: redirection404.php');
  }

if ( !empty($_POST['btn-sub']) ) {
  setcookie('favoris', $movie['id'] , (time() + 3600));
  header('Location: favoris.php');
} ?>

<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-sm-12 col-md-7 col-lg-8 col-xl-8">
      <div class="rating rating2 search-form detail_content">
        <div class="row">
          <h3 class="title-detail" style="margin-right: 6px;">Notez ce film : </h3>
          <a href="note.php?id=<?php echo $movie['id'] ?>&note=1&slug=<?php echo $slug ?>" title="Give 1 stars"><i class="fa fa-star fa-star-note" aria-hidden="true"></i></a>
          <a href="note.php?id=<?php echo $movie['id'] ?>&note=2&slug=<?php echo $slug ?>" title="Give 2 stars"><i class="fa fa-star fa-star-note" aria-hidden="true"></i></a>
          <a href="note.php?id=<?php echo $movie['id'] ?>&note=3&slug=<?php echo $slug ?>" title="Give 3 stars"><i class="fa fa-star fa-star-note" aria-hidden="true"></i></a>
          <a href="note.php?id=<?php echo $movie['id'] ?>&note=4&slug=<?php echo $slug ?>" title="Give 4 stars"><i class="fa fa-star fa-star-note" aria-hidden="true"></i></a>
          <a href="note.php?id=<?php echo $movie['id'] ?>&note=5&slug=<?php echo $slug ?>" title="Give 5 stars"><i class="fa fa-star fa-star-note" aria-hidden="true"></i></a>
        </div>

        <hr>

        <div class="row justify-content-center">
          <div class="mx-1">
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
          </div>

          <div class="mx-1">
            <!-- Twitter -->
            <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
          </div>

          <div class="mx-1">
            <!-- Linkedin -->
            <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: fr_FR</script>
            <script type="IN/Share"></script>
          </div>

          <div class="mx-1">
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
          </div>
        </div>

      </div>
    </div>
  </div>
</div>





<?php include('inc/footer.php'); ?>
