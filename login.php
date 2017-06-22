<?php
/*********************
**  CONNECTION.PHP  **
*********************/

// Gestion User session.
session_start();
$_SESSION['user'] = '';

// Fichiers inclus + initialisation connection à la bdd
include_once('./inc/required.php');

// Titre Page
$title = 'UserLog - Login';

//Tableau d'erreur Formulaire -- Tests ok
$errors = array();
$successForm = false;

// Gestion des erreurs de formulaire
if(!empty($_POST['submitForm']))
{

   // Redirige vers index.php si bouton annuler pressé
   if ($_POST['submitForm'] == 'Annuler')
   {
      header('Location: ./index.php');
      exit;

   }
   elseif ($_POST['submitForm'] == 'Password Lost')
   {
      header('Location: ./lost_pw.php');
      exit;
   }

   // Securité
   $userPseudo = trim(strip_tags($_POST['userPseudo']));
   $userPassword = trim(strip_tags($_POST['userPassword']));

   // PSEUDO
   $errors += testMinMaxNbCar($userPseudo, 6, 100, 'ErrPseudo');
   // pas d'espaces dans pseuso
   if (stripos($userPseudo, ' '))
   {
      $errors['ErrPseudo'] = 'Merci de ne pas inclure d\'espaces dans votre pseudo ou email.';
   }
   // PASSWORD
   $errors += testMinMaxNbCar($userPassword, 6, 255, 'ErrPassword');

   // Formulaire ok -> Login + success
   if (count($errors) == 0)
   {

      // test si pseudo ou adresse mail existent ET mot de passe ok
      $sql = "SELECT id, pseudo, email, email_verified, token, password, rule FROM users WHERE (pseudo = :pseudo OR email = :pseudo)";
      $query = $pdo->prepare($sql);
      $query->bindValue(':pseudo', $userPseudo, PDO::PARAM_STR);
      $query->execute();
      $result = $query->fetch();

      if ($result != '')
      {
         // Le pseudo ou l email existe -> Verification mot de passe
         if (passWash($userPassword, 'verify', $result['password']))
         {
            // Le mot de passe est correct
            // Ouverture session
            $_SESSION['user'] = array(
               'id'       => $result['id'],
               'pseudo'   => $result['pseudo'],
               'email'    => $result['email'],
               'email_verified'    => $result['email_verified'],
               'token'    => $result['token'],
               'rule'     => $result['rule'],
               'ip'       => get_ip()
            );

            $successForm = true;
         }
         else
         {
            $errors['ErrConnect'] = 'Votre mot de passe semble erroné. Merci de verifier vos identifiants';
         }
      }
      else
      {
         $errors['ErrConnect'] = 'Nous n\'avons pas réussi à identifier votre pseudonyme / adresse mail. Merci de verifier vos identifiants';
      }
   }

}

?>

<?php include('./inc/header.php'); ?>

<!-- Main -->

<!-- Formulaire connection -->

<!-- CONNECTION D UN UTILISATEUR -->
<div class="row justify-content-center">

   <!-- Affichage formulaire si formulaire mal ou non rempli -->
   <?php if (!$successForm)
   { ?>

      <form class="newPost mt-2" action="#" method="post">
         <h3>Login :</h3>
         <div class="alert-danger"><?php if (!empty($errors['ErrConnect'])) {echo $errors['ErrConnect'].'<br />';} ?></div>

         <label class="mt-2" for="userPseudo">Pseudo ou adresse mail :</label>
         <div class="alert-warning"><?php if (!empty($errors['ErrPseudo'])) {echo $errors['ErrPseudo'].'<br />';} ?></div>
         <input type="text" name="userPseudo" value="<?php if (!empty($_POST['userPseudo'])) {echo $_POST['userPseudo'];} ?>" size="100"><br />

         <label class="mt-2" for="userPassword">Mot de passe :</label>
         <div class="alert-warning"><?php if (!empty($errors['ErrPassword'])) {echo $errors['ErrPassword'].'<br />';} ?></div>
         <input type="password" name="userPassword" value="<?php if (!empty($_POST['userPassword'])) {echo $_POST['userPassword'];} ?>" size="100"><br />

         <input type="submit" name="submitForm" value="Login" class="btn-success mt-2">
         <input type="submit" name="submitForm" value="Annuler" class="btn-warning mt-2">
         <input type="submit" name="submitForm" value="Password Lost" class="btn-danger mt-2">

      </form>
   <?php } else { ?>
   <!-- sinon message form ok + bouton lien vers index -->
      <div class="row col-5 mt-2 justify-content-center">
         <?php if ($_SESSION['user']['email_verified'] == false)
         {
            $lien = './verif_email.php?email='.urlencode($_SESSION['user']['email']).'&token='.urlencode($_SESSION['user']['token']); ?>
            <h5>Merci de cliquer sur le lien contenu dans l'email que nous vous avons transmis pour finaliser votre inscription</h5>
            <!-- affichage du lien qui serai normalement envoyé à l'utilisateur avec un Mail -->
            <a class="col-12 text-center" href="<?php echo $lien; ?>"><?php echo $lien; ?></a>
         <?php }
         else
         { ?>
            <h3 class="alert alert-success w-100 text-center">Felicitations <?php echo $result['pseudo']; ?>. vous êtes connecté</h3>
         <?php } ?>

         <a href="./index.php"><button type="button" class="btn btn-info">Ok</button></a>
      </div>
   <?php } ?>

</div>


<?php include('./inc/footer.php');
