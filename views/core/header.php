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
<div class="container">

<?php if ($navigation == "main"): ?>
  <br>
  <?php $menu = array( [ "?m=ledger", "Kontoauszug" ] , [ "?m=registration" , "Account" ] , [ "?m=logout" , "Logout" ] ); ?>
<?php elseif ($navigation == "admin"): ?>
  <h2>KDV Adminmodus <a href="<?= BASE_URL ?>" class="btn btn-primary pull-right">Frontend</a></h2>
  <?php $menu = array(  [ "?m=userlist", "User List" ] , [ "?m=productlist" , "Products" ] , [ "?m=transactions" , "Transaktionen" ], ["?m=scanners", "Scanner" ] ); ?>
<?php else: ?>
  <br>
<?php endif; ?>


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
</div></footer>
<?php endif; ?>
</body>
</html>
