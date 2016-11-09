
//==>
//==> MessageBar helper

function MessageBar() {
    this.show = function(className, text, interval, isHtml) {
      var id = "loadingWidget_" + className;
      if ($('#'+id).length == 0)
        $('<div id="'+id+'" class="messageBar"></div>').prependTo("body").click(function(){messageBar.hide(className)});
      var $el = $('#'+id);
      if (isHtml) $el.html(text); else $el.text(text);
      $el.addClass(className).slideDown();
      if (interval) setInterval(function() { messageBar.hide(className); }, interval);
    };
    this.hide = function(className) {
      $("#loadingWidget_"+className).slideUp();
    };
    var loading = $("<div class='progressBar'></div>").prependTo("body").hide();
    
    $(document).ajaxStart(function() {
        loading.show();
    });
    $(document).ajaxStop(function() {
        loading.hide();
    });
    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        console.log("ajaxError",event,jqxhr,thrownError);
        if (event.data) {
            messageBar.show("error", "Fehler: " + event.data.error, 3000);
        } else if (jqxhr.status) {
            messageBar.show("error", "Allgemeiner Fehler: " + jqxhr.status + " " + jqxhr.statusText, 3000);
        } else {
            messageBar.show("error", "Exception: " + thrownError, 3000);
        }
    });
    
};

//==>
//==> Context menu helper

function ShowContextMenu(event, menuItems) {
    $(".ddmenu.context").remove();
    var menu = $("<div class='ddmenu context'></div>");
    for(var k in menuItems) {
        var item = $("<div>"+k+"</div>").appendTo(menu);
        item.click(menuItems[k]);
    }
    $(document.body).append(menu);
    var x = event.pageX, y = event.pageY, xx = menu.width(), yy = menu.outerHeight();
    if (x+xx > window.innerWidth) x -= xx;
    if (y+yy > window.innerHeight) y -= yy;
    
    menu.css({ top: y + "px", left: x + "px" }).slideDown();
    setTimeout(function() {
      $(document).one("click", function(e) {
        menu.remove(); e.preventDefault();
      })
      $(document).one("contextmenu", function(e) {
        menu.remove(); e.preventDefault();
      })
    },1)
}
function CloseContextMenu(event, menuItems) {
    $(".ddmenu.context").remove();
}

window.messageBar = new MessageBar();


function storno_payment(user_id, payment_id) {
  if (!confirm("Zahlung stornieren?"))return;
  $.post("?m=storno&uid="+user_id+"&payment_id="+payment_id, {'storno':true}, function(ok) {
    if (ok.success) messageBar.show("success", "Zahlung wurde storniert", 2000);
    else messageBar.show("error", "Zahlung stornieren fehlgeschlagen: "+ok.error, 10000);
  }, "json");
  return false;
}

