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
   setcookie('userFullMovie', '', 1);
   unset($_COOKIE['userFullMovie']);

   unset($_SESSION['user']);
   session_destroy();

   header('Location: ./index.php');
   exit;
