<?php
include "init.php";
include "stuff.php";

$q="";
$q.= '<meta http-equiv="refresh" content="3">';

$scanner = sql("SELECT *, TIMESTAMPDIFF(SECOND,NOW(),current_state_timeout) timeout FROM scanners WHERE id = ?", [ $_GET["scanner"] ], 1);
if (!$scanner || md5($scanner["id"].$scanner["token"]) != $_GET["token"]) die("Invalid scanner id");

if (strtotime($scanner["current_display_timeout"]) > time()) {
  $parts = explode("\n", $scanner["current_display"]);
  $bg = $parts[0] == "OK" ? "#aaffaa" : ($parts[0] == "SCAN" ? "#aaaaff" : "#ffaaaa");
  $q.= "<pre style='padding:10px; background: $bg;'>".$scanner["current_display"]."</pre>";
}

if (strtotime($scanner["current_state_timeout"]) < time()) {
  $q.= "<h2>Herzlich willkommen!</h2><h2>".date("H:i:s")."</h2>";
} else {

  $user = sql("SELECT * FROM users WHERE id = ?", [ $scanner["current_user_id"] ], 1);
  $q.= "<h2>Hallo ".$user["fullname"]."</h2>";
  $ledger = sql("SELECT * FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id WHERE user_id = ? AND storno IS NULL ORDER BY timestamp DESC LIMIT 3", [ $scanner["current_user_id"] ]);
  #foreach($ledger as $d) {
  #  $q.=sprintf("<h4>%s : %04.2f</h4>", $d["name"], $d["charge"]/100);
  #}
  $q .= get_view("ledger", [ "ledger" => $ledger, "mini" => true ]);
  $schulden_result = sql("SELECT SUM(charge) summe FROM ledger WHERE user_id = ? AND storno IS NULL", [ $scanner["current_user_id"] ]);
  $schulden = $schulden_result[0]["summe"];
  if ($schulden < 0)
    $q.=sprintf("<h2>Guthaben: %04.2f</h2>", -($schulden/100));
  else
    $q.=sprintf("<h2>Schulden: %04.2f</h2>", $schulden/100);
  
  $q.= "<p class=text-muted>State: $scanner[current_state]  |  Timeout: $scanner[timeout] sec</p>";

}

load_view("header",[ "content" => $q ]);

