<?php
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
  load_view("login", []);
}

function show_ledger($uid) {
  $ledger = sql("SELECT l.timestamp, l.charge, p.name, p.code FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id WHERE user_id = ? ORDER BY timestamp DESC", [ $uid ]);
  echo "<table border='1'>";
  echo "<tr><th>Datum</th><th>Barcode</th><th>Name</th><th>Price</th></tr>";
  foreach($ledger as $d) {
    printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%04.2f</td></tr>", $d["timestamp"], $d["code"], $d["name"], -($d["charge"]/100));
  }
  echo "</table>";
}

function show_registration($uid) {
  $action = $_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"];
  if ($_POST["remove_barcode"]) {
    sql("DELETE FROM user_barcodes WHERE id = ? AND user_id = ?", [ intval($_POST["remove_barcode"]), $uid ], true);
  }
  if ($_POST["add_barcode"]) {
    sql("UPDATE scanners SET current_state='register', current_state_timeout=DATE_ADD(NOW(),interval 1 MINUTE), current_user_id=?", [ $uid ], true);
    echo "Scanne deine Identitätskarte jetzt an einem angeschlossenen Scanner! <div id=countdown style='font-size:18pt;'></div> <script> var tt=60; setInterval(function(){ tt--; document.getElementById('countdown').innerHTML=tt;if(tt<1) location=".json_encode($action)."; }, 1000); </script>";
    return;
  }
  if ($_POST["check_register_done"]) {

  }
  if ($_POST["update"]) {
    sql("UPDATE users SET fullname=?,iban=? WHERE id=?", [ $_POST["fullname"], $_POST["iban"], $uid ], true);
  }
  if ($_POST["password"]) {
    sql("UPDATE users SET password_hash=? WHERE id=?", [ password_hash($_POST["password"], PASSWORD_DEFAULT), $uid ], true);
  }
  $user = sql("SELECT * FROM users WHERE id = ?", [ $uid ], 1);
  $barcodes = sql("SELECT * FROM user_barcodes  WHERE user_id = ? ", [ $uid ]);
  load_view("registration_info", [ "action" => htmlspecialchars($action),
                 "user" => $user, "barcodes" => $barcodes ]);
}



