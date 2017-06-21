<?php

function debugg($array)
{
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}

// Fonction verification user is connect
function isConnected()
{
   // tableau d'erreurs
   $errors = array();

   // Verif id
   if (empty($_SESSION['user']['id']) || !is_numeric($_SESSION['user']['id']))
   {
      $errors['id'] = 'id Error';
   }

   // Verif pseudo
   // $errors += testMinMaxNbCar($_SESSION['user']['pseudo'], 6, 100, 'ErrPseudo');
   if (empty($_SESSION['user']['pseudo']) || !is_string($_SESSION['user']['pseudo']) || stripos($_SESSION['user']['pseudo'], ' ')
         || ((strlen($_SESSION['user']['pseudo']) < 6) && (strlen($_SESSION['user']['pseudo']) > 100)))
   {
      $errors['pseudo'] = 'pseudo Error';
   }

   // Verif email
   // $errors += testMinMaxNbCar($_SESSION['user']['email'], 6, 100, 'ErrMail');
   if (empty($_SESSION['user']['email']) || !filter_var($_SESSION['user']['email'], FILTER_VALIDATE_EMAIL)
         || ((strlen($_SESSION['user']['email']) < 6) && (strlen($_SESSION['user']['email']) > 100)))
   {
      $errors['email'] = 'email Error';
   }

   // Verif rule
   if (empty($_SESSION['user']['rule']) || ($_SESSION['user']['rule'] != 'user' && $_SESSION['user']['rule'] != 'admin'))
   {
      $errors['rule'] = 'rule Error';
   }

   // Verif ip
   if (empty($_SESSION['user']['ip']) || !filter_var($_SESSION['user']['ip'], FILTER_VALIDATE_IP) || $_SESSION['user']['ip'] != get_ip())
   {
      $errors['ip'] = 'ip Error';
   }

   // retourne true si aucune erreurs sinon false
   if (count($errors) == 0)
   {
      return true;
   }
   else
   {
      return false;
   }
}

