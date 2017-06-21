<?php
/***********************************************************
* FICHIERS DEVANT ETRE INCLUS + iNITIALISATION BDD CONNECT *
***********************************************************/

require_once('./inc/hp-functions.php');
require_once('./inc/function.php');

// Connection à la bdd - cas particulier mac -> pdo-thorphin
if(!file_exists('inc/pdo-thorphin.php'))
{
   include('inc/pdo.php');
}
else
{
   include('inc/pdo-thorphin.php');
}


