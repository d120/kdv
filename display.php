<?php
include "init.php";
include "stuff.php";
load_view("header",[]);
echo '<meta http-equiv="refresh" content="3">';

$scanner = sql("SELECT * FROM scanners WHERE id = ?", [ $_GET["scanner"] ], 1);
if (!$scanner) die("Invalid scanner id");

if (strtotime($scanner["current_display_timeout"]) > time()) {
  $parts = explode("\n", $scanner["current_display"]);
  $bg = $parts[0] == "OK" ? "#aaffaa" : "#ffaaaa";
  echo "<pre style='padding:10px; background: $bg;'>".$scanner["current_display"]."</pre>";
}

if (strtotime($scanner["current_state_timeout"]) < time()) {
  die("<h2>Herzlich willkommen!</h2><h2>".date("H:i:s")."</h2>");
}
echo "<h2>$scanner[current_state] - $scanner[current_state_timeout]</h2>";

$user = sql("SELECT * FROM users WHERE id = ?", [ $scanner["current_user_id"] ], 1);
echo "<h2>".$user["fullname"]."</h2>";
$ledger = sql("SELECt * FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id WHERE user_id = ? ORDER BY timestamp DESC LIMIT 3", [ $scanner["current_user_id"] ]);
foreach($ledger as $d) {
  printf("<h4>%s : %04.2f</h4>", $d["name"], $d["charge"]/100);
}
$schulden_result = sql("SELECT SUM(charge) summe FROM ledger WHERE user_id = ? AND storno IS NULL", [ $scanner["current_user_id"] ]);
$schulden = $schulden_result[0]["summe"];
if ($schulden < 0)
  printf("<h2>Guthaben: %04.2f</h2>", -($schulden/100));
else
  printf("<h2>Schulden: %04.2f</h2>", $schulden/100);

