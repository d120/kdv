<?php if($_COOKIE["autologin"]) {?>
<iframe src="fslogin/?iframe=true"></iframe>
<?php } ?>

<h2>KDV Login</h2>

<?php if($error): ?>
<div class="alert alert-error">
<h4>Fehler</h4>
<p><?= $error ?></p>
</div>
<?php endif; ?>

<form action="?login" method="post" class="form-horizontal">
<div class="form-group">
  <label for="inputEmail3" class="col-sm-2 control-label">E-Mail-Adresse:</label>
    <div class="col-sm-10"> <input type="email" name="email"></div>
</div>
<div class="form-group">
<label for="inputPassword3" class="col-sm-2 control-label">Password</label>
    <div class="col-sm-10"> <input type="password" name="password"></div>
</div>
<div class="form-group">
  <div class="col-sm-offset-2 col-sm-10"><input type="submit" value="Login" class="btn btn-primary"></div>
</div>
</form>

<br><br><p>
<a href="fslogin" class="btn btn-success">Login mit FS-Account</a>
</p>
