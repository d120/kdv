<?php
include "init.php";
header("Content-Type: text/plain; charset=utf8");

function show_output($output) {
  global $scanner;
  sql("UPDATE scanners SET current_display = ?, current_display_timeout=DATE_ADD(NOW(),INTERVAL 30 SECOND) WHERE id = ?", [$output, $scanner["id"]]);
  echo $output;
}

$barcode = $_POST["barcode"];
$scanner_result = sql("SELECT * FROM scanners WHERE id = ? AND token = ?", [ $_POST["scanner"], $_POST["scanner_token"] ]);
if (count($scanner_result) == 0) {
  header("HTTP/1.1 403 Forbidden");
  die(json_encode([ "error" => "invalid scanner" ]));
}
$scanner = $scanner_result[0];

$product_result = sql("SELECT * FROM products WHERE code = ? AND disabled_at IS NULL", [ $barcode ]);
if (count($product_result) == 1) {
  $product = $product_result[0];
  if ($scanner["current_state"] == "buy" && $scanner["current_user_id"] && strtotime($scanner["current_state_timeout"]) >= time()) {
    sql("INSERT INTO ledger (user_id, product_id, charge) VALUES (?,?,?)",
        [ $scanner["current_user_id"], $product["id"], $product["price"] ], true);

    show_output(sprintf("OK\nRecorded sale %04.2f\n", $product["price"]/100));
  } else {
    show_output(sprintf("FAIL\nPlease login\n%04.2f  %s", $product["price"]/100, $product["name"]));
  }
  return;
}

$user_result = sql("SELECT u.* FROM user_barcodes b INNER JOIN users u ON b.user_id=u.id WHERE b.code = ?", [ $barcode ]);
if (count($user_result) == 1) {
  $user = $user_result[0];
  sql("UPDATE scanners SET current_user_id = ?, current_state_timeout =DATE_ADD( NOW(), INTERVAL 1 MINUTE), current_state='buy' WHERE id = ?", [ $user["id"], $scanner["id"] ], true);
  show_output("OK\nWelcome ".$user["fullname"]."!\n");
  return;
}

if ($scanner["current_state"] == "register" && strtotime($scanner["current_state_timeout"]) >= time()) {
  sql("INSERT INTO user_barcodes (user_id, code) VALUES (?, ?)", [ $scanner["current_user_id"], $barcode], true);
  show_output("OK\nRegistered :-)\n");
  return;
}

show_output("FAIL\nInvalid ".$barcode);


