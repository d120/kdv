<?php if (!$mini) : ?>
<h2>Kontostand: 
<span style="color: <?= ($debt>0) ? "#d00" :( ($debt < 0) ? "#0b0" : "#000") ?>"><?= sprintf("%04.2f", -($debt/100)) ?></span>
</h2>
<?php endif; ?>

  <table border='1' class="table table-bordered table-striped" style="width:auto">
  <thead><tr><th>Datum</th><th>Barcode</th><th>Name</th><th>Price</th></tr></thead>
<?php  foreach($ledger as $d) { ?>
<?php $cls = ($d["storno"]) ? "storno" : ""; ?>
<?php printf("<tr class='%s'><td>%s</td><td>%s</td><td>%s</td><td>%04.2f</td></tr>", $cls, $d["timestamp"], $d["code"], ent($d["comment"]? $d["comment"] :$d["name"]), -($d["charge"]/100)); ?>
<?php } ?>
  </table>

