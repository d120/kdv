<?php
include "init.php";
include "stuff.php";
$url = json_encode("api.php/display/?scanner=$_GET[scanner]&token=$_GET[token]&");

$q.=<<<scr
<script>
var url = $url;
var t1=0;
setInterval(function() {
  $.get(url + "t1=" + t1  , function(x) {
    if (x.html) {
      t1=x.t1;
      $("#cont").html(x.html);
    }
  }, "json");
}, 2000);
function fmt(x) { if (x<10) return "0"+x; else return x; }
setInterval(function() {
  var d = new Date();
  $("#time").html(fmt(d.getHours())+":"+fmt(d.getMinutes())+":"+fmt(d.getSeconds()));
}, 1000);
</script>
<div id="cont"></div>
<div id="time" style="text-align:center;font-size:20pt"></div>
scr;
load_view("header",[ "content" => $q ]);

