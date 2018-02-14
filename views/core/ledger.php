<?php if (!$mini) : ?>
<h2>Kontostand: 
<span style="color: <?= ($debt>0) ? "#d00" :( ($debt < 0) ? "#0b0" : "#000") ?>"><?= sprintf("%04.2f", -($debt/100)) ?></span>
</h2>
<?php endif; ?>

  <table border='1' class="table table-bordered table-striped" style="width:auto">
  <thead><tr><th>Datum</th><th>Barcode</th><th>Name</th><th>Price</th><th></th></tr></thead>
<?php
$i = 0;
foreach($ledger as $d) {
  $cls = ($d["storno"]) ? "storno" : "";
  $storno_btn="";
  if ($i<3 && !$d["storno"]) $storno_btn = sprintf("<a href='#' onclick='return storno_payment(%d,%d)'>Stornieren</a>", $d["user_id"], $d["id"]);
  printf("<tr class='%s'><td>%s</td><td>%s</td><td class='col-comment'>%s</td><td>%04.2f</td><td>%s</td></tr>",
         $cls, $d["timestamp"], $d["code"], nl2br(ent($d["comment"]? $d["comment"] :$d["name"])), -($d["charge"]/100), $storno_btn);
  if (!$d["storno"]) $i++;
}
?>
  </table>

