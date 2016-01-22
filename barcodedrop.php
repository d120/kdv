<?php

function show_output($output) {
  global $scanner;
  sql("UPDATE scanners SET current_display = ?, current_display_timeout=DATE_ADD(NOW(),INTERVAL 12 SECOND) WHERE id = ?", [$output, $scanner["id"]], true);
  echo $output;
}
function set_state($new_state, $user_id = false, $state_timeout = 60) {
  global $scanner;
  sql("UPDATE scanners SET current_state = ?, current_state_timeout = DATE_ADD(NOW(), INTERVAL ? SECOND)  WHERE id = ?",
       [ $new_state, $state_timeout, $scanner["id"] ], true);
  if ($user_id !== false) sql("UPDATE scanners SET current_user_id = ? WHERE id = ?", [ $user_id, $scanner["id"] ], true);
}

$barcode = $_POST["barcode"];
$scanner_result = sql("SELECT * FROM scanners WHERE id = ? AND token = ?", [ $_POST["scanner"], $_POST["scanner_token"] ]);
if (count($scanner_result) == 0) {
  header("HTTP/1.1 403 Forbidden");
  die("FAIL\nInvalid scanner!");
}
$scanner = $scanner_result[0];
if (strtotime($scanner["current_state_timeout"]) < time()) {
  set_state("", false, 0);
  $scanner["current_state"] = "";
}

switch ($barcode) {
case "2992101010102":
  set_state("", false, 0);
  show_output("OK\nGood bye\n");
  return;
case ABC_REGISTER:
  if ($scanner["current_state"] == "buy") {
    set_state("register", false, 30);
    $user=sql("SELECT * FROM users WHERE id=?", [ $scanner["current_user_id"] ], 1);
    show_output("SCAN\nScan card to \nregister\n".$user["fullname"]);
  } else {
    set_state("register", -1, 30);
    show_output("SCAN\nScan card to \ncreate account\n");
  }
  return;
case ABC_REMOVECARD:
  set_state("removecard", false, 30);
  show_output("OK\nRemove card \nfrom acct?\n");
  return;
case ABC_STORNO:
  if ($scanner["current_state"] != "buy") { show_output("FAIL\nPlease login\n"); return; }
  $lastItem = sql("SELECT * FROM ledger WHERE user_id = ? AND storno IS NULL ORDER BY timestamp DESC LIMIT 1", [ $scanner["current_user_id"] ]);
  if (count($lastItem) == 1) {
    sql("UPDATE ledger SET storno=NOW() WHERE id = ?", [ $lastItem[0]["id"] ], true);
    show_output("OK\nStorno ".$lastItem[0]["charge"]);
  }
  return;
}
if ($scanner["current_state"] == "removecard") {
  sql("DELETE FROM user_barcodes WHERE code = ?", [ $barcode ], true);
  set_state("", false, 0);
  show_output("OK\nRemoved card\n");
  return;
}

$product_result = sql("SELECT * FROM products WHERE code = ? AND disabled_at IS NULL", [ $barcode ]);
if (count($product_result) == 1) {
  $product = $product_result[0];
  if ($scanner["current_state"] == "buy" && $scanner["current_user_id"]) {
    $debt = sql("SELECT SUM(charge) summe FROM ledger WHERE user_id = ? AND storno IS NULL", [ $scanner["current_user_id"] ], 1)["summe"];
    $max_debt = sql("SELECT debt_limit FROM users WHERE id = ?", [ $scanner["current_user_id"] ], 1)["debt_limit"];
    if ($debt + $product["price"] > $max_debt) {
      show_output("FAIL\nTransaktion fehlgeschlagen\n"); return;
    }
    sql("INSERT INTO ledger (user_id, product_id, charge) VALUES (?,?,?)",
        [ $scanner["current_user_id"], $product["id"], $product["price"] ], true);

    show_output(sprintf("OK\nRecorded sale \n%04.2f\n%s\n", $product["price"]/100, $product["name"]));
  } else {
    show_output(sprintf("FAIL\nPlease login\n%04.2f\n%s", $product["price"]/100, $product["name"]));
  }
  return;
}

$user_result = sql("SELECT u.* FROM user_barcodes b INNER JOIN users u ON b.user_id=u.id WHERE b.code = ?", [ $barcode ]);
if (count($user_result) == 1) {
  $user = $user_result[0];
  set_state("buy", $user["id"]);
  show_output("SCAN\nWillkommen\n".$user["fullname"]."!\n");
  return;
}

if ($scanner["current_state"] == "register") {
  if ($scanner["current_user_id"] == -1) {
    sql("INSERT INTO users (email, fullname, debt_limit) VALUES ('', 'Anonym', 0) ", [], true);
    $scanner["current_user_id"] = sql("SELECT last_insert_id() id", [], 1)["id"];
  }
  sql("INSERT INTO user_barcodes (user_id, code) VALUES (?, ?)", [ $scanner["current_user_id"], $barcode], true);
  set_state("", false, 0);
  show_output("OK\nRegistered :-)\n");
  return;
}

show_output("FAIL\n! INVALID ! \n".$barcode);


