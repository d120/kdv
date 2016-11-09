
<style>
  table td { padding: 10px 10px 10px 0; }
</style>
<form id="form">
<h2>Neues Produkt</h2>
<table>
<tr><td>Barcode: </td><td><input type="text" id="code" name="code"></td></tr>
<tr><td>Preis: </td><td><input type="text" id="price" name="price"></td></tr>
<tr><td>Produktname: </td><td><input type="text" id="name" name="name"></td></tr>
<tr><td>Kategorie: </td><td><select id="category" name="category"><option value="">(bitte auswählen)</option>
<?php foreach($cats as $d) echo "<option>".ent($d["category"])."</option>"; ?>
<option value="?">(andere)</option>
</select></td></tr>
<tr><td> </td><td><button type="button" id="send" class="btn btn-default">Send</button></td></tr>
</table>
</form>

<script>
var lastCode="";
/*setInterval(function() {
  if ($("#code").val()=="" || $("#code").val()!=lastCode) {
    $.get("../api.php/lastscanned/", function(code) {
      $("#code").val(code);$("#price").focus();lastCode=$("#code").val();
    }, "text");
  }
}, 5000);*/
$("#category").on("change", function() {
  if($(this).val()=="?")$(this).replaceWith("<input id=category>");
  $("#category").focus();
});
$("#code").on("keyup", function(e) {
  if (e.which==13) $("#name").focus();
});
$("#send").click(function() {
  $.post("?m=newproduct", $("#form").serialize(), function(done) {
    if ( done && done.success  === true) {
      $("#code").val(""); $("#name").val(""); $("#code").focus();
      messageBar.show("success", "Produkt angelegt", 2000);
    } else {
      alert("FEHLER");
    }
  }, "json")
    .error(function(err,a,b,c) {
      alert("Fehler: "+err);console.log(err,a,b,c);
    });
});
</script>


