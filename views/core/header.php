<!doctype html>
<head>
<link rel="stylesheet" href="<?= BASE_URL ?>bootstrap.min.css">
<script src="<?= BASE_URL ?>jquery.min.js"></script>
<meta name="viewport" content="width=device-width">
<style> footer { background: #eee; padding: 10px 0; margin: 50px 0 0; } 
tr.storno td { text-decoration: line-through; color: #666; }
</style>
</head>
<body>
<?php if ($navigation == "main"): ?>
  <nav class="navbar navbar-inverse navbar-static-top"><div class=container>
  <div class="navbar-header"><a class="navbar-brand" href="<?=BASE_URL?>">KDV</a></div>
  <ul class="nav navbar-nav pull-right"><li> <a href="<?= BASE_URL ?>?m=logout">Logout</a></li></ul>
  <p class="navbar-text pull-right">Hallo, <?=$_SESSION["user"]["fullname"]?></p>
  </div></nav>
  <?php $menu = array( [ "?m=ledger", "Kontoauszug" ], ["?m=add_payment", "Ein/Auszahlung" ] , ["?m=productlist", "Produktliste"]  , [ "?m=registration" , "Account" ] ); ?>
<?php elseif ($navigation == "admin"): ?>
  <nav class="navbar navbar-inverse navbar-static-top"><div class=container>
  <div class="navbar-header"><a class="navbar-brand" href="<?=BASE_URL?>admin/">KDV Admin</a></div>
  <ul class="nav navbar-nav pull-right"><li> <a href="<?= BASE_URL ?>">Logout</a></li></ul>
  </div></nav>

  <?php $menu = array(  [ "?m=userlist", "User List" ] , [ "?m=productlist" , "Products" ] , [ "?m=transactions" , "Transaktionen" ], ["?m=scanners", "Scanner" ] ); ?>
<?php else: ?>
  <br>
<?php endif; ?>

<div class="container">


<ul class="nav nav-tabs nav-justified">
  <?php if($menu) foreach($menu as $m): list($href, $text) = $m; ?>
  <li class="<?= "?m=$menuactive" == $href ? "active" : "" ?>"><a href="<?= $href ?>"><?= $text ?></a></li>
  <?php endforeach; ?>
</ul>

<?= $content ?>

</div>

<?php if ($navigation): ?>
<footer><div class="container">
<a href="<?= BASE_URL ?>admin/">Admin</a>
&bull;  <a href="https://git.fachschaft.informatik.tu-darmstadt.de/mweller/mwkdv">Open Source</a>
</div></footer>
<?php endif; ?>
</body>
</html>
