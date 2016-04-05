<?php
include "init.php";
include "stuff.php";
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

/*
case "/regproduct/":
  sql("INSERT INTO products (price, name, code) VALUES(?,?,?)", [ $_POST["price"], $_POST["name"], $_POST["code"] ], true);
  $id = sql("SELECT LAST_INSERT_ID() id", [], 1)["id"];
  if (is_uploaded_file($_FILES["productimage"])) {
    move_uploaded_file($_FILES["productimage"], "./productimages/".$id.".jpg");
  }
  echo "success\n".$id;
  break;
*/

case "/lastscanned/":
  $code = sql("SELECT current_display FROM scanners ORDER BY current_display_timeout DESC LIMIT 1", [] , 1)["current_display"];
  $code = explode("\n", $code);
  if (strstr($code[1], "INVALID")) echo $code[2];
  break;

case "/textdisplay/":
  $scanner = sql("SELECT current_display FROM scanners WHERE id = ? and current_display_timeout > NOW() limit 1", [ $_GET["scanner"] ], 1);
  if ($scanner)
    echo $scanner["current_display"];
  else {
    $secs = date("s") % 30;
    echo "XX 10 XXXXXXXXXX\n".date("D, d.m H:i")."\n";
    if ($secs < 10)  echo (filemtime("ad")>time()-1200) ? file_get_contents("ad") : "* Hier koennte *\n* Ihre Werbung *\n*    stehen    *";
    else if ($secs < 20)  echo "    \n   WILLKOMMEN\n";
    else if ($secs < 30)  echo "Bitte Karte oder\nFeedbackbogen\nscannen";
  }
  break;

case "/ad/":
  if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    sleep(1);
    $li = explode("\n", file_get_contents("php://input"));
    $out = "";
    for($i=0;$i<3;$i++){
      if(1 !== preg_match('/^[a-zA-Z0-9!"§$%&\/(),.-;:_ =?+#*\']{0,14}$/', $li[$i])) {
        header("HTTP/1.1 400 Bad Request"); echo "Invalid line $i\n"; return;
      }
      $out .= "*".str_pad($li[$i], 14)."*\n";
    }
    if ($out == file_get_contents("ad")) {
      header("HTTP/1.1 304 Not Modified"); exit;
    }
    file_put_contents("ad", $out);
    header("HTTP/1.1 201 Created");
  } else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo file_get_contents("ad");
  } else {
    header("HTTP/1.1 405 Method Not Allowed");
  }
  break;

case "/display/":
  header("Content-Type: application/json; charset=utf8");
  $scanner = sql("SELECT *, TIMESTAMPDIFF(SECOND,NOW(),current_state_timeout) timeout FROM scanners
    WHERE id = ? AND greatest(current_state_timeout, current_display_timeout, last_changed_at) > ? LIMIT 1",
    [ $_GET["scanner"], $_GET["t1"] ], 1);
  if (!$scanner || md5($scanner["id"].$scanner["token"]) != $_GET["token"]) die("{}");

  if (strtotime($scanner["current_display_timeout"]) > time()) {
    $parts = explode("\n", $scanner["current_display"]);
    $bg = $parts[0] == "OK" ? "#aaffaa" : ($parts[0] == "SCAN" ? "#aaaaff" : "#ffaaaa");
    $q.= "<pre style='padding:10px; background: $bg;'>".$scanner["current_display"]."</pre>";
  }

  if (strtotime($scanner["current_state_timeout"]) <= time()) {
    $q.= "<h2>Herzlich willkommen!</h2>";
  } else {
    $user = sql("SELECT * FROM users WHERE id = ?", [ $scanner["current_user_id"] ], 1);
    $q.= "<h2>Hallo ".$user["fullname"]."</h2>";
    $ledger = sql("SELECT * FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id
      WHERE user_id = ? AND storno IS NULL ORDER BY timestamp DESC LIMIT 3",
      [ $scanner["current_user_id"] ]);
    $q.= get_view("ledger", [ "ledger" => $ledger, "mini" => true ]);
    $schulden = get_user_debt($scanner["current_user_id"]);
    if ($schulden < 0)
      $q.= sprintf("<h2>Guthaben: %04.2f</h2>", -($schulden/100));
    else
      $q.= sprintf("<h2>Schulden: %04.2f</h2>", $schulden/100);
    $q.= "<p class=text-muted>State: $scanner[current_state]  |  Timeout: $scanner[timeout] sec</p>";

  }
  echo json_encode(["html" => $q, "t1" => time() ]);
  break;

case "/me/display/":
  $user = basiclogin();
  header("Content-Type: text/html");
  $q.= "<h2>Hallo ".$user["fullname"]."</h2>";
  $ledger = sql("SELECT * FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id
    WHERE user_id = ? AND storno IS NULL ORDER BY timestamp DESC LIMIT 3",
    [ $user["id"] ]);
  $q.= get_view("ledger", [ "ledger" => $ledger, "mini" => true ]);
  $schulden = get_user_debt($user["id"]);
  if ($schulden < 0)
    $q.= sprintf("<h2>Guthaben: %04.2f</h2>", -($schulden/100));
  else
    $q.= sprintf("<h2>Schulden: %04.2f</h2>", $schulden/100);
  echo $q;
  break;

case "/me/ledger/":
  $user = basiclogin();
  header("Content-Type: application/json; charset=utf8");
  $ledger = sql("SELECT * FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id
    WHERE user_id = ? AND storno IS NULL ORDER BY timestamp DESC ",
    [ $user["id"] ]);

  $schulden = get_user_debt($user["id"])/100;
  echo json_encode(["success" => true, "ledger" => $ledger, "debt" => $schulden]);
  break;

case "/productlist/":
  $user = basiclogin();
  header("Content-Type: application/json; charset=utf-8");
  $products = sql("SELECT * FROM products WHERE disabled_at IS NULL", []);
  echo json_encode($products);
  break;

case "/me/buy/":
  $user = basiclogin();
  header("Content-Type: application/json; charset=utf-8");
  error_log(print_r($_POST,true));
  if (!isset($_POST["barcode"]) || strlen($_POST["barcode"]) < 4) {
    echo json_encode(["error" => "missing_parameter"]);
    return;
  }
  $product_result = sql("SELECT * FROM products WHERE code = ? AND disabled_at IS NULL", [ $_POST["barcode"] ]);
  if (count($product_result) == 1) {
    $res = buy_product($user["id"], $product_result[0]);
    if ($res === true) {
      echo json_encode(["success" => true]);
    } else {
      echo json_encode(["success" => false, "error" => $res ]);
    }
  } else {
    echo json_encode(["success" => false, "error" => "unknown_product"]);
  }
  break;

case "/me/deposit/":
  $user = basiclogin();
  header("Content-Type: application/json; charset=utf-8");
  if (!isset($_POST["amount"]) || strlen($_POST["amount"]) < 1) {
    echo json_encode(["success" => false, "error" => "missing_parameter"]);
    return;
  }
  $product = sql("SELECT * FROM products WHERE id = 1", [], 1);
  $product["price"] = - intval($_POST["amount"] * 100);
  if ($product["price"] <= -5000 || $product["price"] >= 0) {
    echo json_encode(["success" => false, "error" => "invalid_amount"]);
    return;
  }
  $res = buy_product($user["id"], $product);
  if ($res === true) echo json_encode(["success" => true]);
  else echo json_encode(["success" => false, "error" => $res ]);
  break;

default:
  echo "FAIL\n404\n";
  break;
}


