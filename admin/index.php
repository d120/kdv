<?php
include "../init.php";
include "../stuff.php";
require_once "../SimpleImage.class.php";
header("Content-Type: text/html; charset=utf8");
session_start();

function userlist() {
  $users = sql("SELECT *, (select sum(charge) from ledger where user_id=u.id and storno is null) summe FROM users u ", []);
  $q="";
  $q.= "<br><table class='table table-bordered'>";
  $q.= "<thead><tr><th>Id</th><th>Email</th><th>Name</th><th>Kontostand</th><th>Aktion</th></tr></thead>";
  $summe=0.0;
  foreach($users as $d) {
    $q.= sprintf("<tr><td><a href='?m=user&id=%d'>%d</a></td><td><a href='?m=user&id=%d'>%s</a></td><td>%s</td><td><a class='btn btn-default btn-%s' href='?m=userledger&id=%d'>%04.2f</a></td><td><a href='?m=user&id=%d' class='btn btn-default'>Bearbeiten</a>  <a href='?m=add_payment&id=%d' class='btn btn-default'>Ein/Auszahlung</a></td></tr>",
      $d["id"], $d["id"], $d["id"], htmlentities($d["email"]), htmlentities($d["fullname"]), moneycolor($d["summe"]), $d["id"], -($d["summe"]/100), $d["id"], $d["id"]);
    if($d['id']!=1) $summe += $d["summe"];
  }
  $q.= "<tr><td></td><td>Guthabensumme</td><td></td><td>".-($summe/100)."</td><td></td></tr>";
  $q.= "</table>";
  $q.= "<hr><a href='?m=newuser'>Neuer User</a>";
  return $q;
}


function new_user() {
  if ($_POST["email"]) {
    sql("INSERT INTO users (email, debt_limit) VALUES(?, ?)", [ $_POST["email"], DEFAULT_DEBT_LIMIT ], true);
    $newId = sql("SELECT LAST_INSERT_ID() id", [], 1)["id"];
    header("Location: ".BASE_URL."admin/?m=user&id=$newId");
    exit;
  }
  $q.= "<form action='?m=newuser' method='post'>";
  $q.= "<input type=email name=email> <input type=submit value='Neuen User anlegen'>";
  $q.= "</form>";
  return $q;
}
function set_product_img($id, $filespec) {
  $id=intval($id);
  $img = new SimpleImage();
  $img->load($filespec);
  $img->resizeToHeight(300);
  $img->save("../productimages/".$id.".jpg");

}
function new_product() {
  if (count($_POST)) {
    if (!$_POST["price"] || !$_POST["name"]) die(json_encode(["success"=>false, "error"=>"ERR: Missing price or name"]));
    $price = (int)(floatval($_POST["price"])*100);
    sql("INSERT INTO products (price, name, code, category) VALUES(?,?,?,?)", [ $price, $_POST["name"], $_POST["code"], $_POST["category"] ], true);
    $id = sql("SELECT LAST_INSERT_ID() id", [], 1)["id"];
    if (is_uploaded_file($_FILES["productimage"]['tmp_name'])) {
      set_product_img($id, $_FILES["productimage"]['tmp_name']);
    }
    die(json_encode([ "success" => true, "product_id" => $id ]));
  }

  return get_view("new_product", [ "cats" => sql("SELECT DISTINCT category FROM products", []) ]);
}
function transactions() {
  $transactions = sql("SELECT l.id, l.user_id, u.fullname, l.timestamp, l.charge, p.name, p.code, l.storno FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id LEFT OUTER JOIN users u ON u.id = l.user_id ORDER BY timestamp DESC", []);
  return get_view("ledger_glob", [ "ledger" => $transactions ]);
}
function list_scanners() {
  $scanners = sql("SELECT * FROM scanners", []);
  return get_view("scanners", ["scanners" => $scanners]);
}
function edit_product() {
  if($_POST["save"]) {
    $price = (int)(floatval($_POST["price"])*100);
    $flags=0;
    if (isset($_POST["flags"]))
      foreach($_POST["flags"] as $k=>$v) $flags |= intval($k);
    $disable=$_POST["disable"]?"NOW()":"NULL";
    sql("UPDATE products SET name=?,price=?,`code`=?,category=?,flags=?,disabled_at=$disable WHERE id=?",
      [ $_POST["name"], $price, $_POST["code"], $_POST["category"], $flags, $_GET["id"] ], true);
    if (is_uploaded_file($_FILES["productimage"]['tmp_name'])) {
      set_product_img(intval($_GET["id"]), $_FILES["productimage"]['tmp_name']);
      messagebar("success", "Bild hochgeladen", 1000);
    }
    messagebar("success", "Produktdaten aktualisiert", 2000);
  }
  $p=sql("SELECT * FROM products WHERE id=?",[$_GET["id"]],1);
  if(!$p) {
    messagebar("error", "Produkt nicht gefunden", 5000);
    header("Location: ".BASE_URL."admin/?m=productlist"); exit;
  }
  if ($_POST["bestand"]) {
    sql("INSERT INTO ledger (product_id, product_amount, user_id, charge, timestamp) VALUES (?, ?, 1, ?, NOW())",
      [ $p["id"], -intval($_POST["bestand"]), (-intval($_POST["bestand"])) * $p["price"] ], true);
    messagebar("success", "Bestand angepasst um ".intval($_POST["bestand"])." Einheiten", 1000);
  }
  $ledger = sql("SELECT * FROM ledger WHERE product_id = ? AND storno IS NULL ORDER BY timestamp ASC", [ $p["id"] ]);

  return get_view("edit_product", ["product"=>$p, "bestand" => $ledger]);

}


$menuactive = $_GET["m"];
switch($_GET["m"]) {
  case "userlist": $q.=userlist(); break;
  case "user": $q.=show_registration($_GET["id"], true); $menuactive="userlist"; break;
  case "userledger": $q.=show_ledger($_GET["id"]); $menuactive="userlist"; break;
  case "newuser": $q.=new_user(); $menuactive="userlist"; break;
  case "newproduct": $q.=new_product(); $menuactive="productlist"; break;
  case "transactions": $q.= transactions(); break;
  case "productlist": $q.=productlist([[ "Bearbeiten", "?m=product&id=%d" ] ], true); break;
  case "add_payment": $q.=add_payment(intval($_GET["id"]), BASE_URL."admin/?m=userledger&id=".intval($_GET["id"])); break;
  case "scanners": $q.=list_scanners(); break;
  case "product": $q.=edit_product(); $menuactive="productlist"; break;
  case "storno": $q.=storno_payment($_GET["uid"], $_GET["payment_id"]); break;
  case "": case null: $q.= "<br><p class=text-muted>WHERE DO YOU WANT TO GO TODAY?™</p>"; break;
  default: $q.="<br><h2>Nicht gefunden</h2>"; break;
}

load_view("header", [ "content" => $q, "navigation" => "admin", "menuactive" => $menuactive, "displayname" => $_SERVER["AUTHENTICATE_DISPLAYNAME"], "msgbar" => $_SESSION["msgbar"] ]);
$_SESSION["msgbar"] = [];

