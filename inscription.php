<?php
/***********************
**   INSCRIPTION.PHP  **
***********************/

   // Fichiers inclus + initialisation connection à la bdd
   include_once('./inc/required.php');

   // Titre Page
   $title = 'UserLog - Inscription';

   //Tableau d'erreur Formulaire -- Tests ok
   $errors = array();
   $successForm = false;

   // Gestion des erreurs de formulaire
   if(!empty($_POST['submitForm']))
   {
      // debug($_POST, '$_POST');

      // Redirige vers index.php si bouton annuler pressé
      if ($_POST['submitForm'] == 'Annuler')
      {
         header('Location: ./index.php');
         exit;
      }

      // Securité
      $userPseudo = trim(strip_tags($_POST['userPseudo']));
      $userEmail = trim(strip_tags($_POST['userEmail']));
      $userPassword = trim(strip_tags($_POST['userPassword']));
      $userPasswordConfirm = trim(strip_tags($_POST['userPasswordConfirm']));

      // PSEUDO
      $errors += testMinMaxNbCar($userPseudo, 6, 100, 'ErrPseudo');
      // pas d'espaces dans pseuso
      if (stripos($userPseudo, ' '))
      {
         $errors['ErrPseudo'] = 'Merci de ne pas inclure d\'espaces dans le pseudo.';
      }
      elseif (empty($errors['ErrPseudo']))
      {
         // Test unicité pseudo
         $sql = "SELECT COUNT(*) FROM users WHERE pseudo = :pseudo";
         $query = $pdo->prepare($sql);
         $query->bindValue(':pseudo', $userPseudo, PDO::PARAM_STR);
         $query->execute();
         $result = $query->fetchColumn();
         if ($result != 0)
         {
            $errors['ErrPseudo'] = 'Votre pseudonyme existe déjà. Merci d\'en changer.';
         }
      }

      // MAIL
      $errors += testMinMaxNbCar($userPseudo, 6, 100, 'ErrMail');
      // test is email
      if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL))
      {
         $errors['ErrMail'] = 'Merci de renseigner une adresse mail valide.';
      }
      elseif (empty($errors['ErrMail']))
      {
         // Test unicité email
         $sql = "SELECT id FROM users WHERE email = :email";
         $query = $pdo->prepare($sql);
         $query->bindValue(':email', $userEmail, PDO::PARAM_STR);
         $query->execute();
         $result = $query->fetch();
         if (!empty($result))
         {
            $errors['ErrMail'] = 'Votre email existe déjà. Merci d\'en changer.';
         }
      }

      // PASSWORD
      $errors += testMinMaxNbCar($userPassword, 6, 255, 'ErrPassword');
      // test si password = confirmation password
      if ($userPassword != $userPasswordConfirm)
      {
         $errors['ErrPassword'] = 'Erreur sur votre mot de passe.';
      }

      //Formulaire ok -> insertion bdd + success
      if (count($errors) == 0)
      {
         // Generation token
         $token = tokenGen(100);
         // debug($token,'$token');

         $sql = "INSERT INTO users (pseudo, email, password, token, created_at, rule)
            VALUES (:pseudo, :email, :password, '$token', NOW(), 'user')";
         $query = $pdo->prepare($sql);
         $query->bindValue(':pseudo', $userPseudo, PDO::PARAM_STR);
         $query->bindValue(':email', $userEmail, PDO::PARAM_STR);
         $query->bindValue(':password', passWash($userPassword), PDO::PARAM_STR);

         $query->execute();

         $successForm = true;
      }

   }

?>

<?php include('./inc/header.php'); ?>

<!-- Formulaire inscription -->

<!-- INSCRIPTION D UN UTILISATEUR -->
<div class="container-fluid row justify-content-center">

   <!-- Affichage formulaire si formulaire mal ou non rempli -->
   <?php if (!$successForm)
   { ?>

      <form class="newPost mt-2" action="" method="post">
        <div class="page_title row">
          <i class="fa fa-user-plus" aria-hidden="true"></i>
          <h2 class="my-auto">Inscription</h2>
        </div>
        <div class="inscription_content">
          <label class="" for="userPseudo">Pseudo :</label>
          <div class="alert-warning"><?php if (!empty($errors['ErrPseudo'])) {echo $errors['ErrPseudo'].'<br />';} ?></div>
          <input type="text" name="userPseudo" value="<?php if (!empty($_POST['userPseudo'])) {echo $_POST['userPseudo'];} ?>" size="100"><br />

          <div class="spacer-y-10"></div>

          <label class="" for="userEmail">Email :</label>
          <div class="alert-warning"><?php if (!empty($errors['ErrMail'])) {echo $errors['ErrMail'].'<br />';} ?></div>
          <input type="email" name="userEmail" value="<?php if (!empty($_POST['userEmail'])) {echo $_POST['userEmail'];} ?>" size="100"><br />

          <div class="spacer-y-10"></div>

          <label class="" for="userPassword">Mot de passe :</label>
          <div class="alert-warning"><?php if (!empty($errors['ErrPassword'])) {echo $errors['ErrPassword'].'<br />';} ?></div>
          <input type="password" name="userPassword" value="<?php if (!empty($_POST['userPassword'])) {echo $_POST['userPassword'];} ?>" size="100"><br />

          <div class="spacer-y-10"></div>

          <div><label class="" for="userPasswordConfirm">Confirmation mot de passe :</label></div>
          <input type="password" name="userPasswordConfirm" value="<?php if (!empty($_POST['userPasswordConfirm'])) {echo $_POST['userPasswordConfirm'];} ?>" size="100"><br />

          <div class="btn_under_form">
            <input type="submit" name="submitForm" value="S'inscrire" class="btn_validate">
            <input type="submit" name="submitForm" value="Annuler" class="btn_cancel">
          </div>
        </div>

      </form>
   <?php } else { ?>
   <!-- sinon message form ok + bouton lien vers index -->
      <div class="row col-5 mt-2 justify-content-center">
         <h3 class="alert alert-success w-100 text-center">Nouvel utilisateur enregistré, merci !</h3>
         <a href="inscription.php"><button type="button" class="btn btn-info">Retour</button></a>
      </div>
   <?php } ?>

</div>

<?php include('./inc/footer.php');
