<?php

function show_output($ok, $timeout, $output) {
  global $scanner;
  $output = format_display_output($ok, $timeout, $output);
  sql("UPDATE scanners SET current_display = ?, current_display_timeout=DATE_ADD(NOW(),INTERVAL ? SECOND) WHERE id = ?", [$output, $timeout, $scanner["id"]], true);
  echo $output;
}
function set_state($new_state, $user_id = false, $state_timeout = 60) {
  global $scanner;
  sql("UPDATE scanners SET current_state = ?, current_state_timeout = DATE_ADD(NOW(), INTERVAL ? SECOND)  WHERE id = ?",
    [ $new_state, $state_timeout, $scanner["id"] ], true);
  $scanner["current_state"] = $new_state;
  if ($user_id !== false) {
    sql("UPDATE scanners SET current_user_id = ? WHERE id = ?", [ $user_id, $scanner["id"] ], true);
    $scanner["current_user_id"] = $user_id;
  }
}
function user_header() {
  global $scanner;
  $user = sql("SELECT * FROM users WHERE id = ? LIMIT 1", [ $scanner["current_user_id"] ], 1);
  if ($user) {
    return substr($user["fullname"]."          ",0,10).sprintf("% 6.2f", -(get_user_debt($user["id"])/100));
  }
}

$barcode = $_POST["barcode"];
$scanner_result = sql("SELECT * FROM scanners WHERE id = ? AND token = ?", [ $_POST["scanner"], $_POST["scanner_token"] ]);
if (count($scanner_result) == 0) {
  header("HTTP/1.1 403 Forbidden");
  die("FAIL 10\nInvalid scanner!");
}
$scanner = $scanner_result[0];
if (strtotime($scanner["current_state_timeout"]) < time()) {
  set_state("", false, 0);
  $scanner["current_state"] = "";
}

switch ($barcode) {
case "2992101010102":
  set_state("", false, 0);
  show_output("OK", 5, "Good bye\n");
  return;
case ABC_REGISTER:
  if ($scanner["current_state"] == "buy") {
    set_state("register", false, 30);
    $user=sql("SELECT * FROM users WHERE id=?", [ $scanner["current_user_id"] ], 1);
    show_output("SCAN", 30, user_header()."\nScan card to \nregister\n");
  } else {
    set_state("register", -1, 30);
    show_output("SCAN", 30, "Scan card to \ncreate account\n");
  }
  return;
case ABC_REMOVECARD:
  set_state("removecard", false, 30);
  show_output("OK", 30, user_header()."\nRemove card \nfrom acct?\n");
  return;
case ABC_STORNO:
  if ($scanner["current_state"] != "buy") { show_output("FAIL", 5, "Please login\n"); return; }
  $lastItem = sql("SELECT * FROM ledger WHERE user_id = ? AND storno IS NULL ORDER BY timestamp DESC LIMIT 1", [ $scanner["current_user_id"] ]);
  if (count($lastItem) == 1) {
    sql("UPDATE ledger SET storno=NOW() WHERE id = ?", [ $lastItem[0]["id"] ], true);
    show_output("OK", 30, sprintf("%s\nStorno\n%4.2f",user_header(), $lastItem[0]["charge"]/100));
    set_state("buy", false, 30);
  }
  return;
}
if ($scanner["current_state"] == "removecard") {
  sql("DELETE FROM user_barcodes WHERE code = ?", [ $barcode ], true);
  set_state("", false, 0);
  show_output("OK", 10, "Removed card\n");
  return;
}

$product_result = sql("SELECT * FROM products WHERE code = ? AND disabled_at IS NULL", [ $barcode ]);
if (count($product_result) == 1) {
  $product = $product_result[0];
  if ($scanner["current_state"] == "buy" && $scanner["current_user_id"]) {
    $debt = sql("SELECT SUM(charge) summe FROM ledger WHERE user_id = ? AND storno IS NULL", [ $scanner["current_user_id"] ], 1)["summe"];
    $max_debt = sql("SELECT debt_limit FROM users WHERE id = ?", [ $scanner["current_user_id"] ], 1)["debt_limit"];
    if ($debt + $product["price"] > $max_debt) {
      show_output("FAIL", 30, user_header()."\nTransaktion \nfehlgeschlagen\n");
      set_state("buy", false, 30); return;
    }
    sql("INSERT INTO ledger (user_id, product_id, charge, product_amount) VALUES (?,?,?, 1)",
        [ $scanner["current_user_id"], $product["id"], $product["price"] ], true);

    show_output("OK", 30, sprintf(user_header()."\nRecorded sale \n%04.2f\n%s\n", $product["price"]/100, $product["name"]));
    set_state("buy", false, 30);
  } else {
    show_output("FAIL", 10, sprintf("Please login\n%04.2f\n%s", $product["price"]/100, $product["name"]));
  }
  return;
}

$user_result = sql("SELECT u.* FROM user_barcodes b INNER JOIN users u ON b.user_id=u.id WHERE b.code = ?", [ $barcode ]);
if (count($user_result) == 1) {
  $user = $user_result[0];
  set_state("buy", $user["id"], 30);
  show_output("SCAN", 30, sprintf(user_header()."\nHallo", $user["fullname"], $t, $d));
  return;
}

if ($scanner["current_state"] == "register") {
  if ($scanner["current_user_id"] == -1) {
    sql("INSERT INTO users (email, fullname, debt_limit) VALUES ('', 'Anonym', ?) ", [DEFAULT_DEBT_LIMIT_ANONYMOUS], true);
    $scanner["current_user_id"] = sql("SELECT last_insert_id() id", [], 1)["id"];
  }
  sql("INSERT INTO user_barcodes (user_id, code) VALUES (?, ?)", [ $scanner["current_user_id"], $barcode], true);
  set_state("", false, 0);
  show_output("OK", 10, "Registered :-)\n");
  return;
}

show_output("FAIL", 10, "INVALID CODE!\n".$barcode);


