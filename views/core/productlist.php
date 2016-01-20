
<table border>
<tr><th>Code</th><th>Name</th><th>Preis</th><th>Aktion</th></tr>
<?php foreach($products as $d): ?>
<tr><td><a href="?m=product&id=<?=$d["id"]?>"><?= $d["code"] ?></a></td>
<td><?= $d["name"] ?></td><td><?= sprintf("%04.2f", $d["price"]/100) ?></td>
<td><a href="?m=product&id=<?=$d["id"]?>">Bearbeiten</a></td>
</tr>
<?php endforeach; ?>

</table>

