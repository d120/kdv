
<h2>Account (<?= $user["email"] ?>)</h2>

<form action="<?= htmlspecialchars($action) ?>" method="post">
<p>Name: <input type="text" name="fullname" value="<?=$user["fullname"]?>"></p>
<p>IBAN: <input type="text" name="iban" value="<?=$user["iban"]?>"></p>
<p><input type="submit" name="update" value="Speichern"></p>
</form>

<form action="<?= $action ?>" method="post">
<p>Passwort ändern: <input type="password" name="password"></p>
<p><input type="submit" name="change_pw" value=" OK "></p>
</form>

<h4>Identitätskarten</h4>
<table>
<tr><th>Barcode</th><th>Aktion</th></tr>
<?php foreach($barcodes as $d): ?>
<tr><td><?= $d["code"]?></td><td>
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

