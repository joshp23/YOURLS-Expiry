document.getElementById('add-button').insertAdjacentHTML('afterend', '</br></br><label for="expiry"><strong>Short Link Expiration Type</strong>:</label><select name="expiry" id="expiry" data-role="slider" > Select One<option value="" selected="selected">None</option><option value="clock">Timer</option><option value="click" >Click Counter</option></select><div id="expiry_params" style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;display:none;"><div style="margin:auto;width:150px;text-align:left;" ><div id="tick_tock" style="display:none"><input style="width:50px;" type="number" name="age" id="age" value="" min="0"><select name="mod" id="mod" size="1" ><option value="" selected="selected">Select One</option><option value="min">Minutes</option><option value="hour">Hours</option><option value="day" >Days</option><option value="week">Weeks</option></select></div><div id="clip_clop" style="display:none"><input  style="width:50px;" type="number" name="count" id="count" min="0" > Click limit.</div></div><input type="text" id="postx" name="postx" class="text" size="40" placeholder="leave blank for none"/> <strong>Fallback URL</strong></div>');
document.getElementById('add-button').setAttribute('onclick',  'add_link_expiry();');
document.getElementById('expiry').addEventListener('change', function () {
	var style = this.value !== "" ? 'block' : 'none';
	document.getElementById('expiry_params').style.display = style;
	var style = this.value == "clock" ? 'block' : 'none';
	document.getElementById('tick_tock').style.display = style;
	var style = this.value == "click" ? 'block' : 'none';
	document.getElementById('clip_clop').style.display = style;
});
// Create new link with expiry data and add to table
function add_link_expiry() {
	if( $('#add-button').hasClass('disabled') ) {
		return false;
	}
	var newurl = $("#add-url").val();
	var nonce = $("#nonce-add").val();
	var expiry = $("#expiry").val();
	var age = $("#age").val();
	var mod = $("#mod").val();
	var count = $("#count").val();
	var postx = $("#postx").val();
	if ( !newurl || newurl == 'http://' || newurl == 'https://' ) {
		return;
	}
	var keyword = $("#add-keyword").val();
	add_loading("#add-button");
	$.getJSON(
		ajaxurl,
		{action:'add', url: newurl, keyword: keyword, expiry: expiry, age: age, mod: mod, count: count, postx: postx, nonce: nonce},
		function(data){
			if(data.status == 'success') {
				$('#main_table tbody').prepend( data.html ).trigger("update");
				$('#nourl_found').css('display', 'none');
				zebra_table();
				increment_counter();
				toggle_share_fill_boxes( data.url.url, data.shorturl, data.url.title );
			}

			add_link_reset();
			end_loading("#add-button");
			end_disable("#add-button");

			feedback(data.message, data.status);
		}
	);
}
