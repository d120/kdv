<!doctype html>
<head>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
  <link rel="shortcut icon" href="<?= BASE_URL ?>favicon.ico">
  <script src="<?= BASE_URL ?>jquery.min.js"></script>
  <script src="<?= BASE_URL ?>helper.js"></script>
  <meta name="viewport" content="width=device-width">
  <title><?= ent($title) ?></title>
</head>
<body>
<?php if ($navigation == "main"): ?>
  <nav class="navbar navbar-inverse navbar-static-top navbar-typ-<?=$navigation?>"><div class=container>
  <div class="navbar-header"><a class="navbar-brand" href="<?=BASE_URL?>"><img src="<?= BASE_URL ?>favicon.ico" width=24 height=24> KDV</a></div>
  <ul class="nav navbar-nav pull-right">
    <li><a href="<?= BASE_URL ?>?m=registration"><span class='glyphicon glyphicon-user'></span> <?=ent($_SESSION["user"]["fullname"])?></a></li>
    <li> <a href="<?= BASE_URL ?>?m=logout"><span class='glyphicon glyphicon-log-out'></span> Logout</a></li>
  </ul>
  </div></nav>
  <?php $menu = array( [ "?m=ledger", "Kontoauszug" ], ["?m=add_payment", "Ein/Auszahlung" ] ,  ["?m=wire_transfer", "Überweisung" ] , ["?m=productlist", "Produktliste"]  , [ "?m=registration" , "Account" ] ); ?>
<?php elseif ($navigation == "admin"): ?>
  <nav class="navbar navbar-inverse navbar-static-top navbar-typ-<?=$navigation?>"><div class=container>
  <div class="navbar-header"><a class="navbar-brand" href="<?=BASE_URL?>admin/">KDV Admin</a></div>
  <ul class="nav navbar-nav pull-right"><li> <a href="<?= BASE_URL ?>"><span class='glyphicon glyphicon-log-out'></span> Zum Frontend</a></a></li></ul>
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

<script>
var msgbar=<?=json_encode($msgbar)?>;
if (msgbar) {
  for(var i=0;i<msgbar.length;i++)
    messageBar.show(msgbar[i][0], msgbar[i][1], msgbar[i][2]);
}
</script>
</body>
</html>
