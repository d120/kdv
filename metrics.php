<?php
include("init.php");
include("stuff.php");

function print_prometheus_metric($name, $type, $labels, $value, $docstring="") {
    //echo "# HELP $name $docstring\n";
    //echo "# TYPE $name $type\n";
    echo "$name";
    if (count($labels)>0) {
        echo "{";
        foreach($labels as $k=>$v) echo "$k=\"".str_replace("\\", "\\\\", str_replace("\n", "\\n", str_replace("\"", "\\\"", "$v")))."\",";
        echo "}";
    }
    echo " ".(float)($value)."\n";
}


header("Content-Type: text/plain; version=0.0.4");

$prods = sql("SELECT id, name, category,
-(select sum(product_amount) from ledger where product_id=p.id and storno is null) kdv_in_stock_count,
(select sum(product_amount) from ledger where product_id=p.id and storno is null and product_amount>0) kdv_number_sold_sum,
(select sum(product_amount) from ledger where product_id=p.id and storno is null and product_amount<0) kdv_number_refilled_sum,
-(select sum(charge) from ledger where product_id=p.id and storno is null and product_amount>0) kdv_price_sold_total,
-(select sum(charge) from ledger where product_id=p.id and storno is null and product_amount<0) kdv_price_refilled_total
FROM products p 
WHERE disabled_at IS NULL ORDER BY category,name ", []);
foreach($prods as $p) {
    print_prometheus_metric("kdv_in_stock_count", "", ["id"=>$p["id"],"name"=>$p["name"],"category"=>$p["category"] ],
        $p["kdv_in_stock_count"]);
}
foreach($prods as $p) {
    print_prometheus_metric("kdv_number_sold_sum", "", ["id"=>$p["id"],"name"=>$p["name"],"category"=>$p["category"] ],
        $p["kdv_number_sold_sum"]);
}
foreach($prods as $p) {
    print_prometheus_metric("kdv_price_sold_total", "", ["id"=>$p["id"],"name"=>$p["name"],"category"=>$p["category"] ],
        $p["kdv_price_sold_total"]/100);
}
foreach($prods as $p) {
    print_prometheus_metric("kdv_number_refilled_sum", "", ["id"=>$p["id"],"name"=>$p["name"],"category"=>$p["category"] ],
        $p["kdv_number_refilled_sum"]);
}
foreach($prods as $p) {
    print_prometheus_metric("kdv_price_refilled_total", "", ["id"=>$p["id"],"name"=>$p["name"],"category"=>$p["category"] ],
        $p["kdv_price_refilled_total"]/100);
}

