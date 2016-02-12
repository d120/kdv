
<h2>Scanners</h2>
<table class="table table-bordered">
<thead><tr><th>ID</th><th>Token</th><th>Display URL</th><th>Test</th></tr></thead>

<?php foreach($scanners as $d): ?>
<?php $displayUrl = BASE_URL."display.php?scanner=".$d["id"]."&token=".md5($d["id"].$d["token"]); ?>
<tr><td class="scannerid"><?= $d["id"]?></td><td class="token"><?= $d["token"]?></td><td><a href="<?=$displayUrl?>"><?=$displayUrl?></a></td>
<td><input type="text" class="form-control send_scan" placeholder="type barcode and press return" size=45></td>
</tr>
<?php endforeach; ?>
</table>
<pre id="result"></pre>
<script>
$(".send_scan").keyup(function(e){
  if(e.which==13) {
    $("#result").text("Eile mit Weile");
    var $row=$(this).closest("tr");
    var scanner=$row.find(".scannerid").html();
    var token=$row.find(".token").html();
    $.post("../api.php/barcodedrop/", { barcode: $(this).val(), scanner: scanner, scanner_token: token },
      function(result) {
        $("#result").text(result);
    }, "text");
  }
});
</script>

