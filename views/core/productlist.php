<br>
<table class="table table-striped">
<thead>
<tr><th>Code</th><th>Name</th><th>Preis</th><th>Bestand</th><th>Aktion</th></tr>
</thead>
<?php foreach($products as $d): ?>
<tr><td><a href="?m=product&id=<?=$d["id"]?>"><?= ent($d["code"]) ?></a></td>
<td><?= ent($d["name"]) ?></td><td><?= sprintf("%04.2f", $d["price"]/100) ?></td>
<td><?= $d["bestand"] ?></td>
<td>
<?php foreach ($action_buttons as $btn){?>
<a href="<?= sprintf($btn[1], $d["id"]) ?>" class="btn btn-default"><?=$btn[0]?></a>
<?php } ?>
</td>
</tr>
<?php endforeach; ?>

</table>

