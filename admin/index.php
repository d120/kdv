<?php
include "../init.php";
include "../stuff.php";
header("Content-Type: text/html; charset=utf8");

function userlist() {
  $users = sql("SELECT *, (select sum(charge) from ledger where user_id=u.id) summe FROM users u ", []);
  echo "<table>";
  echo "<tr><th>Id</th><th>Email</th><th>Name</th><th>Guthaben/Schulden</th></tr>";
  foreach($users as $d) {
    echo "<tr><td><a href='?m=user&id=".$d["id"]."'>".$d["id"]."</a></td><td><a href='?m=user&id=".$d["id"]."'>".htmlentities($d["email"])."</a></td><td>".htmlentities($d["fullname"])."</td><td><a href='?m=userledger&id=".$d["id"]."'>".sprintf("%04.2f", -($d["summe"]/100))."</a></td></tr>";
  }
  echo "</table>";
  echo "<hr><a href='?m=newuser'>Neuer User</a>";
}

function new_user() {
  if ($_POST["email"]) {
    sql("INSERT INTO users (email) VALUES(?)", [ $_POST["email"] ], true);
    $newId = sql("SELECT LAST_INSERT_ID() id", [], 1)["id"];
    header("Location: ".BASE_URL."admin/?m=user&id=$newId");
    exit;
  }
  echo "<form action='?m=newuser' method='post'>";
  echo "<input type=email name=email> <input type=submit value='Neuen User anlegen'>";
  echo "</form>";
}

function productlist() {
  $prods = sql("SELECT * FROM products ", []);
  load_view("productlist", ["products" => $prods]);
}

echo "<p><a href='".BASE_URL."'>Home</a> | <a href='?m=userlist'>User list</a> | <a href='?m=productlist'>Products</a></p>";

switch($_GET["m"]) {
  case "userlist": userlist(); break;
  case "user": show_registration($_GET["id"]); break;
  case "userledger": show_ledger($_GET["id"]); break;
  case "newuser": new_user(); break;
  case "productlist": productlist(); break;
  default: echo "Willkommen"; break;
}

