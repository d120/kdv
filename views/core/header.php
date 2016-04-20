<!doctype html>
<head>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
  <link rel="shortcut icon" href="<?= BASE_URL ?>favicon.ico">
  <script src="<?= BASE_URL ?>jquery.min.js"></script>
  <meta name="viewport" content="width=device-width">
  <title><?= ent($title) ?></title>
</head>
<body>
<?php if ($navigation == "main"): ?>
  <nav class="navbar navbar-inverse navbar-static-top"><div class=container>
  <div class="navbar-header"><a class="navbar-brand" href="<?=BASE_URL?>">KDV</a></div>
  <ul class="nav navbar-nav pull-right">
    <li><a href="<?= BASE_URL ?>?m=registration"><?=ent($_SESSION["user"]["fullname"])?></a></li>
    <li> <a href="<?= BASE_URL ?>?m=logout">Logout</a></li>
  </ul>
  </div></nav>
  <?php $menu = array( [ "?m=ledger", "Kontoauszug" ], ["?m=add_payment", "Ein/Auszahlung" ] ,  ["?m=wire_transfer", "Überweisung" ] , ["?m=productlist", "Produktliste"]  , [ "?m=registration" , "Account" ] ); ?>
<?php elseif ($navigation == "admin"): ?>
  <nav class="navbar navbar-inverse navbar-static-top"><div class=container>
  <div class="navbar-header"><a class="navbar-brand" href="<?=BASE_URL?>admin/">KDV Admin</a></div>
  <ul class="nav navbar-nav pull-right"><li> <a href="<?= BASE_URL ?>">Logout</a></li></ul>
  <p class="navbar-text pull-right"><b><?= ent($displayname) ?></b></p>
  </div></nav>

  <?php $menu = array(  [ "?m=productlist" , "Products" ] , [ "?m=userlist", "User List" ] , [ "?m=transactions" , "Transaktionen" ], ["?m=scanners", "Scanner" ] ); ?>
<?php else: ?>
  <br>
<?php endif; ?>

<div class="container">


<ul class="nav nav-tabs nav-justified">
  <?php if($menu) foreach($menu as $m): list($href, $text) = $m; ?>
  <li class="<?= "?m=$menuactive" == $href ? "active" : "" ?>"><a href="<?= $href ?>"><?= ent($text) ?></a></li>
  <?php endforeach; ?>
</ul>

<?= $content ?>

</div>

<?php if ($navigation): ?>
<footer><div class="container">
<a href="<?= BASE_URL ?>admin/">Admin</a>
&bull;  <a href="https://git.fachschaft.informatik.tu-darmstadt.de/mweller/mwkdv">Open Source</a>
&bull;  <a href="https://git.fachschaft.informatik.tu-darmstadt.de/mweller/mwkdv/blob/master/apidocs.md">API Documentation</a>

</div></footer>
<?php endif; ?>
</body>
</html>
