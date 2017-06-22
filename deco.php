<?php
/***********************
**  DECONNECTION.PHP  **
***********************/
   // Gestion User session.
   session_start();

   // Fichiers inclus + initialisation connection à la bdd
   include_once('./inc/required.php');

   // Titre Page
   $title = 'Déconnexion';

   // destroy session
   session_destroy();
   unset($_SESSION);

   header('Location: ./index.php');
   exit;
