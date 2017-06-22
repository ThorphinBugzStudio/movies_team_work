<?php
/*********************************************
**   VERIFICATION / CONFIRMATION EMAIL.PHP  **
*********************************************/

   // Gestion User session.
   session_start();

   // Fichiers inclus + initialisation connection à la bdd
   include_once('./inc/required.php');

   // Titre Page
   $title = 'UserLog - Verif email';

   $success = false;

   //Parametres de l'url receptionnée
   $lienMail = '';
   $lienToken = '';

   // Verification get rempli et recuperation email / token
   if(!empty($_GET['email']) && !empty($_GET['token']))
   {
		$lienMail = urldecode($_GET['email']);
		$lienToken = urldecode($_GET['token']);

      // Generation token
      $token = tokenGen(100);
      // debug($token,'$token');

      // Verif user exist et email + token receptionné ok par rapport à ceux de la bdd
      $sqlverif = "SELECT id FROM users WHERE email = :email AND token = :token";
      $stmt = $pdo->prepare($sqlverif);
      $stmt->bindValue(':email', $lienMail);
      $stmt->bindValue(':token', $lienToken);
      $stmt->execute();
      $verifIdExist = $stmt->fetch();

      // if user exist bien dans la table
      // passage statut verification email à true
      if (!empty($verifIdExist))
      {
         $id_user = $verifIdExist['id'];
               $update = "UPDATE users SET email_verified = :e_verif, token = :token WHERE id = $id_user";
               $stmt = $pdo->prepare($update);
               $stmt->bindValue(':e_verif', 1);
               $stmt->bindValue(':token', $token);
               $stmt->execute();

               // update status verification email sur session active
               $_SESSION['user']['email_verified'] = 1;

               $success = true;
      }
   }
?>

<?php include('./inc/header.php'); ?>

<!-- Confirmation email validé -->
<div class="row justify-content-center">

   <div class="row col-12 mt-2 justify-content-center">
   <?php if ($success)
   { ?>
      <h3 class="alert alert-success w-100 text-center">Bravo, votre adresse mail a bien été confirmée</h3>

   <?php } else { ?>
   <!-- sinon message probleme confirmation -->
      <h3 class="alert alert-danger w-100 text-center">Votre adresse mail n'a pu etre confirmée !</h3>
   <?php } ?>
</div><br />

   <a href="index.php"><button type="button" class="btn btn-info">Retour</button></a>

</div>

<?php include('./inc/footer.php');
