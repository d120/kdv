
<h2>Überweisung</h2>
<form action="<?= $action ?>" method="post" class="form" id="transferform">

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

        <button onclick="searchuser()" class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span> Benutzer suchen</button>
        <br><br>
        <div id="userlist"></div>

    </div>
</div>

<br>
<p>
    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span>&nbsp;  Überweisen</button>
    <button type="button" class="btn btn-default" onclick="history.back()">Abbrechen</button>
</p>

</form>

<script>
$("#transferform").submit(function() {
  if (confirm("Überweisung in Höhe von "+$("#ibetrag").val()+" an Kontonummer "+$("#itarget").val()+" freigeben?")==false) return false;
});
function searchuser() {
  var mail=prompt("Bitte die E-Mail-Adresse des zu suchenden Benutzers eingeben:", "");
  if (!mail) return;
  $.get("<?=BASE_URL?>api.php/searchuser/?email=" + escape(mail), function(result) {
    if (!result.success) {
      alert("Nicht gefunden");
    } else {
      $("#itarget").val(result.account_id);
    }
  }, "json");
}
$.get("<?=BASE_URL?>api.php/last_contacts/", function(result) {
    result.accounts.forEach((x)=>{
        var btn = $("<input type='button'>");
        btn.val(x.fullname); btn.click(()=>{ $("#itarget").val(x.account_number); });
        $("#userlist").append(btn)//.append("<br>");
    });
}, "json");
</script>
