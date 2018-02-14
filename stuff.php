<?php

function ent($x) {
  return htmlspecialchars($x, 0, "UTF-8");
}

function messagebar($cls, $text, $timeout) {
  if(!isset($_SESSION["msgbar"])) $_SESSION["msgbar"]= [];
  $_SESSION["msgbar"][] = [ $cls, $text, $timeout ];
}

function moneycolor($money) {
  if ($money == 0) return "";
  elseif ($money < 0) return "success";
  else return "danger";
}

function format_display_output($ok, $timeout, $output) {
  return str_pad("$ok $timeout ", 16, "X") . "\n" . $output;
}
function accountNumber($user) {
  $number = 120000+$user["id"];
  
    $stack = 0;
    $number = str_split(strrev($number));

    foreach ($number as $key => $value)
    {
        if ($key % 2 == 0)
        {
            $value = array_sum(str_split($value * 2));
        }
        $stack += $value;
    }
    $stack %= 10;

    if ($stack != 0)
    {
        $stack -= 10;     $stack = abs($stack);
    }


    $number = implode('', array_reverse($number));
    $number = $number . strval($stack);

    return $number; 
}

function checkAccountNumber ($card_number) {
  $card_number_checksum = '';
  foreach (str_split(strrev((string) $card_number)) as $i => $d) {
    $card_number_checksum .= $i %2 !== 0 ? $d * 2 : $d;
  }
  if( array_sum(str_split($card_number_checksum)) % 10 === 0 ) {
      return intval(substr($card_number, 2, strlen($card_number)-3));
  } else {
      return false;
  }
}

function latexSpecialChars( $string )
{
    $map = array( 
            "#"=>"\\#",
            "$"=>"\\$",
            "%"=>"\\%",
            "&"=>"\\&",
            "~"=>"\\~{}",
            "_"=>"\\_",
            "^"=>"\\^{}",
            "\\"=>"\\textbackslash",
            "{"=>"\\{",
            "}"=>"\\}",
    );
    return preg_replace_callback( "/([\^\%~\\\\#\$%&_\{\}])/", function($x){return $map[$x];}, $string );
}

function buy_product($user_id, $product, $comment = "", $transfer_uid = null, &$transaction_id = 0, $product_amount = 1) {
  global $db;
  $debt = sql("SELECT SUM(charge) summe FROM ledger WHERE user_id = ? AND storno IS NULL", [ $user_id ], 1)["summe"];
  $user = sql("SELECT * FROM users WHERE id = ?", [ $user_id ], 1);
  $max_debt = $user["debt_limit"];
  if ($debt + $product["price"] > $max_debt) {
    notify_user($user, "[kdv] transaction failed!", "You're broke. Your payment of ".sprintf("%0.02f",$product["price"]/100)." failed!");
    return "transaction_failed";
  }
  sql("INSERT INTO ledger (user_id, product_id, charge, product_amount, comment, transfer_uid, client_ua, client_addr) VALUES (?,?,?, ?,?,?,?,?)",
      [ $user_id, $product["id"], $product["price"], $product_amount, $comment, $transfer_uid, $_SERVER["REQUEST_URI"]." ".$_SERVER["HTTP_USER_AGENT"], $_SERVER["REMOTE_ADDR"] ], true);
  $transaction_id = $db->lastInsertId();
  notify_user($user,
    sprintf("[kdv] %0.02f€ : %s",$product["price"]/100, $product["name"]),
    ($comment ? "Kommentar: $comment\n" : "") . "Guthaben/Schulden vorher: $debt\nProdukt: $product[name]\nPreis: $product[price]\n\nDatum/Uhrzeit: ".date("r"));
  return true;
}


function basiclogin() {
  if ($_SERVER["PHP_AUTH_USER"]== ".apitoken." && strlen($_SERVER["PHP_AUTH_PW"])==50) {
    $user = sql("SELECT * FROM users WHERE apitoken=?", [$_SERVER["PHP_AUTH_PW"]], 1);
    if ($user) return $user;
  }
  if ($_SERVER["PHP_AUTH_USER"]) {
    $user = get_login_user($_SERVER["PHP_AUTH_USER"]);
    if ($user && password_verify($_SERVER["PHP_AUTH_PW"], $user["password_hash"])) {
      return $user;
    }
  }
  header('WWW-Authenticate: Basic realm="KDV - Email and Password"');
  header('HTTP/1.0 401 Unauthorized');
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode(['error' => 'unauthorized']);
  exit;
}

function login() {
  if ($_POST["email"]) {
    $user = get_login_user($_POST["email"]);
    if ($user && password_verify($_POST["password"], $user["password_hash"])) {
      $_SESSION["user"] = $user;
      header("Location: ".BASE_URL);
      return;
    } else {
      echo "<p>Ungültige Zugangsdaten</p>";
    }
  }
  return get_view("login", []);
}

function get_user_debt($uid) {
  return sql("SELECT SUM(charge) summe FROM ledger WHERE user_id = ? AND storno IS NULL", [ $uid ], 1)["summe"];
}


function add_payment($uid, $backurl) {
  $user = sql("SELECT * FROM users WHERE id = ?", [$uid], 1);
  if (!$user) return "Bad user id";
  $product_id = intval($_GET["product_id"]);
  if (!$product_id) $product_id = PRODID_CASHPAYMENT;
  $product = sql("SELECT * FROM products WHERE id = ?", [ $product_id ], 1);
  $product_amount = 1;
  if ($_POST["charge"] && $_POST["product_id"] == 1) {
    $product["price"] = floatval(str_replace(",",".",$_POST["charge"])) * 100;
    if ($product["price"] <= 0) return "<div class=well>Ungültiger Betrag</div>";
    if ($_POST["what"] == "deposit") $product["price"] = -1 * $product["price"];
  } elseif (intval($_POST["product_amount"]) > 0) {
    $product_amount = intval($_POST["product_amount"]);
    $product["price"] = $product_amount * $product["price"];
  }
  if (count($_POST) && $product["price"]) {
    // buy_product($user_id, $product, $comment = "", $transfer_uid = null, &$transaction_id = 0, $product_amount = 1)
    $ok = buy_product($user["id"], $product, "", null, $transaction_id, $product_amount);
    if ($ok !== true) {
      return "<div class=well>$ok</div>";
    }
    messagebar("success", "Zahlung gespeichert: ".$product["price"]. " / ".$product["name"], 2000);
    header("Location: ".$backurl);
    exit;
  }
  return get_view("add_payment", [ "user" => $user, "product" => $product ]);
}


function storno_payment($uid, $payment_id) {
  $user = sql("SELECT * FROM users WHERE id = ?", [$uid], 1);
  if (!$user) die(json_encode(["error" => "Bad user id"]));
  $payment_id = intval($_GET["payment_id"]);
  if (!$payment_id) die(json_encode(["error" => "Bad payment id"]));
  if (count($_POST)) {
    $ok=sql("UPDATE ledger SET storno=NOW() WHERE id=? AND user_id=? AND transfer_uid IS NULL", [ $payment_id, $user['id'] ], true);
    if ($ok)
      echo json_encode([ "success" => true ]);
    else
      echo json_encode([ "error" => "Payment not found or cancellation not allowed" ]);
  } else {
    echo json_encode([ "error" => "Method not allowed" ]);
  }
  exit();
}

function wire_transfer($uid) {
  $user = sql("SELECT * FROM users WHERE id = ?", [$uid], 1);
  if (!$user) return "Internal Error";
  
  if (count($_POST)) {
    $product = sql("SELECT * FROM products WHERE id = ?", [ PRODID_WIRETRANSFER ], 1);
    $product["price"] = floatval(str_replace(",",".",$_POST["charge"])) * 100;
    if ($product["price"] <= 0) return "Invalid amount";
    
    $to_uid = checkAccountNumber($_POST["transfer_to"]);
    if (!$to_uid) return "Invalid account number";
    
    $touser = sql("SELECT * FROM users WHERE id = ?", [$to_uid], 1);
    if (!$touser) return "User not found";
    
    $id1 = $id2 = 0;
    $verwendungszweck = "Überweisung von $user[email] an $touser[email] : $_POST[verwendungszweck]";
    $ok = buy_product($user["id"], $product, $verwendungszweck, null, $id1);
    if ($ok !== true) {
      return "<div class=well>$ok</div>";
    }
    $product["price"] = -$product["price"];
    $ok = buy_product($touser["id"], $product, $verwendungszweck, $id1, $id2);
    sql("UPDATE ledger SET transfer_uid = ? WHERE id = ? LIMIT 1 ", [ $id2, $id1 ], true);
    return "<div class='alert alert-success'><h4>Überweisung erfolgt.</h4></div>";
    exit;
  }
  return get_view("wire_transfer", [ "user" => $user ]);
}

function show_ledger($uid) {
  $debt = get_user_debt($uid);
  $ledger = sql("SELECT l.timestamp, l.charge, p.name, p.code, l.storno, l.comment, l.id, l.user_id FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id WHERE user_id = ? ORDER BY timestamp DESC", [ $uid ]);
  return get_view("ledger", [ "ledger" => $ledger, "debt" => $debt ]);
}

function show_registration($uid, $admin=false) {
  $q = "";
  $scanners = sql("SELECT * FROM scanners ORDER BY current_state_timeout DESC", []);
  $count = sql("SELECT COUNT(*) c FROM user_barcodes WHERE user_id = ?", [ $uid ], 1)["c"];
  if ($_POST["remove_barcode"]) {
    sql("DELETE FROM user_barcodes WHERE id = ? AND user_id = ?", [ intval($_POST["remove_barcode"]), $uid ], true);
  }
  if ($_POST["add_barcode"]) {
    sql("UPDATE scanners SET current_state='register', current_state_timeout=DATE_ADD(NOW(),interval 1 MINUTE), current_user_id=?,current_display=?,current_display_timeout=DATE_ADD(NOW(),interval 1 MINUTE) WHERE id = ?", [ $uid, format_display_output("SCAN", 60, "\nScanne jetzt \ndeine neue Karte"), intval($_POST["scanner_id"]) ], true);
    return get_view("add_barcode_countdown", [ "scanner" => intval($_POST["scanner_id"]) ]);
  }
  if ($_POST["check_register_done"]) {
    foreach($scanners as $d) if ($d["id"] == $_POST["check_register_done"]) die(($d["current_state"] == "register" && strtotime($d["current_state_timeout"]) > time()) ? "no" : "yes");
    die("no");
  }
  if ($_POST["update"]) {
    sql("UPDATE users SET fullname=? WHERE id=?", [ $_POST["fullname"], $uid ], true);
    if($admin) sql("UPDATE users SET email=?,debt_limit=? WHERE id=?", [ $_POST["email"], $_POST["debt_limit"]*100, $uid ], true);
  }
  if ($_POST["password"]) {
    sql("UPDATE users SET password_hash=? WHERE id=?", [ password_hash($_POST["password"], PASSWORD_DEFAULT), $uid ], true);
  }
  $user = sql("SELECT * FROM users WHERE id = ?", [ $uid ], 1);
  $barcodes = sql("SELECT * FROM user_barcodes  WHERE user_id = ? ", [ $uid ]);
  return $q.get_view("registration_info", [ "scanners" => $scanners, "admin"=>$admin,
                 "user" => $user, "barcodes" => $barcodes ]);
}

function productlist($show_edit_buttons, $show_all=false) {
  $prods = sql("SELECT *, -(select sum(product_amount) from ledger where product_id=p.id and storno is null) bestand ".
    "FROM products p ".
    ($show_all?"":"WHERE disabled_at IS NULL ").
    "ORDER BY category,name ", []);
  if ($_GET["format"]) {
    if ($_GET["format"] == "csv") {
      header("Content-Type: text/plain; charset=utf8");
      foreach($prods as $r) {
        foreach($r as $c) echo "$c\t";
        echo "\n";
      }
      exit;
    }
    $tex = get_view("barcode.tex", [ "products" => $prods ]);
    if ($_GET["format"] == "pdf") {
      unlink("./data/tmp.tex"); unlink("./data/tmp.pdf");
      file_put_contents("./data/tmp.tex", $tex);
      $out = `cd data; pdflatex tmp.tex`;
      if (file_exists("data/tmp.pdf")) {
        header("Content-Type: application/pdf");
        readfile("data/tmp.pdf");
      } else {
        echo "<pre style='color:red'>$out</pre>";
      }
    } else {
      header("Content-Type: text/plain; charset=utf8");
      echo $tex;
    }
    exit;
  } else {
    return get_view("productlist", ["products" => $prods, "action_buttons"=>$show_edit_buttons]);
  }
}

function send_gcm_message($to_id, $data) {
  $body = json_encode(["data" => $data, "registration_ids" => [$to_id]]);
  $headers = [
    "Authorization: key=" . GCM_API_KEY,
    "Content-Type: application/json"
  ];
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
  curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");
  curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  $response = curl_exec($ch);
  //var_dump( $response );
  curl_close($ch);
  return $response;
}

function notify_user($user, $title, $message) {
  if ($user['gcm_token']) {
    send_gcm_message($user['gcm_token'], ["title"=>$title, "message"=>$message]);
  }
  if ($user['email_notification']) {
    mail($user['email'], $title, $message);
  }
}


