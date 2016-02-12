<h2><?= $product["name"] ?></h2>
<p>Barcode: <?= $product["code"] ?></p>
<p>Preis: <?= sprintf("%.2f",$product["price"]/100) ?></p>
<table class="table table-bordered" style=width:auto>
<thead><tr><th>Datum</th><th>Anzahl</th></tr></thead><tbody>
<?php $sum=0; foreach($bestand as $d) : $sum += $d["product_amount"]; ?>
<tr><td><?= $d["timestamp"] ?></td><td align=right> <?= -$d["product_amount"] ?></td></tr>
<?php endforeach; ?>
</tbody><tfoot><tr><td>Derzeitiger Bestand: </td><td align=right><b><font size=4><?= -$sum?></font></b></td></tr></tfoot>
</table>

<h4>Auffüllung eintragen</h4>
<form action="<?=$action?>" method="post">
<input type="number" name="bestand" size=5>
<input type="submit" value="OK">
</form>

