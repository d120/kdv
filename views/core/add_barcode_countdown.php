
Scanne deine Identitätskarte jetzt an Scanner <?=$scanner?>!
<div id=countdown style='font-size:18pt;'></div>

<script>
var tt=60;
var action=<?=json_encode($action)?>;
setInterval(function(){
  tt--;
  document.getElementById('countdown').innerHTML=tt;
  if(tt<1) location=action;
  $.post(action, { check_register_done: '<?=$scanner?>' }, function(ok) {
    if (ok == "yes") location=action;
  }, "text");
}, 1000);
</script>


