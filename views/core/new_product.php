<p><input type="text" id="code"></p>
<p><input type="text" id="price"></p>
<p><input type="text" id="name"></p>
<p><button id="send">Send</button></p>

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


