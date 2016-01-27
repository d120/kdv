<?php
include(".htconfig.php");
define("VIEW_DIR", __DIR__."/views/");

include("views.inc.php");

set_view_var("action", $_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"]);
set_view_var("title", "KDV");

try {
$db = new PDO(DATABASE_URI, DATABASE_USER, DATABASE_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $ex) {
// avoid the default error message as it might display the db password
die("<H2>Eile mit Weile</H2>Database connection failed!");
}

$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

function sql($query, $args, $noquery = false) {
  global $db;
  $q = $db->prepare($query);
  $q->execute($args);
  if ($noquery === true) return $q->rowCount(); elseif ($noquery === 1) return $q->fetch(); else return $q->fetchAll();
}

