<?php
/*
Plugin Name: Viadeo Resume
Plugin URI: http://www.magetys.com
Description: Show your resume or your contacts' resumes generated with professional social network Viadeo profiles (http://www.viadeo.com)
Author: Magetys
Version: 1.0.4
Author URI: http://www.magetys.com
*/

// == ADMIN INSTALL DB ==========================================================================

define( 'VIADEO_RESUME_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VIADEO_RESUME_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

function viadeo_resume_install() {

	global $wpdb;

	$table = $wpdb->prefix."viadeo_resume";

	$structure = "CREATE TABLE $table (name VARCHAR(50) NOT NULL PRIMARY KEY, value VARCHAR(50));";
	
	$insert = "INSERT INTO $table VALUES('access_token', '')";

	$wpdb->query($structure);
	$wpdb->query($insert);
}

add_action('activate_viadeo-resume/viadeo_resume.php', 'viadeo_resume_install');

function viadeo_resume_uninstall() {

	global $wpdb;

	$table = $wpdb->prefix."viadeo_resume";
	$structure = "DROP TABLE $table;";
	$wpdb->query($structure);
}

add_action('deactivate_viadeo-resume/viadeo_resume.php', 'viadeo_resume_uninstall');

// == ADMIN OPTIONS PAGE ========================================================================

include('viadeoapi.inc');

$VD = new ViadeoAPI();
$VD->setCurlOption(CURLOPT_SSL_VERIFYPEER, FALSE);

if(isset($_GET['page'])) {
	$VD->setRedirectURI(ViadeoHelper::getCurrentURL() . "?page=" . $_GET['page']);
}


function viadeo_resume_deconnect() {

	global $wpdb;
	$structure = "UPDATE " . $wpdb->prefix . "viadeo_resume SET value='' WHERE name='access_token'";
	$wpdb->query($structure);

}

function viadeo_resume_access_token() {

	global $wpdb;

	$results = $wpdb->get_results("SELECT value FROM ".$wpdb->prefix."viadeo_resume WHERE name='access_token'");
	if(count($results) == 1)
		return $results[0]->value;
	else
		return "";

}

function viadeo_resume_admin_connected() {

	?>
	<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2>Viadeo Resume</h2>
	<h3 class="title">Server configuration</h3>
	<p>
	<?php 
	$plugin_url = esc_url( VIADEO_RESUME_PLUGIN_URL );
	if(is_curl_installed()) {
	?>
	<img src="<?php echo $plugin_url . '/circle_green.png' ?>" alt="OK" width="16" height="16" />
	<?
	} else {
	?>
	<img src="<?php echo $plugin_url . '/circle_red.png' ?>" alt="NOK" width="16" height="16" />
	<?php
	}
	?>
	Running Curl <a href="http://wordpress.org/extend/plugins/viadeo-resume/faq/">(?)</a>
	</p>
	
	<?php

	$decoLink = ViadeoHelper::getCurrentURL() . "?page=" . $_GET['page'] . "&deconnection=true";
	$buttonText = "Disconnet from Viadeo";

	try {

		$VD = new ViadeoAPI(viadeo_resume_access_token());
		$VD->setCurlOption(CURLOPT_SSL_VERIFYPEER, FALSE);
		$me = $VD->get('/me')->execute(); 
		$buttonText = "Disconnet from Viadeo";
		?>
		<h3 class="title">Plugin configuration</h3>
		<p>You are connected to <a href="http://www.viadeo.com">Viadeo</a> with the account of <?php echo $me->name; ?>.</p>
		<p>Now you can insert your resume on every page or post with the short code <b>[viadeo-resume]</b></p>
		<p>You can also insert the resume of your <?php echo $me->contact_count; ?> Viadeo contacts with the short code <b>[viadeo-resume profile="<i>nickname</i>"]</b>.<br />Usually Viadeo nicknames are formatted like this : <i>firstname.lastname</i>.<br />When you write a post, you can generate the short code if you click on "add a Viadeo Resume" button.</p>


		<?php

	} catch (ViadeoAPIException $e) {
		$buttonText = "Set another Viadeo Access Token";
		?>
		<div id="notice" class="error below-h2"><p>Your Viadeo Access Token seems to be wrong. Please, set another one.</p></div>
		<?php
	} catch (ViadeoAuthenticationException $e) {
		$buttonText = "Set another Viadeo Access Token";
		?>
		<div id="notice" class="error below-h2"><p>Your Viadeo Access Token seems to be wrong. Please, set another one.</p></div>
		<?php
	}

?>
		<form method="post" action="<?php echo $decoLink; ?>">
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php echo $buttonText; ?>"></p>
		</form>
		</div>
<?php
}

function viadeo_resume_menu() {




	global $VD;
	global $wpdb;

	if(isset($_GET['deconnection'])) {
		viadeo_resume_deconnect();
	}

	if(viadeo_resume_access_token() == "") {

		if(isset($_POST['viadeo_access_token'])) {

			$wpdb->query("UPDATE " . $wpdb->prefix . "viadeo_resume SET value='" . $_POST['viadeo_access_token'] . "' WHERE name='access_token'");
			viadeo_resume_admin_connected();

			?>

			<?php

		} else {

			$postUrl = ViadeoHelper::getCurrentURL() . "?page=" . $_GET['page'];

			?>

			<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>Viadeo Resume</h2>
			<h3 class="title">Server configuration</h3>
			<p>
			<?php 
			$plugin_url = esc_url( VIADEO_RESUME_PLUGIN_URL );
			if(is_curl_installed()) {
			?>
			<img src="<?php echo $plugin_url . '/circle_green.png' ?>" alt="OK" width="16" height="16" />
			<?
			} else {
			?>
			<img src="<?php echo $plugin_url . '/circle_red.png' ?>" alt="NOK" width="16" height="16" />
			<?php
			}
			?>
			Running Curl <a href="http://wordpress.org/extend/plugins/viadeo-resume/faq/">(?)</a>
			</p>
			<h3 class="title">Plugin configuration</h3>
			<form method="post" action="<?php echo $postUrl; ?>">
			<p>Just set your personal Viadeo Access Token and click on the "Connect to Viadeo" button to start using Viadeo Resume Plugin.</p>
			<table class="form-table">
				<tbody>
				<tr valign="top">
				<th scope="row"><label for="viadeo_access_token">Viadeo Access Token *</label></th>
				<td><input name="viadeo_access_token" type="text" id="viadeo_access_token" class="regular-text"></td>
				</tr>
				</tbody>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Connect to Viadeo"></p>
			</form>
			<h3 class="title">* How to get your Viadeo Access Token</h3>
			<p>
			Connect to your Viadeo account on the web application at <a href="http://www.viadeo.com">http://www.viadeo.com</a><br /><br />

			Go to this page : <a href="http://dev.viadeo.com/documentation/authentication/request-an-api-key/">http://dev.viadeo.com/documentation/authentication/request-an-api-key/</a><br /><br />

			Complete all fields of the form to get your Access Token (informations that you set aren’t important for what we want)<br />
			 - Set an application name<br />
			 - Set an application description<br />
			 - Upload an application logo<br />
			 - Set an application URL<br />
			 - Accept the Viadeo API terms of usage<br />
			 - Save information<br /><br />

			Now, you can get your personal Access Token
			</p>
			</div>

			<?php
		}


	} else {

		viadeo_resume_admin_connected();
	}
}
 
function viadeo_resume_admin_actions() {
	add_options_page("Viadeo Resume", "Viadeo Resume", 1, "Viadeo-Resume", "viadeo_resume_menu");
}
 
add_action('admin_menu', 'viadeo_resume_admin_actions');

function is_curl_installed() {
	return in_array('curl', get_loaded_extensions());
}

// == ADMIN MESSAGE ===============================================================================

function viadeo_resume_media_button( ) {
	global $post_ID, $temp_ID;
	$iframe_post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);
	$title = esc_attr( __( 'Add a Viadeo Resume' ) );
	$plugin_url = esc_url( VIADEO_RESUME_PLUGIN_URL );
	$site_url = admin_url( "/admin-ajax.php?post_id=$iframe_post_id&amp;viadeo-resume=contact-list&amp;action=viadeo_resume_contact_list&amp;TB_iframe=true&amp;width=768" );

	echo '<a href="' . $site_url . '&id=add_form" class="thickbox" title="' . $title . '"><img src="' . $plugin_url . '/icon_viadeo.png" alt="' . $title . '" width="13" height="12" /></a>';
}

if ( !empty( $_GET['viadeo-resume'] ) && $_GET['viadeo-resume'] == 'contact-list' ) {
	add_action( 'parse_request', 'viadeo_resume_contact_list' );
	add_action( 'wp_ajax_viadeo_resume_contact_list', 'viadeo_resume_contact_list' );
}

add_action( 'media_buttons', 'viadeo_resume_media_button', 329 );

function viadeo_resume_contact_list( $wp ) {

?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Viadeo Resume helper</title>
	</head>

	<body id="media-upload" class="js folded">

		<h3 class="media-title">List of your Viadeo contacts</h3>
		<?php

		try {

			$VD = new ViadeoAPI(viadeo_resume_access_token());
			$VD->setCurlOption(CURLOPT_SSL_VERIFYPEER, FALSE);

			$me = $VD->get("/me")->execute();

			?>
			<p>Copy and paste the shortcode corresponding of one of your contact. To show your resume, just use the short code <b>[viadeo-resume]</b>.</p>
			<div>
			<?php

			$limit = 100;
			$req_friends = $me->connection('contacts')->user_detail('full')->limit($limit);
			$friends = $req_friends->execute(); 

			$nb = 1; $maxPages = $friends->count / $limit;

			do {  
				?>
				<?php
				foreach ($friends->data as $friend) {  
					?>
					<b><i><?php echo $friend->name ?> : </i></b> [viadeo-resume profile="<?php echo $friend->nickname ?>"]<br />
					<?php
				}  
			} while ((--$maxPages > 0) && (strlen($friends->paging->next) > 0) && ($friends = $req_friends->setURL($friends->paging->next)->x()));  
			?>

			</div>	

		<?php

		} catch (ViadeoAuthenticationException $e) {
			?>
			<div style="background-color:#FF0000;"><p style="color:#FFFFFF;"><b>You need to be connected to Viadeo on your admin control panel</b></p></div>
			<?php
		} catch (ViadeoAPIException $e) {
			?>
			<div style="background-color:#FF0000;"><p style="color:#FFFFFF;"><b>You need to be connected to Viadeo on your admin control panel</b></p></div>
			<?php
		}
		?>

	</body>
	</html>

<?php
	exit;
}



// == ADMIN ADD SHORTCODE ========================================================================

function viadeo_resume_shortcode( $atts, $content ) {
	extract( shortcode_atts( array(
		'profile' => 'me'
	), $atts ) );

	ob_start();
	viadeo_resume_show_profile($profile);
	$output_string = ob_get_contents();
	ob_end_clean();

	return $output_string;
}
add_shortcode( 'viadeo-resume', 'viadeo_resume_shortcode' );

// == SHOW PROFILE ===============================================================================²

function viadeo_resume_add_my_stylesheet() {
	wp_register_style( 'viadeo_resume-style', plugins_url('viadeo_resume.css', __FILE__) );
	wp_enqueue_style( 'viadeo_resume-style' );
}

add_action( 'wp_enqueue_scripts', 'viadeo_resume_add_my_stylesheet' );


function viadeo_resume_show_profile($memberId) {

	try {

		$VD = new ViadeoAPI(viadeo_resume_access_token());
		$VD->setCurlOption(CURLOPT_SSL_VERIFYPEER, FALSE);

		$me = $VD->get("/" . $memberId)->execute();  

	?>
		<div id="cv">
		<p class="cv-portrait"><img src="<?php echo $me->picture_large; ?>" /></p>
		<h1><?php echo $me->headline; ?></h1>
		<p><?php echo $me->location->city . " " . $me->location->zipcode . " - " . $me->location->area . " - " . $me->location->country; ?></p>

		<h2>Professional experience</h2>
		<?php 
		$career = $me->connection('career')->execute();
		foreach ($career->data as $job) {  
		?>
			<div class="right-content">
				<h3><?php echo $job->position . ", " . $job->company_name; ?></h3>
				<p><?php echo str_replace("\n","<br />", $job->description); ?></p>
				<br /><br />
			</div>
			<p><?php showDate($job->begin, $job->end); ?></p>
			<p class="spacer">&nbsp;</p>
		<?php
		}
		 ?>


		<h2>Education</h2>
		<?php 
		$education = $me->connection('education')->execute();
		foreach ($education->data as $school) {  
		?>
			<div class="right-content">
				<h3><?php echo $school->school->name; ?></h3>
				<p><?php echo $school->degree; ?></p>
			</div>
			<p><?php showDate($school->begin, $school->end); ?></p>
			<p class="spacer">&nbsp;</p>
		<?php
		}
		 ?>

		<h2>Interests</h2>
		<p><?php echo $me->interests; ?></p>
		</div>
		<div class="viadeo-link">
			<p>This resume has been generated by <a href="http://dev.viadeo.com">Viadeo API</a></p>
		</div>
<?php
	} catch (ViadeoAuthenticationException $e) {
			?>
			<div style="background-color:#FF0000;"><p style="color:#FFFFFF;"><b>You need to be connected to Viadeo on your admin control panel</b></p></div>
			<?php
	} catch (ViadeoAPIException $e) {
			?>
			<div style="background-color:#FF0000;"><p style="color:#FFFFFF;"><b>You need to be connected to Viadeo on your admin control panel</b></p></div>
			<?php
	}
}



function showDate($begin, $end) {
	if($end == "") {
		echo "Since " . $begin;
	} else if ($end == $begin) {
		echo $begin;
	} else {
		echo $begin . " - " . $end;
	}
}



?>
