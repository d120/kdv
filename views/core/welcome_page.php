<h2>Moin moin, <?= $name ?></h2>

<h2>Beliebte Produkte</h2>
<div class="row">
  <?php foreach($def_products  as $p) : ?>
<div class="col-xs-6 col-md-2">
    <a href="<?=BASE_URL?>?m=add_payment&product_id=<?=$p["id"]?>" class="thumbnail" style="height:150px" title="<?=$p["name"]?> (Preis: <?=$p["price"]/100?> €) #<?=$p["mr"]?>">
    <?php if ($p["mc"]>10) echo "<span class='ctr'>$p[mc]</span>"; ?>
      <img src="<?=BASE_URL?>productimages/<?=$p["id"]?>.jpg" alt="<?=$p["name"]?>" style="max-height:140px;max-width:100%;">
    </a>
  </div>
  <?php endforeach; ?>
</div>


<h2>Apps</h2>
<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>Android-App</h3>
        <p>...</p>
        <p><a href="#" class="btn btn-primary" role="button">Download</a> <a href="https://git.d120.de/totterbein/kdvAPP" class="btn btn-default" role="button">Source code</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>Kommandozeilen-Client</h3>
        <p>...</p>
        <p><a href="https://git.d120.de/jtillmanns/kdv_cli" class="btn btn-default" role="button">Source code</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>Windows-Client</h3>
        <p>...</p>
        <p><a href="https://feldbergstr.dyn.max-weller.de/~mw/test/clickonce/KDV.NET/kdv_gui.application?<?=urlencode(BASE_URL)?>&<?=urlencode($apitoken)?>" class="btn btn-primary" role="button">Starten</a> <a href="https://git.d120.de/mweller/KDV.NET" class="btn btn-default" role="button">Source code</a></p>
      </div>
    </div>
  </div>
</div>
<!--
<?php var_dump($def_products);?>
-->
