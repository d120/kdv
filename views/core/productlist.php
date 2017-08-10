
<span class="pull-right" style="margin-top: 20px;">
<a href="?m=newproduct" class="btn btn-success">Neues Produkt</a>
<a href="?m=productlist&format=pdf" class="btn btn-primary">PDF-Export</a>
<a href="?m=productlist&format=tex" class="btn btn-primary">Tex-Export</a>
</span>

<h2>Produktliste</h2>
<hr>

<table class="table table-bordered">
<thead>
<tr><th>Code</th><th>Name</th><th>Preis</th><th>Bestand</th><th>Aktion</th></tr>
</thead>
<?php foreach($products as $d): 
$header=$d["category"];
if ($group!=$header) echo "<tr style='background:#f9f9f9'><td colspan=5 style='font:bold 12pt monospace'>&nbsp;".$header."</td></tr>"; $group=$header; ?>
<tr style='<?= $d['disabled_at'] ? 'color:#aaa;' : '' ?>'>
  <td><a href="?m=product&id=<?=$d["id"]?>"><?= ent($d["code"]) ?></a></td>
  <td>
    <?= ent($d["name"]) ?>
    <?php if (file_exists('productimages/'.$d['id'].'.jpg')) echo '<img src="'.BASE_URL.'productimages/'.$d["id"].'.jpg" style="max-width:72px;max-height:36px;float:right">'; ?>
  </td>
  <td><?= sprintf("%04.2f", $d["price"]/100) ?></td>
  <td><?= $d["bestand"] ?></td>
  <td>
  <?php foreach ($action_buttons as $btn){?>
  <a href="<?= sprintf($btn[1], $d["id"]) ?>" class="btn btn-default"><?=$btn[0]?></a>
  <?php } ?>
  </td>
</tr>
<?php endforeach; ?>

</table>

