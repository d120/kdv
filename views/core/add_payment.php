<h2>Manuelle Buchung</h2>
<form action="<?= $action ?>" method="post" class="form">

<div class="row"><div class="col-sm-5">
<label for="ibetrag">Betrag:</label>
<div class="input-group">
  <input type="text" id="ibetrag" name="charge" <?= $product["price"] ? "disabled ".sprintf("value='%04.2f'", $product["price"]/100) : "" ?> class="form-control"> 
  <span class="input-group-addon" id="basic-addon2">Euro</span>
</div>

<?php if(!$product["price"]){?>
<p class="help-block">Negativer Betrag: Einzahlung in die Kasse<br> Positiver Betrag: Auszahlung / Produktkauf</p>
<?php }?>
</div>

<div class="col-sm-5">
<label for="iprodukt">Produkt:</label> <input type="text" id="iprodukt" disabled value="<?= ent($product["name"]) ?>" class="form-control"></p>
<input type="hidden" name="product_id" value="<?= ent($product["id"]) ?>">

</div></div>

<p><button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span>&nbsp;  Buchen</button></p>

</form>


