<?php
/****************************
*  FONCTIONS QUI VONT BIEN  *
****************************/

/*---------
|  DEBUG  |
---------*/

// Affichage array et variables (variable à afficher, 'nom de la variable')
function debug($var, $nomVar = '')
{
   if (is_array($var)) {
      echo $nomVar;
      echo '<pre>';
      print_r($var);
      echo '</pre><br />';

   } else {
      echo $nomVar.' => ';
      echo $var.'<br />';
   }
}

/*--------------
|  FORM ERROR  |
--------------*/

// Fonction test nb caracteres >=min et <=max (str à tester, nb caratere minimum,
// nb caractere maximum, clé ''.'XXXXX' par défaut 'str'.'XXXX')
// Return array keyErreur 'Erreur'
function testMinMaxNbCar($varStr, $minNbCar, $maxNbCar, $key = 'str')
{
   $error = array();
   if (empty($varStr)) {
      // str vide
      $error[$key] = 'Une saisie est attendue';
   } elseif (strlen($varStr) < $minNbCar) {
      // nbcar < min
      $error[$key] = 'Nombre de carateres minimum attendu : '.$minNbCar;
   } elseif (strlen($varStr) > $maxNbCar) {
      // nbcar > max
      $error[$key] = 'Nombre de carateres maximum attendu : '.$maxNbCar;
   }
   return $error;
}

// Fonction testant arg1 == arg2 pour un affichage ou un checked de bouton
// si arg3 == 'c' && arg1 == arg2 -> retourne 'checked' sinon retourne ''
// si arg3 == '' && arg1 == arg2 -> retourne true sinon retourne false
function testTrue($a1, $a2, $a3 = '')
{
   if ($a3 == 'c')
   {
      if ($a1 == $a2)
      {
         return 'checked';
      }
      else
      {
         return '';
      }
   }
   if ($a3 == '')
   {
      if ($a1 == $a2)
      {
         return true;
      }
      else
      {
         return false;
      }
   }
}

// Fonction requete bdd  * sur id unique
// ($pdo, 'table', id,
// chaine des éléments selctionnés ex :'title, content, author, status' - par défaut 'id',
// Methode de retour ('fetch' / 'fetchColumn' / 'FetchAll' )
// retourne un fetchxxxx basé sur l'id et la methode.
function selectId($pdoBdd, $table, $id, $selected = 'id', $method = 'fetch')
{
   $sql = "SELECT $selected FROM $table WHERE id= :id";
   $query = $pdoBdd->prepare($sql);
   $query->bindValue(':id', $id, PDO::PARAM_INT);
   $query->execute();
   switch ($method)
   {
      case 'fetchAll':
         return $query->fetchAll();
         break;
      case 'fetchColumn':
         return $query->fetchColumn();
         break;
      default:
         return $query->fetch();
         break;
   }
}

/*-----------------------
|  FORMAT DATE - HEURE  |
-----------------------*/

// fonction de mise en forme d'une date au format DATETIME
// Si pas d'arguments retourne un NOW au format DATETIME SQL
function dateFr($dateBDD = '')
{
   if (!empty($dateBDD) )
   {
      // dd/mm/yyyy à hh:ii
      return date('d/m/Y à H:i', strtotime($dateBDD));
   }
   else
   {
      // DATETIME SQL
      return date('Y-m-d H:i:s');
   }

}

/*-------------------
|  BASE DE DONNEES  |
-------------------*/

// Fonction dynamique de connection à une une bdd
// retourne une nouvelle instance PDO pointant sur la bdd $bddName
// newBddCon(str $bddName -> nom de la bdd)
function newBddCon($bddName)
{
   // test os server windows ou autre
   if (!stristr($_SERVER["HTTP_USER_AGENT"], 'Windows'))
   {
      $mdp = 'mysql';
   }
   else
  {
      $mdp = '';
  }
   
   // test si $bddName est string
   if (is_string($bddName))
   {
      return new PDO('mysql:host=localhost;dbname='.$bddName, 'root', $mdp, array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
                        ));
   }
   else
   {
      echo 'Erreur sur newBddCon<br/>';
      echo 'Attendu : newBddCon(str $bddName -> nom de la bdd)<br/>';
      echo '$bddName => ';
      var_dump($bddName);
      echo '<br/>';
   }
}

/*--------------
|  PAGINATION  |
--------------*/

// Fonction pour compter le nombre de lignes dans une table
// $pdo -> objet PDO pointant vers la bdd à requeter
// $table -> string table cible
// $where -> string requete de type 'WHERE status = 'p' AND (title LIKE '%XXXX%' OR content LIKE '%XXXXX%')'
function nbLignes($pdo, $table, $where = '' )
{
   $sql = "SELECT COUNT(*) FROM $table $where";
   $query = $pdo->prepare($sql);
   $query->execute();
   return $query->fetchColumn();
}

// Fonction pagination
// $page -> numéro de page
// $num -> nombre d'items par page
// $count -> nombre de lignes dans la table || fonction nbLignes($pdo, $table, $where = '' )
// $cible -> Page en sur laquelle s'applique la pagination. par défaut index.php
// $search -> terme recherché. par défaut ''
//   insere dans l'url un 'search=TERM&' avant le 'page=' si $search != ''
function pages($page, $num, $count, $cible = 'index.php', $search = '')
{
   $offset = $page * $num - $num;
   $itemStart = $offset + 1;
   $itemEnd = $offset + $num;
   $termSearch = $search;

   // si $search != ''
   // ajoute 'search=' pour definir le get
   // ajoute un & pour separer avec search= d'avec page=
   if ($search != '')
   {
      $search = 'search='.$search;
      $search .= '&';
   }

   // plus assez d'items pour faire une page suivante
   if ($itemEnd > $count)
   {
      $itemEnd = $count;
   }
   // Affichage bouton precedent si page > 1
	if ($page > 1)
   {
      echo '<a href="./'.$cible.'?'.$search.'page=' . ($page - 1) . '" class="btn btn-outline-warning align-self-start">Précédent</a>';
   }

   // affichage du terme recherché si $search et $termSearch != ''
   if ($termSearch != '')
   {
      echo '<p class="align-self-center" >Recherche en cours : '.$termSearch.'</p>';
   }
   // Affichage de item NUM à item NUM+$num sur nombre d'items
   echo '<p class="align-self-center" >item '.$itemStart.' à '.$itemEnd.' sur '.$count.' items</p>';

 	//n'affiche le lien vers la page suivante que s'il y en a une
   if ($page * $num < $count)
   {
      echo '<a href="./'.$cible.'?'.$search.'page=' . ($page + 1) . '" class="btn btn-outline-warning align-self-end">Suivant</a>';
   }
}

/*------------
|  PASSWORD  |
------------*/

function passWash($passWord, $action = '', $hash = '')
{
   switch ($action)
   {
      case 'verify':
         return password_verify($passWord, $hash);
         break;

      case 'bcrypt':
         return password_hash($passWord, PASSWORD_BCRYPT);
         break;

      default:
         return password_hash($passWord, PASSWORD_DEFAULT);
         break;
   }
}

/*---------
|  TOKEN  |
---------*/

function tokenGen($nbBytes)
{
   // return bin2hex(random_bytes($nbBytes));
   return md5(uniqid(rand(), true));
}

/*------
|  IP  |
------*/

/**
 * Récupérer la véritable adresse IP d'un visiteur
 */
function get_ip() {
	// IP si internet partagé
	if (isset($_SERVER['HTTP_CLIENT_IP']))
   {
		return $_SERVER['HTTP_CLIENT_IP'];
	}
	// IP derrière un proxy
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
   {
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	// Sinon : IP normale
	else
   {
		return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
	}
}
