<?php
include("init.php");
include("stuff.php");

session_start();
header("Content-Type: text/html; charset=utf8");

if (!isset($_SESSION["user"])) {
  $q = login();
  load_view("header",["content" => $q]);
  exit;
}

$q = "";
$menuactive = $_GET["m"];

switch($_GET["m"]) {
  case "ledger":
    $uid = intval($_SESSION["user"]["id"]);
    $q .= show_ledger($uid);
    break;
  case "logout":
    session_destroy();
    header("Location: ".BASE_URL);
    break;
  case "registration":
    $uid = intval($_SESSION["user"]["id"]);
    $q.= show_registration($uid);
    break;
  default:
    $q.= show_timestamps();
    break;
}

load_view("header", ["navigation" => "main", "menuactive" => $menuactive, "content"=>$q]);

