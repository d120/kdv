
<h2>KDV Login</h2>

<?php if($error): ?>
<div class="alert alert-error">
<h4>Fehler</h4>
<p><?= $error ?></p>
</div>
<?php endif; ?>

<form action="?login" method="post">
  <input type="email" name="email"> <input type="password" name="password">
  <input type="submit" value="Login">
</form>


