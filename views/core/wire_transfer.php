
<h2>Überweisung</h2>
<form action="<?= $action ?>" method="post" class="form">

<div class="row">
    <div class="col-sm-5">
        <p>
        <label for="ibetrag">Betrag:</label>
        <div class="input-group">
          <input type="text" id="ibetrag" pattern="[0-9,]+" required name="charge" class="form-control"> 
          <span class="input-group-addon" id="basic-addon2">Euro</span>
        </div>
        </p>

        <p><label for="verwendungszweck">Verwendungszweck:</label> 
        <input type="text" id="verwendungszweck" class="form-control" name="verwendungszweck"></p>



    </div>

    <div class="col-sm-5">
        <p><label for="itarget">Empfänger-Kontonummer:</label> 
        <input type="text" required id="itarget" pattern="[0-9]{7}" class="form-control" name="transfer_to"></p>




    </div>
</div>

<br>
<p>
    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span>&nbsp;  Überweisen</button>
    <button type="button" class="btn btn-default" onclick="history.back()">Abbrechen</button>
</p>

</form>


