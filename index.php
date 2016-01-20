<?php
include("init.php");
include("stuff.php");

session_start();
header("Content-Type: text/html; charset=utf8");

if (!isset($_SESSION["user"])) {
  login();
  exit;
}

load_view("header",[]);
echo "<p><a href='?m=ledger'>Kontoauszug</a> | <a href='?m=registration'>Account</a> | <a href='?m=logout'>Logout</a></p><hr>\n\n";

switch($_GET["m"]) {
  case "ledger":
    $uid = intval($_SESSION["user"]["id"]);
    show_ledger($uid);
    break;
  case "logout":
    session_destroy();
    header("Location: ".BASE_URL);
    break;
  case "registration":
    $uid = intval($_SESSION["user"]["id"]);
    show_registration($uid);
    break;
  default: break;
}

