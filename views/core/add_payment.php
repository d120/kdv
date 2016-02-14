<h2>Manuelle Buchung</h2>
<form action="<?= $action ?>" method="post" class="form">

<div class="row"><div class="col-sm-5">
<label for="ibetrag">Betrag:</label>
<div class="input-group">
  <input type="text" id="ibetrag" name="charge" <?= $product["price"] ? "disabled ".sprintf("value='%04.2f'", $product["price"]/100) : "" ?> class="form-control"> 
  <span class="input-group-addon" id="basic-addon2">Euro</span>
</div>

</div>

<div class="col-sm-5">
<label for="iprodukt">Produkt:</label> 

<?php if($product["price"]){?>
<input type="text" id="iprodukt" disabled value="<?= ent($product["name"]) ?>" class="form-control"></p>
<?php }else{ ?><br>
<button type="button" class="btn btn-default" data-transaction="deposit">Einzahlung</button>
<button type="button" class="btn btn-default" data-transaction="buy">Auszahlung / Kauf</button>
<input type="hidden" value="" name="what" id="tr">
<script>
var myBtn = $("button[data-transaction]");
myBtn.click(function() {
  myBtn.attr("class", "btn btn-default");
  this.className ="btn btn-primary";
  $("#tr").val(this.getAttribute("data-transaction"));
  $("button[type=submit]").attr("disabled", false);
});
</script>

<?php }?>

<input type="hidden" name="product_id" value="<?= ent($product["id"]) ?>">

</div></div>
<br>
<p><button <?=$product["price"]?"":"disabled"?> type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span>&nbsp;  Buchen</button>
<button type="button" class="btn btn-default" onclick="history.back()">Abbrechen</button>
</p>

</form>


