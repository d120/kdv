<h2><?= $product["name"] ?></h2>

<!--zweispaltig--><div class="row"><div class="col-md-6">
<h3>Produktdaten</h3>

<form action="<?= $action ?>" method="post" class="form-horizontal">
<div class="form-group">
<label for="inputName" class="col-sm-4 control-label">Name: </label>
<div class="col-sm-8"><input type="text" name="name" value="<?= $product["name"] ?>" class="form-control"></div>
</div>

<div class="form-group">
<label for="inputCode" class="col-sm-4 control-label">Barcode: </label>
<div class="col-sm-8"><input type="text" name="code" value="<?= $product["code"] ?>" class="form-control"></div>
</div>

<div class="form-group">
<label for="inputPrice" class="col-sm-4 control-label">Preis: </label>
<div class="col-sm-8"><input type="number" name="price" value="<?= sprintf("%.2f",$product["price"]/100) ?>" class="form-control"></div>
</div>

<div class="form-group"><div class="col-sm-8 push-sm-4"><input type='submit' name='save' value='Speichern' class='btn btn-default'></div></div>
</form>



<h3>Auffüllung eintragen</h3>
<form action="<?=$action?>" method="post" class='form form-inline'>
<input type="number" name="bestand" size=5 class='form-control'>
<input type="submit" value="OK" class='btn btn-default'>
</form>
<!--zweite spalte--></div><div class="col-md-6">

<h3>Bestand</h3>
<table class="table table-bordered" style=width:auto>
<thead><tr><th>Datum</th><th>Anzahl</th></tr></thead><tbody>
<?php $sum=0; foreach($bestand as $d) : $sum += $d["product_amount"]; ?>
<tr><td><?= $d["timestamp"] ?></td><td align=right> <?= -$d["product_amount"] ?></td></tr>
<?php endforeach; ?>
</tbody><tfoot><tr><td>Derzeitiger Bestand: </td><td align=right><b><font size=4><?= -$sum?></font></b></td></tr></tfoot>
</table>


<!--ende zweispaltig--></div></div>

