<?php
include "../init.php";
include "../stuff.php";
header("Content-Type: text/html; charset=utf8");

function userlist() {
  $users = sql("SELECT *, (select sum(charge) from ledger where user_id=u.id and storno is null) summe FROM users u ", []);
  $q="";
  $q.= "<br><table class='table table-bordered'>";
  $q.= "<tr><th>Id</th><th>Email</th><th>Name</th><th>Guthaben/Schulden</th><th>Aktion</th></tr>";
  foreach($users as $d) {
    $q.= sprintf("<tr><td><a href='?m=user&id=%d'>%d</a></td><td><a href='?m=user&id=%d'>%s</a></td><td>%s</td><td class='%s'><a href='?m=userledger&id=%d'>%04.2f</a></td><td><a href='?m=userledger&id=%d'>Kontoauszug</a> | <a href='?m=add_payment&id=%d'>Ein/Auszahlung</a></td></tr>",
        $d["id"], $d["id"], $d["id"], htmlentities($d["email"]), htmlentities($d["fullname"]), moneycolor($d["summe"]), $d["id"], -($d["summe"]/100), $d["id"], $d["id"]);
  }
  $q.= "</table>";
  $q.= "<hr><a href='?m=newuser'>Neuer User</a>";
  return $q;
}


function add_payment() {
  $user = sql("SELECT * FROM users WHERE id = ?", [$_GET["id"]], 1);
  if (!$user) return "Bad user id";
  if ($_POST["charge"]) {
    $charge = floatval(str_replace(",",".",$_POST["charge"])) * 100;
    sql("INSERT INTO ledger (user_id, product_id, charge) VALUES (?, 1, ?)", [ $_GET["id"], $charge ], true);
    header("Location: ".BASE_URL."admin/?m=userledger&id=".intval($_GET["id"]));
    exit;
  }
  return get_view("add_payment", [ "user" => $user ]);
}

function new_user() {
  if ($_POST["email"]) {
    sql("INSERT INTO users (email) VALUES(?)", [ $_POST["email"] ], true);
    $newId = sql("SELECT LAST_INSERT_ID() id", [], 1)["id"];
    header("Location: ".BASE_URL."admin/?m=user&id=$newId");
    exit;
  }
  $q.= "<form action='?m=newuser' method='post'>";
  $q.= "<input type=email name=email> <input type=submit value='Neuen User anlegen'>";
  $q.= "</form>";
  return $q;
}

function transactions() {
  $transactions = sql("SELECT l.user_id, u.fullname, l.timestamp, l.charge, p.name, p.code, l.storno FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id LEFT OUTER JOIN users u ON u.id = l.user_id ORDER BY timestamp DESC", []);
  return get_view("ledger_glob", [ "ledger" => $transactions ]);
}

function productlist() {
  $prods = sql("SELECT * FROM products ", []);
  return get_view("productlist", ["products" => $prods]);
}

function list_scanners() {
  $scanners = sql("SELECT * FROM scanners", []);
  return get_view("scanners", ["scanners" => $scanners]);
}

$menuactive = $_GET["m"];;
switch($_GET["m"]) {
  case "userlist": $q.=userlist(); break;
  case "user": $q.=show_registration($_GET["id"]); $menuactive="userlist"; break;
  case "userledger": $q.=show_ledger($_GET["id"]); $menuactive="userlist"; break;
  case "newuser": $q.=new_user(); $menuactive="userlist"; break;
  case "transactions": $q.= transactions(); break;
  case "productlist": $q.=productlist(); break;
  case "add_payment": $q.=add_payment(); break;
  case "scanners": $q.=list_scanners(); break;
  default: $q.= "Willkommen"; break;
}

load_view("header", [ "content" => $q, "navigation" => "admin", "menuactive" => $menuactive ]);

