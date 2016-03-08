
<style>
  table td { padding: 10px 10px 10px 0; }
</style>

<h2>Neues Produkt</h2>
<table>
<tr><td>Barcode: </td><td><input type="text" id="code"></td></tr>
<tr><td>Preis: </td><td><input type="text" id="price"></td></tr>
<tr><td>Produktname: </td><td><input type="text" id="name"></td></tr>
<tr><td> </td><td><button id="send" class="btn btn-default">Send</button></td></tr>
</table>

<script>
var lastCode="";
setInterval(function() {
  if ($("#code").val()=="" || $("#code").val()!=lastCode) {
    $.get("../api.php/lastscanned/", function(code) {
      $("#code").val(code);$("#price").focus();lastCode=$("#code").val();
    }, "text");
  }
}, 5000);
$("#send").click(function() {
  $.post("../api.php/regproduct/", {
    code: $("#code").val(), price: $("#price").val(), name: $("#name").val()
  }, function(done) {
    $("#code").val(""); $("#name").val(""); $("#code").focus();
  }, "text");
});
</script>


