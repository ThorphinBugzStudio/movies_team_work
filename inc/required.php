<?php
/***********************************************************
* FICHIERS DEVANT ETRE INCLUS + iNITIALISATION BDD CONNECT *
***********************************************************/

require_once('./inc/hp-functions.php');
require_once('./inc/function.php');

// Connection à la bdd - cas particulier mac -> pdo-thorphin
$pdo = newBddCon('exo_equipe');
