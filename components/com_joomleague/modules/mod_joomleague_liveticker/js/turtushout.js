 $(document).ready(function(){
 	
 	$JoLe2("#turtushout-warning").remove();
 	$JoLe2.get("?action=turtushout_token",function(txt){
		$JoLe2("#turtushout-form").append('<input type="hidden" name="ts" value="'+txt+'" />');
	});
 	$JoLe2("#turtushout-form").show();
 	$JoLe2("#turtushout-status").show();
	$JoLe2("#turtushout-form").submit( function () {
		$JoLe2("#turtushout-status").html("Sending...");
		$JoLe2.get(turtushout_server_url +"?action=turtushout_shout&" + $JoLe2("#turtushout-form").serialize(),function(txt){
			if (txt == "Shouted!") {
				TurtushoutUpdate();
			} else {
				$JoLe2("#turtushout-status").html(txt);
			}
		});
		return false;
	});
	setTimeout(TurtushoutUpdate, turtushout_update_timeout);
 });

function TurtushoutUpdate() 
{
	$JoLe2("#turtushout-status").html("Aktualisierung l&auml;uft...");
	$JoLe2("#turtushout-shout").load(turtushout_server_url + "?action=turtushout_shouts", function() {
		$JoLe2("#turtushout-status").html("Spiele sind Aktualisiert");
	});

    setTimeout(TurtushoutUpdate, turtushout_update_timeout);
}
function TurtushoutDelete(id) {
	$JoLe2("#turtushout-status").html("Deleteing..");
	$JoLe2("#turtushout-status").load(turtushout_server_url + "?action=turtushout_del&sid=" + id, TurtushoutUpdate);
}
