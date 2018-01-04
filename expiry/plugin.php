<?php
/*
Plugin Name: Expiry
Plugin URI: https://github.com/joshp23/YOURLS-Expiry
Description: Will set expiration conditions on your links (or not)
Version: 0.11.1
Author: Josh Panter
Author URI: https://unfettered.net
*/
// TODO expiry data in stats page
// TODO fix the fine js
// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();
/*
 *
 * ADMIN PAGE
 *
 *
*/
// Register admin forms
yourls_add_action( 'plugins_loaded', 'expiry_add_pages' );
function expiry_add_pages() {
        yourls_register_plugin_page( 'expiry', 'Expiry', 'expiry_do_page' );
}
function expiry_do_page() {

	expiry_update_ops();
	expiry_flush();

	$opt = expiry_config();

	// neccessary values for display
	$globalExp = array("none" => " ", "click" => " ", "clock" => " ");
	switch ($opt[5]) {
		case 'click': $globalExp['click'] = 'selected'; break;
		case 'clock': $globalExp['clock'] = 'selected'; break;
		default:      $globalExp['none']  = 'selected'; break;
	}

	$ageMod = array("min" => " ", "day" => " ", "hour" => " ", "week" => " ");
	switch ($opt[7]) {
		case 'min':  $ageMod['min']  = 'selected'; break;
		case 'hour': $ageMod['hour'] = 'selected'; break;
		case 'week': $ageMod['week'] = 'selected'; break;
		default:     $ageMod['day']  = 'selected'; break;
	}

	$intercept = array("simple" => " ", "custome" => " ", "template" => " ");
	switch ($opt[0]) {
		case 'template': $intercept['template'] = 'selected'; break;
		case 'custome': $intercept['custome'] = 'selected'; break;
		default:      $intercept['simple']  = 'selected'; break;
	}

	$ciVisChk = ( $opt[0] !== 'custome' ? 'none' : 'inline' );

	$unique = ( 'YOURLS_UNIQUE_URLS' == true ) ? ' disabled="disabled" <p><strong>Notice:</strong> <code>YOURLS_UNIQUE_URLS</code> is set to <code>true</code>. This value must be set to <code>false</code> to use this function.</p>' : ' > Use a global post-expiration URL?';
	
	if( $opt[5] == 'false' ) {
		$gpxVisChk = 'none';
		$gpxChk = null;
	} else {
		$gpxVisChk = 'inline';
		$gpxChk = 'checked';
	}
	
	$expChk = ( $opt[3] == 'true' ? 'checked' : null );
	$tblChk = ( $opt[2] == 'true' ? 'checked' : null );

	// Create nonce
	$nonce = yourls_create_nonce( 'expiry' );
		

echo <<<HTML
	<div id="wrap">
		<div id="tabs">

			<div class="wrap_unfloat">
				<ul id="headers" class="toggle_display stat_tab">
					<li class="selected"><a href="#stat_tab_config"><h2>Config</h2></a></li>
					<li><a href="#stat_tab_exp_list"><h2>Expiry List</h2></a></li>
					<li><a href="#stat_tab_prune"><h2>Prune</h2></a></li>
					<li><a href="#stat_tab_ex"><h2>Examples</h2></a></li>
				</ul>
			</div>

			<div id="stat_tab_config" class="tab">

				<form method="post">
					<br>
					<h3>Default Expiry Type</h3>

					<div style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">
						<select name="expiry_global_expiry" size="1" >
							<option value="click" {$globalExp['click']}>Click Counter</option>
							<option value="clock" {$globalExp['clock']}>Timer</option>
							<option value="none" {$globalExp['none']}>None</option>
						</select>
						<p>Set this if you want a global expiration condition for ALL new links.</p>
						<p>For example, you can make it so that every new link will expire in 3 days unless otherwise specified. Leave to 'none' for standard YOURLS behavior.</p>
					</div>
					<br>
					<h3>Default Click Counter Value</h3>
					
					<div style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">
						<input type="hidden" name="expiry_default_click" value="50">
	  					<input type="number" name="expiry_default_click" min="1" max="99999" value=$opt[8]><br>

						<p>If the expiry type is set to 'click' and no 'count' value is set, expiry falls back to this value.</p>
					</div>
					<br>
					<h3>Default Countdown Time Span</h3>

					<div style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">
						<input type="hidden" name="expiry_default_age" value="50">
	  					<input type="number" name="expiry_default_age" min="1" max="100" value=$opt[6]>
						<select name="expiry_default_age_mod" >
							<option value="min" {$ageMod['min']}>Minute(s)</option>
							<option value="hour" {$ageMod['day']}>Hour(s)</option>
							<option value="day" {$ageMod['hour']}>Day(s)</option>
							<option value="week" {$ageMod['week']}>Week(s)</option>
						</select>
						<p>If the expiry type is set to 'clock' with no other conditions set, expiry falls back to this value.</p>
					</div>
					<br>
					<h3>Expiry Intercept Behavior</h3>
					
					<div style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">
						<select name="expiry_global_expiry" size="1" >
							<option value="simple" {$intercept['simple']}>YOURLS style</option>
							<option value="template" {$intercept['template']}>Bootstrap Template</option>
							<option value="custome" {$intercept['custome']}>Custome URL</option>
						</select>
						<p>The click that causes a link to expire must be handled, we intercept it. You can choose how to do that here.</p>
					</div>

					<div style="display:$ciVisChk;">
						<br>
						<h3>Custome Intercept Page</h3>

						<div style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">

							<p><label for="expiry_custom">Enter intercept URL here</label> <input type="text" size=40 id="expiry_custom" name="expiry_custom" value="$opt[1]" /></p>
							<p>Setting the above option without setting this will fall back to default behavior.</p>

						</div>
					</div>
					<br>
					<h3>Global Post-Expiry Option</h3>

					<div class="checkbox" style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">
						<label>
							<input name="expiry_global_post_expire_chk" type="hidden" value="false" />
							<input name="expiry_global_post_expire_chk" type="checkbox" value="true" $gpxChk $unique
						</label>
						<p>Instead of being deleted form the database, expiry short links can be edited to a new url when they expire. You can set a global value for this here.</p>

						<p>An alternative way to acheive this might be to use the <a href="https://diegopeinador.blogspot.com/2013/04/fallback-url-simple-plugin-for-yourls.html" target="_blank">Fallbak-URL</a> plugin. Combined, there is a lot of flexibility.</p>
					</div>

					<div style="display:$gpxVisChk;">
						<br>
						<h3>Global Post Expiry Destination URL</h3>

						<div style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">

							<p><label for="expiry_custom">Enter intercept URL here</label> <input type="text" size=40 id="expiry_custom" name="expiry_custom" value="$opt[1]" /></p>
							<p>Setting the above option without setting this will fall back to default behavior.</p>

						</div>
					</div>
					
					<br>
					<h3>Expose Expiry Tags</h3>

					<div class="checkbox" style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">
						<label>
							<input name="expiry_expose" type="hidden" value="false" />
							<input name="expiry_expose" type="checkbox" value="true" $expChk > Expose? 
						</label>
						<p>If enabled, any links with an expiry set will be marked with an unobtrusive green highlight in the admin interface.</p>
					</div>

					<br>
					<h3>Expiry Table Handling</h3>

					<div class="checkbox" style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">
						<label>
							<input name="expiry_table_drop" type="hidden" value="false" />
							<input name="expiry_table_drop" type="checkbox" value="true" $tblChk > Drop it? 
						</label>
						<p>If selected, the expiry data will be flushed if the plugin gets disabled. Leave unchecked to preserve this data.</p>
					</div>

					<input type="hidden" name="nonce" value="$nonce" />
					<p><input type="submit" value="Submit" /></p>
				</form>
			</div>


			<div  id="stat_tab_exp_list" class="tab">

	 			<h3>Expiry URL List</h3>
				<h4>You can add an Expiry condition to a link that is already in the database</h4>

HTML;
	expiry_list_mgr($nonce);
echo <<<HTML
			</div>
			<div  id="stat_tab_prune" class="tab">

	 			<h3>Advanced: Database  Maintenance </h3>

				<p>There are 3 settings for this operation</p>
				<ul>
					<li><strong>Expired</strong>: Locates all links that are beyond expiration conditions, processes them accordingly.</li>
					<li><strong>Expires</strong>: Dumps all expiry data. URL's remain in the database, only expiry data is stripped.</li>
					<li><strong>Expiring</strong>: <span style="color:red">Warning!</span> Removes every url from YOURLS in the expiry table.</li>
				</ul>
				<p>Each of these items, as well as every Expiry function, has an api access point. More on how (and why) to use them on the next tab.</p>
				<form method="post">

					<div style="padding-left: 10pt;border-left:1px solid blue;border-bottom:1px solid blue;">
						<select name="expiry_admin_prune_type" size="1" >
							<option value="lazyorblind" >Select One</option>
							<option value="expired" >Expired</option>
							<option value="expires" >Expires</option>
							<option value="expiring">Expiring</option>
						</select>

						<label>
							<input name="expiry_admin_prune_do" type="hidden" value="false" />
							<input name="expiry_admin_prune_do" type="checkbox" value="true" > Do it? 
							<p>Be sure that you have the correct option, and check the box.</p>
						</label>
					</div>
	
					<input type="hidden" name="nonce" value="$nonce" />
					<p><input type="submit" value="Submit" /></p>

				<form method="post">
			</div>

			<div  id="stat_tab_ex" class="tab">
				<h3>Coming Soon</h3>
				<p>Watch this plugin's <a href="https://github.com/joshp23/YOURLS-expiry" target="_blank" >Github Repo</a> for updates and examples.</p>
				<h4>API Documentaion</h4>
				<h4>Cron examples</h4>
				<h4>Index.php Integration example</h4>
			</div>
		</div>
	</div>
			
HTML;
		
}
// Display page 0.1 - the expiry list
function expiry_list($nonce) {
	global $ydb;
echo <<<HTML
	<p>Not all values are required. For Expiry type: clicks, only count is required.</p>
	<p>For Expiry type: clock, time and time unit are required. That is all.</p>
	
	<form method="post">
		<table id="main_table" class="tblSorter" border="1" cellpadding="5" style="border-collapse: collapse">
			<thead>
				<tr>
					<th>Alias</th>
					<th>Expiry Type</th>
					<th>Clicks</th>
					<th>Timer</th>
					<th>Time Unit</th>
					<th>PostX Destiantion</th>

					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="text" name="shorturl" size=8></td>
					<td>
						<select name="expiry" size="1" >
							<option value="none">Select One</option>
							<option value="click">Click Counts</option>
							<option value="clock">Timer</option>
						</select>
					</td>
					<td><input type="text" size="5" name="count" value=""></td>
					<td><input type="text" size="3" name="age" value=""></td>
					<td>
						<select name="mod" size="1" >
							<option value="">Select One</option>
							<option value="min">Minutes</option>
							<option value="hour">Hours</option>
							<option value="day" >Days</option>
							<option value="week">Weeks</option>
						</select>
					</td>
					<td><input type="text" name="postx" size=30 ></td>
					<td colspan=3 align=right>
						<input type=submit name="submit" value="Submit: Expiry an Old Link">
						<input type="hidden" name="nonce" value="$nonce" />
					</td>
				</tr>
HTML;
	// populate table rows with expiry data if there is any

	$table = 'expiry';
	$sql = "SELECT * FROM $table ORDER BY timestamp DESC";
	$binds = array('keyword' => $keyword);
	$expiry_list = $ydb->fetchAll($sql, $binds);

	if($expiry_list) {
		foreach( $expiry_list as $expiry ) {
			$kword  = $expiry->keyword;
			$type   = $expiry->type;
			$click  = $expiry->click;
			$fresh  = $expiry->timestamp;
			$stale  = $expiry->shelflife;
			$postx  = $expiry->postexpire;
			$death  = ((time() - $fresh) - $stale);
			$remove = ''. $_SERVER['PHP_SELF'] .'?page=expiry&action=remove&key='. $kword .'';
			$strip  = ''. $_SERVER['PHP_SELF'] .'?page=expiry&action=no_postx&key='. $kword .'';
			// print if there is any data
			echo <<<HTML
				<tr>
					<td>$kword</td>
					<td>$type</td>
					<td>$click</td>
					<td>$death</td>
					<td>Seconds</td>
					<td>$postx</td>
					<td><a href="$remove">Remove Expiry <img src="/images/delete.png" title="UnExpire" border=0></a></td>
					<td><a href="$strip">Strip Postx <img src="/images/delete.png" title="UnPostExpiry" border=0></a></td>
				</tr>
HTML;
		}
	}
echo <<<HTML
			</tbody>
		</table>
	</form>
HTML;
}
/*
// Admin Page JS FIXME
yourls_add_action('html_addnew', 'expiry_script');
function expiry_script(){
//	echo "<script src=\"". yourls_plugin_url( dirname( __FILE__ ) ). "/assets/expiry.js\" type=\"text/javascript\"></script>" ;
//	echo '<link rel="stylesheet" href="/css/infos.css" type="text/css" media="screen" />';
	echo '<script src="/js/infos.js" type="text/javascript"></script>';
}
*/
// Mark expiry links on admin page
yourls_add_filter( 'table_add_row', 'show_expiry_tablerow' );
function show_expiry_tablerow($row, $keyword, $url, $title, $ip, $clicks, $timestamp) {
	
	global $ydb;
	
	// Check if this is wanted
	$expiry_expose = yourls_get_option( 'expiry_expose' ); 
	if($expiry_expose !== "false") {

		// If the keyword is set to expire, make the URL show in green;

		$table = 'expiry';
		$sql = "SELECT * FROM $table WHERE BINARY `keyword` = :keyword";
		$binds = array('keyword' => $keyword);
		$expiry = $ydb->fetchOne($sql, $binds);
	
		if( $expiry ) {
			$old_key = '/td class="keyword"/';
			$new_key = 'td class="keyword" style="border-right: 6px solid green;"';
			$newrow = preg_replace($old_key, $new_key, $row);
			return $newrow;
		} else {
		$newrow = $row;
		}
		return $newrow;
	} else {
	return $row;
	}
}/*
 *
 * 	Form submissions
 *
 *
*/
// Options updater
function expiry_update_ops() {
	if(isset( $_POST['expiry_global_expiry'])) {
		// Check nonce
		yourls_verify_nonce( 'expiry' );

		yourls_update_option( 'expiry_global_expiry', $_POST['expiry_global_expiry'] );
		if(isset( $_POST['expiry_default_click'] )) yourls_update_option( 'expiry_default_click', $_POST['expiry_default_click'] );
		if(isset( $_POST['expiry_default_age'] )) yourls_update_option( 'expiry_default_age', $_POST['expiry_default_age'] );
		if(isset( $_POST['expiry_default_age_mod'] )) yourls_update_option( 'expiry_default_age_mod', $_POST['expiry_default_age_mod'] );
		if(isset( $_POST['expiry_intercept'] )) yourls_update_option( 'expiry_intercept', $_POST['expiry_intercept'] );
		if(isset( $_POST['expiry_custom'] )) yourls_update_option( 'expiry_custom', $_POST['expiry_custom'] );
		if(isset( $_POST['expiry_global_post_expire_chk'] )) yourls_update_option( 'expiry_global_post_expire_chk', $_POST['expiry_global_post_expire_chk'] );
		if(isset( $_POST['expiry_global_post_expire'] )) yourls_update_option( 'expiry_global_post_expire', $_POST['expiry_global_post_expire'] );
		if(isset( $_POST['expiry_expose'] )) yourls_update_option( 'expiry_expose', $_POST['expiry_expose'] );
		if(isset( $_POST['expiry_table_drop'] )) yourls_update_option( 'expiry_table_drop', $_POST['expiry_table_drop'] );

	}
}

// Epirly List Mgr
function expiry_list_mgr($n) {

	// CHECK if UNSET form was submitted, handle expiry list
	if( isset( $_GET['action'] ) ) {
		global $ydb;
		// remove expiry data from a link (keeps link in YOURLS)
		if( $_GET['action'] == 'remove') {
			if( isset($_GET['key']) ) {
				$key = $_GET['key'];
        			$delete = $ydb->query("DELETE FROM `expiry` WHERE `keyword` = '$key'");
			}
			// go to list
			expiry_list($n);
		}
		// remove fallback data from expiry (keeps expiration data)
		if( $_GET['action'] == 'no_postx') {
			if( isset($_GET['key']) ) {
				$key = $_GET['key'];
        			$update = $ydb->query("UPDATE `expiry` SET `postexpire` = 'none' WHERE `keyword` = '$key'");
			}
			// go to list
			expiry_list($n);
		}
	}
	elseif( !empty($_POST) && isset( $_POST['shorturl'] ) && isset( $_POST['expiry'] ) ) {
		expiry_old_link();
		expiry_list($n);
	}
	else {
		expiry_list($n);
	}
}
// Prune Form
function expiry_flush() {
	if( isset( $_POST['expiry_admin_prune_do'] ) ) {
		if( $_POST['expiry_admin_prune_do'] == 'true' ) {
			// Check nonce
			yourls_verify_nonce( 'expiry' );
			$type = $_POST['expiry_admin_prune_type'];
			expiry_db_flush( $type );
			switch ($type) {
				case 'expired':
					echo '<font color="green">All expired links have beeen deleted from the system. Have a nice day.</font>';
					break;
				case 'expires':
					echo '<font color="green">All links are now non-perishable. Have a nice day.</font>';
					break;
				case 'expiring':
					echo '<font color="green">All links with expiration dates have been deleted. Have a ncie day.</font>';
					break;
				case 'lazyorblind';
					echo '<font color="red">You submitted the form without selecting an option... try again.</font>';
					break;
				default:
					echo '<font color="red">Something went wrong, check your database.</font>';
			}
		} else {
			echo '<font color="red">You submitted the Prune without checking "Do it?"</font>';
		}
	}
}
/*
 *
 * Expiry Checking
 *
 *
*/
// Hook on basic redirect
yourls_add_action( 'redirect_shorturl', 'expiry_check' );
// expiry check
function expiry_check( $args ) {

	global $ydb;

    $keyword = $args[1]; // Keyword for this request
	$table = 'expiry';
	$sql = "SELECT * FROM $table WHERE `keyword` = :keyword";
	$binds = array('keyword' => $keyword);
	$expiry = $ydb->fetchOne($sql, $binds);
	
	if( $expiry ) {
	
		$result = false;

		$expiry = (array)$expiry;
		if( $expiry['type'] == 'click' ) {

			$count 	= $expiry['click'];

			$stats  = yourls_get_link_stats( $keyword );
			$link = $stats['link'];
			$clicks = $link['clicks'];
			
			if ( $clicks >= $count ) {
				$result = 'click-bomb';
			} else {
				$result = false;
			}
		}

		elseif( $expiry['type'] == 'clock' ) {

			$fresh  = $expiry['timestamp'];
			$stale  = $expiry['shelflife'];

			if( ( time() - $fresh)  >= $stale ) {
				$result = 'time-bomb';	
			} else {
				$result = false;
			}
		}
		
		if($result !== false) {

			$opt = expiry_config();

			$gpx = $opt[5] == 'false' ? null : $opt[4];
			$postx  = (isset($expiry['postexpire']) ? $expiry['postexpire'] : $gpx);
			expiry_router($keyword, $result, $postx);
		}
	}
}
// expiry check ~ router
function expiry_router($keyword, $result, $postx) {
	// try to edit maybe
	if ( $postx !== null && $postx !=='' && $postx !== 'none') {
	
		$switch = yourls_edit_link( $postx, $keyword );

		if( $switch['status'] == 'success' ) {

			expiry_cleanup( $keyword, $result );

			if ( !yourls_is_api() ) {
				yourls_redirect_($base . '/' . $keyword, 302);
				die();
			}
		} else {
	
			$postx = null;
		}
	} 

	elseif( $postx == null || $postx == '' || $postx == 'none' ) {
	
		yourls_delete_link_by_keyword( $keyword );
		
		if ( !yourls_is_api() ) {
		
			$expiry_intercept = yourls_get_option( 'expiry_intercept' );
			
			switch ($expiry_intercept) {
				case 'simple': 
					yourls_die('This short URL has expired.', 'Link Expired', '403');
				case 'template': 
					expiry_display_expired($keyword, $result);
				case 'custom':
					$expiry_custom = yourls_get_option( 'expiry_custom' );
					if ($expiry_custom !== 'none') { 
						yourls_redirect( $expiry_custom, 302 );
						die();
					} else {
						yourls_die('This short URL has expired.', 'Link Expired', '403');
					}
				default:
					yourls_die('This short URL has expired.', 'Link Expired', '403');
			}
		}
	}
	
}

/*
 *
 *	Helpers
 *
 *
*/
// Get options and set defaults
function expiry_config() {

	// Get values from DB
	$intercept = yourls_get_option( 'expiry_intercept' );
	$int_cust  = yourls_get_option( 'expiry_custom' );
	$tbl_drop  = yourls_get_option( 'expiry_table_drop' );
	$expose	   = yourls_get_option( 'expiry_expose' );
	$gpx	   = yourls_get_option( 'expiry_global_post_expire' );
	$gpx_chk   = yourls_get_option( 'expiry_global_post_expire_chk' );
	$age	   = yourls_get_option( 'expiry_default_age' ); 
	$mod	   = yourls_get_option( 'expiry_default_age_mod' );
	$click	   = yourls_get_option( 'expiry_default_click' );
	$global	   = yourls_get_option( 'expiry_global_expiry' );
	
	// Set defaults if necessary
	if( $intercept	== null ) $intercept 	= 'simple';
	if( $int_cust	== null ) $int_cust	= 'none';
	if( $tbl_drop 	== null ) $tbl_drop 	= 'false';
	if( $expose	== null ) $expose	= 'true';
//	if( $gpx	== null ) $gfb		= 'none';
	if( $gpx_chk	== null ) $gpx_chk	= 'false';
	if( $age	== null ) $age		= '3';
	if( $mod	== null ) $mod		= 'day';
	if( $click	== null ) $click	= '50';
	if( $global	== null ) $global	= 'none';

	if( 'YOURLS_UNIQUE_URLS' == true) $gpx_chk = 'false';

	return array(
	$intercept,		// opt[0]
	$int_cust,		// opt[1]
	$tbl_drop,		// opt[2]
	$expose,		// opt[3]
	$gpx,			// opt[4]
	$gpx_chk,		// opt[5]
	$age,			// opt[6]
	$mod,			// opt[7]
	$click,			// opt[8]
	$global			// opt[9]
	);
}

// Adjust human readable time into seconds
function expiry_age_mod($age, $mod) {
	switch ($mod) {
		case 'week': 
			$age = $age * 7 * 24 * 60 * 60;
			break;
		case 'day':
			$age = $age * 24 * 60 * 60;
			break;
		case 'hour':
			$age = $age * 60 * 60;
			break;
		case 'min':
			$age = $age * 60;
			break;
		default:
			$age = $age;
	}
	return $age;
}

// intercept template
function expiry_display_expired($keyword, $result) {

	$base	= YOURLS_SITE;
	$img	= yourls_plugin_url( dirname( __FILE__ ).'/assets/caution.png' );
	$css 	= yourls_plugin_url( dirname( __FILE__ ).'/assets/bootstrap.min.css' );

	$vars = array();
		$vars['keyword'] 	= $keyword;
		$vars['result'] 	= $result;	//TODO - put in intercept.php
		$vars['base'] 		= $base;
		$vars['img'] 		= $img;
		$vars['css'] 		= $css;

	$intercept = file_get_contents( dirname( __FILE__ ) . '/assets/intercept.php' );
	// Replace all %stuff% in intercept.php with variable $stuff
	$intercept = preg_replace_callback( '/%([^%]+)?%/', function( $match ) use( $vars ) { return $vars[ $match[1] ]; }, $intercept );

	echo $intercept;
	die();
}

/*
 *
 *	API
 *
 *
*/
// Expire new links
yourls_add_filter( 'add_new_link', 'expiry_new_link' );
function expiry_new_link( $return, $url , $keyword, $title ) { 

	$opt = expiry_config();

	$type = isset($_REQUEST['expiry']) ? $_REQUEST['expiry'] : $opt[9];

	switch( $type ) {
		case 'click': 									// ex. "expiry=click"
			$click = (isset($_REQUEST['count']) ? $_REQUEST['count'] : $opt[8]);	// ex. "count=50"
			if( !is_numeric( $click ) ){	
				$return['expiry'] = "'count' must be a valid number, no expiry set";
				return $return;
			}

			$fresh = $stale = 'dummy';	
			$return['expiry'] = "$click click expiry set";
			break;
		case 'clock':									// ex. "expiry=clock"
			$age = (isset($_REQUEST['age']) ? $_REQUEST['age'] : $opt[6]); 		// ex. "age=3"
			if( !is_numeric( $age ) ) {
				$return['expiry'] = "'age' must be a valid number, no expiry set";
				return $return;
			}

			$mod = (isset($_REQUEST['mod']) ? $_REQUEST['mod'] : $opt[7]); 		// ex. "mod=hour"
			if( !in_array( $mod, array( 'min', 'hour', 'day', 'week' ) ) ) {
				$return['expiry'] = "'mod' must be 'min', 'day', 'hour', or 'week', no expiry set";
				return $return;
			}

			$fresh = time();
			$stale = expiry_age_mod($age, $mod);
			$click = 'dummy';
			$return['expiry'] = "$age $mod expiry set.";
			break;
		default:
			return $return;
		
	}

	$gpx    = $opt[5] == 'false' ? null : $opt[4];
	$postx  = (isset($_REQUEST['postx']) ? $_REQUEST['postx'] : $gpx); 			// ex. "postx=https://example.com"
	if($postx !== null && $postx !== 'none') {
		$return['postx'] = $postx;
		if (!filter_var($postx, FILTER_VALIDATE_URL) ) {
			$return['postx'] = "invalid url, not set";
			$postx = 'none';
		}
		elseif(!yourls_is_allowed_protocol( $postx ) ){
			$return['postx'] = "disallowed protocol, not set";
			$postx = 'none';
		}
	}

	// All set, put it in the database
	global $ydb;
	$insert = $ydb->query("REPLACE INTO `expiry` (keyword, type, click, timestamp, shelflife, postexpire) VALUES ('$keyword', '$type', '$click', '$fresh', '$stale', '$postx')");

	return yourls_apply_filter( 'after_expiry_new_link', $return, $url, $keyword, $title );
}

// Expiry old links
yourls_add_filter( 'api_action_expiry', 'expiry_old_link' );
function expiry_old_link() {
	
	$auth = yourls_is_valid_user();
	if( $auth !== true ) {
		$format = ( isset($_REQUEST['format']) ? $_REQUEST['format'] : 'xml' );
		$callback = ( isset($_REQUEST['callback']) ? $_REQUEST['callback'] : '' );
		yourls_api_output( $format, array(
			'simple' => $auth,
			'message' => $auth,
			'errorCode' => 403,
			'callback' => $callback,
		) );
	}

	if( !isset( $_REQUEST['shorturl'] ) ) {
		return array(
			'statusCode' => 400,
			'simple'     => "Need a 'shorturl' parameter",
			'message'    => 'error: missing shorturl param',
		);	
	}

	$shorturl = $_REQUEST['shorturl'];

	if( !yourls_is_shorturl( $shorturl ) ) {
		return array(
			'statusCode' => 400,
			'simple'     => "Not a valid short url",
			'message'    => 'error: bad url',
		);	
	}

	$keyword = str_replace( YOURLS_SITE . '/' , '', $shorturl ); // accept either 'http://ozh.in/abc' or 'abc'

	$keyword = yourls_sanitize_string( $keyword );
	$url = yourls_get_keyword_longurl( $keyword );
	$title = yourls_get_keyword_title( $keyword );

	$opt = expiry_config();

	$type = isset($_REQUEST['expiry']) ? $_REQUEST['expiry'] : $opt[9];

	switch( $type ) {

		case 'click': 									// ex. "expiry=click"
			$click = (isset($_REQUEST['count']) ? $_REQUEST['count'] : $opt[8]);	// ex. "count=50"
			if( !is_numeric( $click ) ){
				return array(
					'statusCode' => 400,
					'simple'     => "'count' must be a valid number, no expiry set",
					'message'    => "error: 'count' must be a valid number",
				);		
			}

			$fresh = $stale = null;	
			$return['expiry'] = "$click click expiry set";
			$return['expiry_type'] = "click";
			$return['expiry_life'] = "$click";
			break;

		case 'clock':									// ex. "expiry=clock"
			$age = (isset($_REQUEST['age']) ? $_REQUEST['age'] : $opt[6]); 		// ex. "age=3"
			if( !is_numeric( $age ) ) {
				return array(
					'statusCode' => 400,
					'simple'     => "'age' must be a valid number, no expiry set",
					'message'    => "error: 'age' must be a valid number",
				);		
			}

			$mod = (isset($_REQUEST['mod']) ? $_REQUEST['mod'] : $opt[7]); 		// ex. "mod=hour"
			if( !in_array( $mod, array( 'min', 'hour', 'day', 'week' ) ) ) {
				return array(
					'statusCode' => 400,
					'simple'     => "'mod' must be set to 'min', 'day', 'hour', or 'week', no expiry set",
					'message'    => "error: 'mod' must be set to 'min', 'day', 'hour', or 'week'",
				);		
			}

			$fresh = time();
			$stale = expiry_age_mod($age, $mod);
			$click = null;
			$return['expiry'] = "$age $mod expiry set.";
			$return['expiry_type'] = "clock";
			$return['expiry_life'] = "$stale"; // in seconds
			break;

		case 'none':
			return array(
				'statusCode' => 400,
				'simple'     => "'expiry' must be set to 'click' or 'clock', no expiry set",
				'message'    => "error: 'expiry' must be set to 'click' or 'clock'",
			);		
	}

	$gpx    = $opt[5] == 'false' ? null : $opt[4];
	$postx  = (isset($_REQUEST['postx']) ? $_REQUEST['postx'] : $gpx); 			// ex. "postx=https://example.com"
	if($postx !== null && $postx !== 'none') {
		$return['postx'] = $postx;
		if (!filter_var($postx, FILTER_VALIDATE_URL) ) {
			$return['postx'] = "error: invalid url, not set";
			$postx = null;
		}
		elseif(!yourls_is_allowed_protocol( $postx ) ){
			$return['postx'] = "error: disallowed protocol, not set";
			$postx = null;
		}
	}
	$shorturl = YOURLS_SITE . '/' . $keyword;
	$return['statusCode'] = "200";
	$return['message'] = "success: expiry set";
	$return['shorturl'] = $shorturl;
	$return['url'] = $url;
	$return['title'] = $title;
	$return['simple'] = "Success: '$type' expiry set for $shorturl ";
	
	// All set, put it in the database
	global $ydb;
	$insert = $ydb->query("REPLACE INTO `expiry` (keyword, type, click, timestamp, shelflife, postexpire) VALUES ('$keyword', '$type', '$click', '$fresh', '$stale', '$postx')");
	return yourls_apply_filter( 'after_expiry_old_link', $return, $url, $keyword, $title );
}

// Prune away expired links
yourls_add_filter( 'api_action_prune', 'expiry_prune_api' );
function expiry_prune_api() {

	$auth = yourls_is_valid_user();
	if( $auth !== true ) {
		$format = ( isset($_REQUEST['format']) ? $_REQUEST['format'] : 'xml' );
		$callback = ( isset($_REQUEST['callback']) ? $_REQUEST['callback'] : '' );
		yourls_api_output( $format, array(
			'simple' => $auth,
			'message' => $auth,
			'errorCode' => 403,
			'callback' => $callback,
		) );
	}

	// We need a scope for the prune
	if( !isset( $_REQUEST['scope'] ) ) {
		return array(
			'statusCode' => 400,
			'simple'     => "Need a 'scope' parameter",
			'message'    => "error: missing 'scope' param",
		);	
	}
	
	// Scope must be in range
	if( !in_array( $_REQUEST['scope'], array( 'expired', 'expires', 'expiring' ) ) ) {
		return array(
			'statusCode' => 400,
			'simple'     => "Error: 'scope' must be set to 'expired', 'expires' or 'expiring'",
			'message'    => "error: bad param value for 'scope'",
			);
	}

	$type = $_REQUEST['scope'];
	switch( $type ) {
		case 'expired':
			
			if( expiry_db_flush( $type ) ) {
				return array(
					'statusCode' => 200,
					'simple'     => "Expired links have been pruned",
					'message'    => 'success: pruned',
				);
			} else {
				return array(
					'statusCode' => 500,
					'simple'     => 'Error: could not prune expiry, not sure why :-/',
					'message'    => 'error: unknown error',
				);
			}

		case 'expires':
			
			if( expiry_db_flush( $type ) ) {
				return array(
					'statusCode' => 200,
					'simple'     => "Expirations have been stripped from all links",
					'message'    => 'success: pruned',
				);
			} else {
				return array(
					'statusCode' => 500,
					'simple'     => 'Error: could not prune expiry, not sure why :-/',
					'message'    => 'error: unknown error',
				);
			}

		case 'expiring':
			
			if( expiry_db_flush( $type ) ) {
				return array(
					'statusCode' => 200,
					'simple'     => "All perishable links have been pruned",
					'message'    => 'success: pruned',
				);
			} else {
				return array(
					'statusCode' => 500,
					'simple'     => 'Error: could not prune expiry, not sure why :-/',
					'message'    => 'error: unknown error',
				);
			}
	}
}
/*
 *
 *	Database
 *
 *
*/
// Create tables for this plugin when activated
yourls_add_action( 'activated_expiry/plugin.php', 'expiry_activated' );
function expiry_activated() {

	global $ydb;

	$init = yourls_get_option('expiry_init');
	if ($init === false) {
		// Create the init value
		yourls_add_option('expiry_init', time());
		// Create the expiry table
		$table_expiry  = "CREATE TABLE IF NOT EXISTS expiry (";
		$table_expiry .= "keyword varchar(200) NOT NULL, ";
		$table_expiry .= "type varchar(5) NOT NULL, ";
		$table_expiry .= "click varchar(5), ";
		$table_expiry .= "timestamp varchar(20), ";
		$table_expiry .= "shelflife varchar(20), ";
		$table_expiry .= "postexpire varchar(200), ";
		$table_expiry .= "PRIMARY KEY (keyword) ";
		$table_expiry .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1;";
		$tables = $ydb->query($table_expiry);

		yourls_update_option('expiry_init', time());
		$init = yourls_get_option('expiry_init');
		if ($init === false) {
			die("Unable to properly enable expiry due an apparent problem with the database.");
		}
	}
}

// Delete table when plugin is deactivated
yourls_add_action('deactivated_expiry/plugin.php', 'expiry_deactivate');
function expiry_deactivate() {
	$expiry_table_drop = yourls_get_option('expiry_table_drop');
	if ( $expiry_table_drop !== 'false' ) {
		global $ydb;
	
		$init = yourls_get_option('expiry_init');
		if ($init !== false) {
			yourls_delete_option('expiry_init');
			$ydb->query("DROP TABLE IF EXISTS expiry");
		}
	}
}

// DB Flushing
function expiry_db_flush( $type ) {
	global $ydb;

	switch ( $type ) {
		// get rid of expired links that have not been triggered
		case 'expired':
			$expiry_list = $ydb->get_results("SELECT * FROM `expiry` ORDER BY timestamp DESC");
			
			if($expiry_list) {
				foreach( $expiry_list as $expiry ) {		
					$keyword = $expiry->keyword;
					expiry_check('prune', $keyword);
				}
			}

			$result = true;
			break;
			
		// remove expiry data from all links & preserve the short url	
		case 'expires':
			$init_1 = yourls_get_option('expiry_init');

			if ($init_1 !== false) {
				$ydb->query("TRUNCATE TABLE `expiry`");
				yourls_update_option('expiry_init', time());
				$init_2 = yourls_get_option('expiry_init');
				if ($init_2 === false || $init_1 == $init_2) {
					die("Unable to properly reset the database. Contact your sys admin");
				}
			}

			$result = true;
			break;
			
		// delete every short url that is set to expire	
		case 'expiring': // nuke
		
			$expiry_list = $ydb->get_results("SELECT * FROM `expiry` ORDER BY timestamp DESC");
			
			if($expiry_list) {
				foreach( $expiry_list as $expiry ) {
					$keyword = $expiry->keyword;
					yourls_delete_link_by_keyword( $keyword );
				}
			}

			$result = true;	
			break;
		default: 	// expired
			$expiry_list = $ydb->get_results("SELECT * FROM `expiry` ORDER BY timestamp DESC");
			
			if($expiry_list) {
				foreach( $expiry_list as $expiry ) {		
					$keyword = $expiry->keyword;
					expiry_check('prune', $keyword);
				}
			}

			$result = true;
			break;
	}

	return $result;
}
// auto-delete expiry records 
yourls_add_action( 'delete_link', 'expiry_cleanup' );	// cleanup on keyword deletion
function expiry_cleanup( $args ) {
	global $ydb;

    	$keyword = $args[0]; // Keyword to delete

	// Delete the expiry data, no need for it anymore
	$ydb->query("DELETE FROM `expiry` WHERE `keyword` = '$keyword';");

}

// Change-Error MSG behavior
if((yourls_is_active_plugin('change-error-messages/plugin.php')) == false) {

	yourls_add_filter( 'add_new_link', 'change_error_messages' );
	// If the keyword exists, display the long URL in the error message
	function change_error_messages( $return, $url, $keyword, $title  ) {
		if ( isset( $return['code'] ) ) {
			if ( $return['code'] === 'error:keyword' ){
				$long_url = yourls_get_keyword_longurl( $keyword );
				if ($long_url){
					$return['message']	= 'The keyword "' . $keyword . '" already exists for: ' . $long_url;
				} elseif ( yourls_keyword_is_reserved( $keyword ) ){
								$return['message']	= "The keyword '" . $keyword . "' is reserved";
				}
			}
			elseif ( $return['code'] === 'error:url' ){
				if ($url_exists = yourls_url_exists( $url )){
					$keyword = $url_exists->keyword;
					$return['status']   = 'success';
					$return['message']	= 'This URL already has a short link: ' . YOURLS_SITE .'/'. $keyword;
					$return['title']    = $url_exists->title;
					$return['shorturl'] = YOURLS_SITE .'/'. $keyword;
				}
			}
		}
		return yourls_apply_filter( 'after_custom_error_message', $return, $url, $keyword, $title );
	}
}
