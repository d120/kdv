
<table>
<tr><th>ID</th><th>Token</th><th>Display URL</th></tr>

<?php foreach($scanners as $d): ?>
<?php $displayUrl = BASE_URL."display.php?scanner=".$d["id"]."&token=".md5($d["id"].$d["token"]); ?>
<tr><td><?= $d["id"]?></td><td><?= $d["token"]?></td><td><a href="<?=$displayUrl?>"><?=$displayUrl?></a></td></tr>
<?php endforeach; ?>
</table>


