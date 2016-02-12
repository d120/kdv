
<h2>Account (<?= ent($user["email"]) ?>)</h2>

<div class="row"><div class="col-md-6">

<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Accountdaten</h3></div><div class="panel-body">

<form action="<?= htmlspecialchars($action) ?>" method="post" class="form-horizontal">
<div class="form-group">
<label for="inputName" class="col-sm-4 control-label">Name: </label>
<div class="col-sm-8"><input type="text" name="fullname" value="<?=ent($user["fullname"])?>" class="form-control" id="inputName"></div>
</div>

<div class="form-group">
<label for="inputLimit" class="col-sm-4 control-label">E-Mail: </label>
<div class="col-sm-8"><input type="text" <?=$admin?"":"readonly"?> name="email" value="<?=ent($user["email"])?>" class="form-control" id="inputLimit" size=5></div>
</div>

<div class="form-group">
<label for="inputLimit" class="col-sm-4 control-label">Limit: </label>
<div class="col-sm-8"><input type="text" <?=$admin?"":"readonly"?> name="debt_limit" value="<?=sprintf("%.2f",$user["debt_limit"]/100)?>" class="form-control" id="inputLimit" size=5></div>
</div>

<p><input type="submit" name="update" value="Speichern" class="btn btn-primary"></p>
</form>
</div></div>

<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Passwort ändern</h3></div><div class="panel-body">

<form action="<?= $action ?>" method="post" class="form-horizontal">
<div class="form-group">
<label for="inputPassword3" class="col-sm-4 control-label">Neues Passwort: </label>
<div class="col-sm-8"><input type="password" name="password" class="form-control" id="inputPassword3"></div>
</div>

<p><input type="submit" name="change_pw" value=" OK " class="btn btn-primary"></p>
</form>
</div></div>

</div><div class="col-md-6">

<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Identitätskarten</h3></div>
<div class="panel-body">
<table class="table table-lined">
<tr><th>Barcode</th><th>Aktion</th></tr>
<?php foreach($barcodes as $d): ?>
<tr><td><?= ent($d["code"])?></td><td>
<form action="<?= $action ?>" method="post"><input type="hidden" name="remove_barcode" value="<?= $d["id"] ?>"><button type="submit" class="btn btn-warning"><span class="glyphicon glyphicon-trash"></span></button></form>
</td></tr>
<?php endforeach; ?>
</table>

<form action="<?= $action ?>" method="post" class=" form-inline">
<p><select name="scanner_id" class="form-control">
<?php foreach($scanners as $d) : ?><option value="<?=$d["id"]?>">Scanner <?= $d["id"]?></option><?php endforeach; ?>
</select>
<input type="submit" name="add_barcode" value="Neuen Barcode hinzufügen" class="btn btn-success"></p>
</form>
</div></div>

</div></div>


