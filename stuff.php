<?php

function ent($x) {
  return htmlspecialchars($x, 0, "UTF-8");
}

function moneycolor($money) {
  if ($money == 0) return "";
  elseif ($money < 0) return "success";
  else return "danger";
}

function format_display_output($ok, $timeout, $output) {
  return str_pad("$ok $timeout ", 16, "X") . "\n" . $output;
}

function buy_product($user_id, $product) {
  $debt = sql("SELECT SUM(charge) summe FROM ledger WHERE user_id = ? AND storno IS NULL", [ $user_id ], 1)["summe"];
  $user = sql("SELECT debt_limit FROM users WHERE id = ?", [ $user_id ], 1);
  $max_debt = $user["debt_limit"];
  if ($debt + $product["price"] > $max_debt) {
    return "transaction_failed";
  }
  sql("INSERT INTO ledger (user_id, product_id, charge, product_amount) VALUES (?,?,?, 1)",
      [ $user_id, $product["id"], $product["price"] ], true);
  if ($user["notification_email"] == 1)
    mail($user["email"], "[kdv] recorded sale worth $product[price]", "Guthaben/Schulden vorher: $debt\n\nProdukt: $product[name]\nPreis: $product[price]\n\nDatum/Uhrzeit: ".date("r"));
  return true;
}

function basiclogin() {
  if ($_SERVER["PHP_AUTH_USER"]) {
    $user = sql("SELECT * FROM users WHERE email = ?", [ $_SERVER["PHP_AUTH_USER"] ], 1);
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
    $user = sql("SELECT * FROM users WHERE email = ?", [ $_POST["email"] ], 1);
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
  if (!$product_id) $product_id = 1;
  $product = sql("SELECT * FROM products WHERE id = ?", [ $product_id ], 1);
  if ($_POST["charge"] && $_POST["product_id"] == 1) {
    $product["price"] = floatval(str_replace(",",".",$_POST["charge"])) * 100;
    if ($product["price"] <= 0) return "<div class=well>Ungültiger Betrag</div>";
    if ($_POST["what"] == "deposit") $product["price"] = -1 * $product["price"];
  }
  if (count($_POST) && $product["price"]) {
    $ok = buy_product($user["id"], $product);
    if ($ok !== true) {
      return "<div class=well>$ok</div>";
    }
    header("Location: ".$backurl);
    exit;
  }
  return get_view("add_payment", [ "user" => $user, "product" => $product ]);
}

function show_ledger($uid) {
  $debt = get_user_debt($uid);
  $ledger = sql("SELECT l.timestamp, l.charge, p.name, p.code, l.storno FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id WHERE user_id = ? ORDER BY timestamp DESC", [ $uid ]);
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

function productlist($show_edit_buttons) {
  $prods = sql("SELECT *, -(select sum(product_amount) from ledger where product_id=p.id and storno is null) bestand FROM products p ORDER BY category,name ", []);
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
      }else {
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




