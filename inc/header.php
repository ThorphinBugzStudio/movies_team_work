<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="description" content="Movies Full, est le site de référencement d'une longue liste de films. Découvrez ou redécouvrez les plus grandes oeuvres cinématographique." />
    <meta name="keywords" content="cinéma, cinema, film, films, poster, posters, cinématographie, acteur, acteurs, réalisateur, réalisateurs, producteur, producteurs, vidéo, vidéos, video, videos, movies full, movie, movies, actor, actors, prodducer, producers, character, characters">
    <title>Movies Full » <?php if(empty($title)) { echo ''; } else { echo $title; } ?></title>
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans" rel="stylesheet">
    <link rel="stylesheet" href="./inc/css/font-awesome.css">
    <link rel="stylesheet" href="./inc/css/hover.css">
    <link rel="stylesheet" href="./inc/css/bootstrap.css">
    <link rel="stylesheet" href="./inc/css/style.css">
  </head>
  <body>

    <!-- Bannière pour Inscription/ (Dé)Connexion / Profil utilisateur -->
    <div class="userBanner">
      <div class="mr-auto">
        <?php if(isConnected()) { ?>

          <a href="dashboard.php">
            <i class="fa fa-tachometer" aria-hidden="true"></i>
            <span>Administration</span>
          </a>

          <a href="#">
            <i class="fa fa-user" aria-hidden="true"></i>
            <span><?php echo $_SESSION['user']['pseudo']; ?></span>
          </a>

          <a href="deco.php">
            <i class="fa fa-sign-out" aria-hidden="true"></i>
            <span>Déconnexion</span>
          </a>
        <?php } else { ?>
          <a href="inscription.php">
            <i class="fa fa-user-plus" aria-hidden="true"></i>
            <span>Inscription</span>
          </a>

          <a href="login.php">
            <i class="fa fa-sign-in" aria-hidden="true"></i>
            <span>Connexion</span>
          </a>

      <?php } ?>
      </div>
    </div>

    <!-- Décoration -->
    <div style="height: 2px; background-color: #ffd800;"></div>

    <!-- Titre & logo du site -->
    <div class="mainBanner img-fluid mx-auto">
      <div class="mainHeader py-5">
        <img src="./inc/img/logo.png" alt="Logo" class="mainLogo">
        <h1 class="mainTitle">Movies Full</h1>
      </div>
    </div>

    <!-- Navigation du site -->
    <div class="navbar">
      <div class="mx-auto">
        <nav>
          <a href="index.php">Accueil</a>
          <a href="#">Lien</a>
          <a href="#">Lien</a>
        </nav>
      </div>
    </div>

    <main>
