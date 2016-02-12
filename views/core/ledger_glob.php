<h2>Kontoauszug</h2>
  <table class="table table-bordered table-striped">
  <thead><tr><th>Datum</th><th>User Id</th><th>Produktname</th><th>Price</th></tr></thead>
<?php  foreach($ledger as $d) { ?>
<?php 
$cls="";
if ($d["storno"]) $cls.="storno ";
printf("<tr class='%s'><td>%s</td><td><a href='?m=user&id=%d'>%d</a></td><td>%s</td><td>%04.2f</td></tr>", $cls, $d["timestamp"], $d["user_id"], $d["user_id"], $d["name"], -($d["charge"]/100)); ?>
<?php } ?>
  </table>

