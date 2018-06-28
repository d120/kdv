
<span class="pull-right" style="margin-top: 20px;">
<a href="?m=newproduct" class="btn btn-success">Neues Produkt</a>
<a href="?m=stocktaking" class="btn btn-success">Inventur</a>
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
  <td><a href="?m=product&id=<?=$d["id"]?>" tabindex="-1"><?= ent($d["code"]) ?></a></td>
  <td>
    <?= ent($d["name"]) ?>
    <?php if (file_exists('productimages/'.$d['id'].'.jpg')) echo '<img src="'.BASE_URL.'productimages/'.$d["id"].'.jpg" style="max-width:72px;max-height:36px;float:right">'; ?>
  </td>
  <td><?= sprintf("%04.2f", $d["price"]/100) ?></td>
  <td id="stock_<?=$d['id']?>"><?= $d["bestand"] ?></td>
  <td>
  <?php if (is_string($action_buttons)) echo sprintf($action_buttons, $d["id"]); ?>
  <?php if (is_array($action_buttons)) foreach ($action_buttons as $btn){?>
  <a href="<?= sprintf($btn[1], $d["id"]) ?>" class="btn btn-default"><?=$btn[0]?></a>
  <?php } ?>
  </td>
</tr>
<?php endforeach; ?>

</table>

<script>
$("input[data-stocktake-prodid]").keyup(function(e) {
   if(e.which==13) {
       var id=this.getAttribute("data-stocktake-prodid");
       if(this.value=="")return;
       
       var dat={product_id: id, expr:this.value};
       if(/^[0-9][0-9+*-]*$/.test(this.value)) {
           dat.set_value = eval(this.value);
       }else if(/^[-+]?[0-9]+[0-9+*-]*$/.test(this.value)) {
           dat.add_value = eval("0"+this.value);
       }else {
           messageBar.show("error", "Invalid data input format", 1000); return;
       }
       if(!confirm(JSON.stringify(dat)))return;
       this.disabled=true;
       $.post("?m=stocktaking_update", dat, (x)=> {
           this.disabled=false;
           if(x.success) {
               messageBar.show("success", "Inventar aktualisiert", 1000);
               $("#stock_"+id).text(x.new_value);
               this.value="";
           } else {
               messageBar.show("error", "Konnte Inventar nicht aktualisieren: "+x.error, 1000);
           }
       }, "json");
   }
});
</script>
