<h2><?= $product["name"] ?></h2>

<!--zweispaltig--><div class="row"><div class="col-md-6">
<h3>Produktdaten</h3>

<form action="<?= $action ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
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
<div class="col-sm-8"><input type="number" step="any" name="price" value="<?= sprintf("%.2f",$product["price"]/100) ?>" class="form-control"></div>
</div>

<div class="form-group">
<label for="inputcategory" class="col-sm-4 control-label">Category: </label>
<div class="col-sm-8"><input type="text" id="inputcategory" name="category" value="<?= $product["category"] ?>" class="form-control"></div>
</div>

<div class="form-group">
<label for="inputimg" class="col-sm-4 control-label">Product image: </label>
<div class="col-sm-8"><input type="file" id="inputimg" name="productimage"></div>
</div>

<div class="form-group">
<label class="col-sm-4 control-label">Flags: </label>
<div class="col-sm-8">
<input type="checkbox" name="flags[1]" value="1" <?=$product["flags"]&1 ? "checked":""?>> Auf Startseite zeigen<br>
<input type="checkbox" name="disable" value="1" <?=$product["disabled_at"] ? "checked":""?>> Ausblenden
</div>
</div>


<div class="form-group"><div class="col-sm-8 push-sm-4"><input type='submit' name='save' value='Speichern' class='btn btn-default'></div></div>
</form>



<h3>Auffüllung eintragen</h3>
<form action="<?=$action?>" method="post" class='form form-inline'>
<input type="number" name="bestand" size=5 class='form-control'>
<input type="submit" value="OK" class='btn btn-default'>
</form>


<h3>Produktbild</h3>
<img src="<?=BASE_URL?>productimages/<?=$product["id"]?>.jpg" style="max-width: 80%">

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

