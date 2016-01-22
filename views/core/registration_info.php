
<h2>Account (<?= $user["email"] ?>)</h2>

<form action="<?= htmlspecialchars($action) ?>" method="post" class="form-horizontal">
<div class="form-group">
<label for="inputName" class="col-sm-2 control-label">Name: </label>
<div class="col-sm-6"><input type="text" name="fullname" value="<?=ent($user["fullname"])?>" class="form-control" id="inputName"></div>
</div>

<div class="form-group">
<label for="inputLimit" class="col-sm-2 control-label">Limit: </label>
<div class="col-sm-4"><input type="text" disabled value="<?=$user["max_debt"]/100?>" class="form-control" id="inputLimit" size=5></div>
</div>

<p><input type="submit" name="update" value="Speichern"></p>
</form>

<form action="<?= $action ?>" method="post" class="form-horizontal">
<div class="form-group">
<label for="inputPassword3" class="col-sm-2 control-label">Passwort ändern: </label>
<div class="col-sm-6"><input type="password" name="password" class="form-control" id="inputPassword3"></div>
</div>

<p><input type="submit" name="change_pw" value=" OK "></p>
</form>

<h4>Identitätskarten</h4>
<table>
<tr><th>Barcode</th><th>Aktion</th></tr>
<?php foreach($barcodes as $d): ?>
<tr><td><?= ent($d["code"])?></td><td>
<form action="<?= $action ?>" method="post"><input type="hidden" name="remove_barcode" value="<?= $d["id"] ?>"><input type="submit" value="Löschen"></form>
</td></tr>
<?php endforeach; ?>
</table>

<form action="<?= $action ?>" method="post">
<p><select name="scanner_id">
<?php foreach($scanners as $d) : ?><option><?= $d["id"]?></option><?php endforeach; ?>
</select>
<input type="submit" name="add_barcode" value="Neuen Barcode hinzufügen"></p>
</form>

