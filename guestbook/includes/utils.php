<?php
/****************************************************************************
 * DRBGuestbook
 * http://www.dbscripts.net/guestbook/
 * 
 * Copyright © 2007-2008 Don B
 ****************************************************************************/
 
$dbs_error = NULL;
$ERROR_MSG_MIN_LENGTH = 'The %s field cannot accept values less than %s characters in length.';
$TEMPLATE_NAME = "default";

function include_from_template($filename) {
	global $TEMPLATE_NAME;
	require(dirname(__FILE__) . '/../template/' . $TEMPLATE_NAME . '/' . $filename);
}

function path_to_file_from_template($filename) {
	global $TEMPLATE_NAME;
	global $base_url;
	return $base_url . 'template/' . $TEMPLATE_NAME . '/' . $filename;
}

function relative_location($relativeURI) {
	$baseURL = base_URL(); 
	header("Location: $baseURL/$relativeURI");
}

function base_URL() {
	$domain = $_SERVER['HTTP_HOST'];
	$baseURI = rtrim(dirname($_SERVER['PHP_SELF']), '\\/');
	
	// Work around for sites using sub directory hosting
	if(substr($baseURI, 0, strlen("/" . $domain . "/")) === "/" . $domain . "/") {
		$baseURI = substr($baseURI, strlen("/" . $domain . "/"));
	}
	
	return "http://$domain$baseURI";
}

function get_file_extension($filepath) {
	$lastdot = strrpos($filepath, ".");
	if($lastdot === FALSE) {
		return FALSE;
	}
	$filetype = strtolower(substr($filepath, $lastdot + 1));
	return $filetype;
}

function replace_file_extension($filepath, $newext) {
	$lastdot = strrpos($filepath, ".");
	if($lastdot === FALSE) {
		return FALSE;
	}
	return substr($filepath, 0, $lastdot + 1) . $newext;
}

function value_or_blank($assoc, $key) {
	if(isset($assoc[$key])) {
		return $assoc[$key];
	} else {
		return '';
	}
}

function validate_length($assoc, $key, $maxlen, $name = NULL) {
	global $dbs_error;
	
	if(isset($assoc[$key]) && strlen($assoc[$key]) > $maxlen) {
		global $ERROR_MSG_MAX_LENGTH;
		if(!isset($name)) $name = $key;
		$dbs_error .= sprintf($ERROR_MSG_MAX_LENGTH . '<br />', htmlspecialchars($name), htmlspecialchars($maxlen));
		return FALSE;
	}
	return TRUE;
}

function validate_minlength($assoc, $key, $minlen, $name = NULL) {
	global $dbs_error;
	
	if(isset($assoc[$key]) && strlen($assoc[$key]) < $minlen) {
		global $ERROR_MSG_MIN_LENGTH;
		if(!isset($name)) $name = $key;
		$dbs_error .= sprintf($ERROR_MSG_MIN_LENGTH . '<br />', htmlspecialchars($name), htmlspecialchars($minlen));
		return FALSE;
	}
	return TRUE;
}

function validate_notempty($assoc, $key, $name = NULL) {
	global $dbs_error;
	
	if(!isset($assoc[$key]) || empty($assoc[$key])) {
		global $ERROR_MSG_REQUIRED;
		if(!isset($name)) $name = $key;
		$dbs_error .= sprintf($ERROR_MSG_REQUIRED . '<br />', htmlspecialchars($name));
		return FALSE;
	}
	return TRUE;
}

function validate_email($assoc, $key, $name = NULL) {
	global $dbs_error;

	// Determine if valid E-Mail
	if(isset($assoc[$key]) && !empty($assoc[$key]) && !preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/D", $assoc[$key])) {
		global $ERROR_MSG_EMAIL;
		if(!isset($name)) $name = $key;
		$dbs_error .= sprintf($ERROR_MSG_EMAIL . '<br />', htmlspecialchars($name));
		return FALSE;
	}
	return TRUE;

}

function validate_url($assoc, $key, $name = NULL) {
	global $dbs_error;
	if(!isset($name)) $name = $key;
	
	// Determine if valid URL
	if(isset($assoc[$key]) && !empty($assoc[$key])) {
		$url_parts = @parse_url($assoc[$key]);
		if( empty($url_parts) || empty($url_parts["scheme"]) || !preg_match("/^(http):\/\/[A-Za-z0-9\-]+(\.[A-Za-z0-9\-]+)*(\/[A-Za-z0-9\$\-\_\.\+\!\*\'\(\)\,\%\;\:\@\&\=\/]*(\?[A-Za-z0-9\$\-\_\.\+\!\*\'\(\)\,\%\;\:\@\&\=\/]*)?)*$/D", $assoc[$key]) ) {
			global $ERROR_MSG_URL_INVALID;
			$dbs_error .= sprintf($ERROR_MSG_URL_INVALID . '<br />', htmlspecialchars($name));
			return FALSE;
		} else {
			if( $url_parts["scheme"] != "http" ) {
				global $ERROR_MSG_URL_BAD_PROTOCOL;
				$dbs_error .= sprintf($ERROR_MSG_URL_BAD_PROTOCOL . '<br />', htmlspecialchars($name));
				return FALSE;
			}
		}
	
	}
	return TRUE;
}

function validate_notags($assoc, $key, $name = NULL) {
	global $dbs_error;
	
	if(isset($assoc[$key]) && !empty($assoc[$key])) {
		if($assoc[$key] !== strip_tags($assoc[$key])) {
			global $ERROR_MSG_TAGS_NOT_ALLOWED;
			if(!isset($name)) $name = $key;
			$dbs_error .= sprintf($ERROR_MSG_TAGS_NOT_ALLOWED . '<br />', htmlspecialchars($name));
			return FALSE;
		}
	}
	return TRUE;
}

function confirm_install() {

	// Confirm that default password was changed
	global $ADMIN_PASSWORD;
	if($ADMIN_PASSWORD === "password") {
		die("You must change the default admin password in config.php before using the application.");
	}
}

function the_site_title() {
	global $SITE_TITLE;
	echo htmlspecialchars($SITE_TITLE);
}

function array_casesearch($needle, $haystack) {
	foreach ($haystack as $key => $value) {
		if (strcasecmp($needle, $value) === 0) return $key;
	}
	return FALSE;
}

function stripslashes_recursive($value) {
	return is_array($value)?array_map('stripslashes_recursive', $value):stripslashes($value);
}

/* DO NOT REMOVE OR HIDE THE CREDIT BELOW, PER LICENSE! */
function the_credits() {
	echo("<div class=\"credit\">Powered by DRBGuestbook<br /><a href=\"http://www.dbscripts.net/guestbook/\">PHP Guestbook</a> &middot; <a href=\"http://www.dbscripts.net/\">Free PHP Scripts</a></div>\n");
}
/* END CREDIT */ 
	
?>
