<?php
include "init.php";
include "stuff.php";
header("Content-Type: text/plain; charset=utf8");

function diejsonerror($err, $code=500) {
    http_response_code($code);
    echo json_encode(["error" => "$code", "message" => $err]);
    die();
}

$routes = [];

header("Content-Type: application/json; charset=utf8");
if (strpos($_SERVER['CONTENT_TYPE'],'application/json')!==false) {
  $_POST = json_decode(file_get_contents('php://input'), true);
}
if (strpos($_SERVER["REMOTE_ADDR"], API_V2_IP_RESTRICT) !== 0 && !($_SERVER["PHP_AUTH_USER"] == "strichliste" && $_SERVER["PHP_AUTH_PW"] == API_V2_TOKEN)) {
  var_dump($_SERVER);
  header("WWW-Authenticate: Basic realm=\"strichliste\"");
  diejsonerror("Forbidden! $_SERVER[REMOTE_ADDR]", 401);
}
$routes['^GET /user$'] = 'api_list_users';
function api_list_users($route) {
  $users = sql("SELECT id, fullname name, coalesce(-(select sum(charge) from ledger where user_id=u.id and storno is null)/100,0) balance, (select max(timestamp) from ledger where user_id=u.id) lastTransaction FROM users u where id<>1", []);
  echo json_encode(["overallCount"=>count($users), "limit"=>null, "offset"=>null, "entries"=>$users]);
}

$routes['^GET /settings$'] = 'api_settings';
function api_settings($route) {
  echo json_encode(["boundaries"=>["upper"=>999999,"lower"=>-10]]);
}

function get_transaction_list($userid, $limit, $offset = 0, $calc_rows='') {
  $ledger = sql("SELECT $calc_rows l.timestamp createDate, -l.charge/100 value, l.id, concat(p.name, ' ',l.comment) text FROM ledger l LEFT OUTER JOIN products p ON l.product_id=p.id 
  WHERE user_id = ? and storno is null 
  ORDER BY timestamp DESC LIMIT $limit OFFSET $offset ", [ $userid ]);
  return $ledger;
}

$routes['^GET /user/(\d+)$'] = 'api_show_user';
function api_show_user($route, $userid) {
  $debt = get_user_debt($userid);
  $ledger = get_transaction_list($userid, 5);
  $info = sql("SELECT id, fullname name FROM users u where id=?", [$userid], 1);
  $info["balance"] = -$debt/100;
  $info["transactions"] = $ledger;
  echo json_encode($info);
}

$routes['^GET /user/(\d+)/transaction$'] = 'api_show_user_transactions';
function api_show_user_transactions($route, $userid) {
  $limit = max(5, min(100, intval($_GET['limit'])));
  $offset = intval($_GET['offset']);
  $ledger = get_transaction_list($userid, $limit, $offset, 'SQL_CALC_FOUND_ROWS');
  $totalCount = sql("SELECT FOUND_ROWS() value;", [], 1);
  echo json_encode(["overallCount"=>$totalCount['value'], "limit"=>$limit, "offset"=>$offset, "entries"=>$ledger]);
}

$routes['^POST /user/(\d+)/transaction$'] = 'api_create_user_transactions';
function api_create_user_transactions($route, $userid) {
  $product_id = intval($_POST["product_id"]);
  if (!$product_id) $product_id = PRODID_CASHPAYMENT;
  $product = sql("SELECT * FROM products WHERE id = ?", [ $product_id ], 1);
  $product_amount = 1;
  if ($product_id == PRODID_CASHPAYMENT) {
    $product["price"] = - floatval($_POST["value"]) * 100;
    if ($product["price"] == 0) diejsonerror("Ungültiger Betrag");
  } else {
    $product_amount = intval($_POST["product_amount"]);
    if($product_amount<1) $product_amount=1;
    $product["price"] = $product_amount * $product["price"];
  }

  $ok = buy_product($userid, $product, "", null, $transaction_id, $product_amount);
  if ($ok !== true) {
    diejsonerror($ok);
  }
  echo json_encode(["id"=>$transaction_id, "userId"=>$userid, "createDate"=>time(), value=>-$product["price"]/100]);
}

$routes['^GET /product$'] = 'api_list_products';
function api_list_products($route) {
  $products = sql("SELECT id, category, name, price, position, flags, code,
  (select max(timestamp) from ledger where user_id=products.id )  lastBuy
  FROM products WHERE disabled_at IS NULL order by category, name", []);
  foreach($products as &$d) {
    $d["price"] = $d["price"]/100;
    if (file_exists("productimages/$d[id].jpg"))
      $d["imageUrl"] = BASE_URL."productimages/".$d["id"].".jpg";
    else $d["imageUrl"] = null;
  }
  echo json_encode(["overallCount"=>count($products), "limit"=>null, "offset"=>null, "entries"=>$products]);
}

$routes['^GET /metrics$'] = 'api_get_metrics';
function api_get_metrics($route) {
  $m['countTransactions'] = sql("select count(*) value from ledger where user_id<>1 ", [], 1)['value'];
  $m['countUsers'] = sql("select count(*) value from users where id<>1 ", [], 1)['value'];
  $m['overallBalance'] = sql("select -sum(charge)/100 value from ledger where user_id<>1 and storno is null", [], 1)['value'];
  $m['avgBalance'] = $m['overallBalance']/$m['countUsers'];
  
  echo json_encode($m);
}


$p = $_SERVER["REQUEST_METHOD"]." ".$_SERVER["PATH_INFO"];
foreach($routes as $route=>$fn) {
    if (preg_match('#'.$route.'#', $p, $matches)) {
        call_user_func_array($fn, $matches);
        exit;
    }
}

http_response_code(404);
echo json_encode(["error"=>"404","message"=>"not implemented"]);

