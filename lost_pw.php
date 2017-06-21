<?php
/************************
**  LOST PASSWORD.PHP  **
************************/

   // Fichiers inclus + initialisation connection à la bdd
   include_once('./inc/required.php');

   // Titre Page
   $title = 'UserLog - Lost Password';

   //Tableau d'erreur Formulaire -- Tests ok
   $errors = array();
   $successForm = false;

   //Parametres pour l'url envoyée de recuperation envoyé par mail
   $lien = '';

   // Gestion des erreurs de formulaire
   if(!empty($_POST['submitForm']))
   {
      // Redirige vers index.php si bouton annuler pressé
      if ($_POST['submitForm'] == 'Annuler')
      {
         header('Location: ./index.php');
         exit;
      }

      // Securité
      $userEmail = trim(strip_tags($_POST['userEmail']));

      // EMAIL
      $errors += testMinMaxNbCar($userEmail, 6, 100, 'ErrMail');
      // test is email
      if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL))
      {
         $errors['ErrMail'] = 'Merci de renseigner une adresse mail valide.';
      }
      elseif (empty($errors['ErrMail']))
      {
         // recuperation email et token pour lien à envoyer à l'utilisateur
         $sql = "SELECT email, token FROM users WHERE email = :email";
         $query = $pdo->prepare($sql);
         $query->bindValue(':email', $userEmail, PDO::PARAM_STR);
         $query->execute();
         $user = $query->fetch();

         // Erreur dans le cas ou l'email n'existe pas
         if (empty($user))
         {
            $errors['emailNotExist'] = 'l\'email renseigné n\'existe pas dans notre base de données.';
         }
      }
      // Formulaire ok -> Mail de recuperation de mot de passe (token)
      if (count($errors) == 0)
      {
         $lien = './reinit_pw.php?email='.urlencode($user['email']).'&token='.urlencode($user['token']);

         $successForm = true;
      }
   }
?>

<?php include('./inc/header.php'); ?>

<!-- Main -->

<!-- Formulaire adresse mail -->

<!-- Demande une adresse mail  pour envoi mail de reinitialisation de mot de passe -->
<div class="row justify-content-center">

   <!-- Affichage formulaire si formulaire mal ou non rempli -->
   <?php if (!$successForm)
   { ?>

      <form class="newPost mt-2" action="#" method="post">
         <h3>Pour changer de mot de passe : Merci de saisir votre adresse mail :</h3>
         <div class="alert-danger"><?php if (!empty($errors['emailNotExist'])) {echo $errors['emailNotExist'].'<br />';} ?></div>

         <label class="mt-2" for="userEmail">Email :</label>
         <div class="alert-warning"><?php if (!empty($errors['ErrMail'])) {echo $errors['ErrMail'].'<br />';} ?></div>
         <input type="email" name="userEmail" value="<?php if (!empty($_POST['userEmail'])) {echo $_POST['userEmail'];} ?>" size="100"><br />

         <input type="submit" name="submitForm" value="Envoyer" class="btn-success mt-2">
         <input type="submit" name="submitForm" value="Annuler" class="btn-warning mt-2">

      </form>
   <?php } else { ?>
   <!-- sinon message form ok + bouton lien vers index -->
      <div class="row col-5 mt-2 justify-content-center">
         <h3 class="alert alert-success w-100 text-center">Nous vous avons transmis un mail : Merci de cliquer sur le lien joint afin de réinitialiser votre mot de passe</h3>
         <!-- affichage du lien qui serai normalement envoyé à l'utilisateur avec un Mail -->
         <a class="col-12 text-center" href="<?php echo $lien; ?>"><?php echo $lien; ?></a>

         <a class="mt-2" href="./index.php"><button type="button" class="btn btn-info">Ok</button></a>
      </div>
   <?php } ?>

</div>


<?php include('./inc/footer.php'); ?>
