<?php
include "init.php";
header("Content-Type: text/plain; charset=utf8");

$p = $_SERVER["PATH_INFO"];

switch($p) {
case "/barcodedrop/":
  include("barcodedrop.php");
  break;

case "/istgeradejemandda/":
  $last_actions = sql("SELECT MAX(current_state_timeout) t FROM scanners ORDER BY current_state_timeout DESC", []);
  echo $last_actions[0]["t"];
  break;

case "/regproduct/":
  sql("INSERT INTO products (price, name, code) VALUES(?,?,?)", [ $_POST["price"], $_POST["name"], $_POST["code"] ], true);
  $id = sql("SELECT LAST_INSERT_ID() id", [], 1)["id"];
  if (is_uploaded_file($_FILES["productimage"])) {
    move_uploaded_file($_FILES["productimage"], "./productimages/".$id.".jpg");
  }
  echo "success\n".$id;
  break;

case "/lastscanned/":
  $code = sql("SELECT current_display FROM scanners ORDER BY current_display_timeout DESC LIMIT 1", [] , 1)["current_display"];
  $code = explode("\n", $code);
  if (strstr($code[1], "INVALID")) echo $code[2];
  break;

default:
  echo "FAIL\n404\n";
  break;
}


