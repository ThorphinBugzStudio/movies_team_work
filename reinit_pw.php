<?php
/****************************
**   REINITIALISATION.PHP  **
****************************/

   // Fichiers inclus + initialisation connection à la bdd
   include_once('./inc/required.php');

   // Titre Page
   $title = 'UserLog - New password';

   //Tableau d'erreur Formulaire -- Tests ok
   $errors = array();
   $successForm = false;

   //Parametres de l'url receptionnée
   $lienMail = '';
   $lienToken = '';

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
      $userPassword = trim(strip_tags($_POST['userPassword']));
      $userPasswordConfirm = trim(strip_tags($_POST['userPasswordConfirm']));

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
         // Verification get rempli et recuporation email / token
         if(!empty($_GET['email']) && !empty($_GET['token']))
         {
	    		$lienMail = urldecode($_GET['email']);
				$lienToken = urldecode($_GET['token']);

            // Generation token
            $token = tokenGen(100);
            // debug($token,'$token');

            // Verif user exist
            $sqlverif = "SELECT id FROM users WHERE email = :email AND token = :token";
            $stmt = $pdo->prepare($sqlverif);
            $stmt->bindValue(':email', $lienMail);
            $stmt->bindValue(':token', $lienToken);
            $stmt->execute();
            $verifIdExist = $stmt->fetch();

            // if user exist bien dans la table
            // modification de son password et du token
            if (!empty($verifIdExist))
            {
               $id_user = $verifIdExist['id'];
               $update = "UPDATE users SET password = :password, token = :token, modified_at = NOW() WHERE id = $id_user";
               $stmt = $pdo->prepare($update);
               $stmt->bindValue(':password', passWash($userPassword));
               $stmt->bindValue(':token',$token);
               $stmt->execute();
            }

            $successForm = true;
         }
      }

   }

?>

<?php include('./inc/header.php'); ?>

<!-- Formulaire nouveau password + nouveau token -->

<!-- NOUVEAU PASSWORD -->
<div class="row justify-content-center">

   <!-- Affichage formulaire si formulaire mal ou non rempli -->
   <?php if (!$successForm)
   { ?>

      <form class="newPost mt-2" action="" method="post">
         <h3>Nouveau Mot de passe :</h3>

         <label class="mt-2" for="userPassword">Mot de passe :</label>
         <div class="alert-warning"><?php if (!empty($errors['ErrPassword'])) {echo $errors['ErrPassword'].'<br />';} ?></div>
         <input type="text" name="userPassword" value="<?php if (!empty($_POST['userPassword'])) {echo $_POST['userPassword'];} ?>" size="100"><br />

         <div><label class="mt-2" for="userPasswordConfirm">Confirmation mot de passe :</label></div>
         <input type="password" name="userPasswordConfirm" value="<?php if (!empty($_POST['userPasswordConfirm'])) {echo $_POST['userPasswordConfirm'];} ?>" size="100"><br />

         <input type="submit" name="submitForm" value="Enregistré" class="btn-success mt-2">
         <input type="submit" name="submitForm" value="Annuler" class="btn-warning mt-2">

      </form>
   <?php } else { ?>
   <!-- sinon message form ok + bouton lien vers index -->
      <div class="row col-5 mt-2 justify-content-center">
         <h3 class="alert alert-success w-100 text-center">Nouveau mot de passe modifié et enregistré, merci !</h3>
         <a href="index.php"><button type="button" class="btn btn-info">Retour</button></a>
      </div>
   <?php } ?>

</div>

<?php include('./inc/footer.php');
