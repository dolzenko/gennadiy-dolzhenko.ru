<?php
/****************************************************************************
 * DRBGuestbook
 * http://www.dbscripts.net/guestbook/
 * 
 * Copyright © 2007-2008 Don B
 ****************************************************************************/

$DEMO_MODE = FALSE;
$READ_ONLY_MODE = FALSE;

$base_url = "./../";
require_once(dirname(__FILE__) . '/../includes/utils.php');
require_once(dirname(__FILE__) . '/../includes/guestbook.php');
require_once(dirname(__FILE__) . '/../includes/views.php');
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../strings.php');

// Confirm that application is fully installed
confirm_install();

// Start session
session_start();

// Check if logged in
if(!isset($_SESSION['username']) || !isset($_SESSION['admin'])) {
	
	// Redirect to login page
	relative_location('../login.php');
	exit();
	
}

// Check if admin
if($_SESSION['admin'] !== "TRUE") {
	
	// Redirect to home page
	relative_location('..');
	exit();

}

// Handle actions
if(isset($_REQUEST['action'])) {
	global $READ_ONLY_MODE;
	global $DEMO_MODE;
	
	$action = $_REQUEST['action'];
	switch($action) {
		
		case "delete":
			if($READ_ONLY_MODE === FALSE && $DEMO_MODE === FALSE && isset($_POST['id'])) {
				guestbook_delete_entries($_POST['id'], (isset($_POST['banip']) && $_POST['banip'] == "true") );
			}
			show_entries_admin();
			break;
			
		case "showbans":
			show_bans_admin();
			break;
			
		case "unban";
			if($READ_ONLY_MODE === FALSE && $DEMO_MODE === FALSE && isset($_POST['id'])) {
				unban($_POST['id']);
			}
			show_bans_admin();
			break;
			
		case "showbadwords":
			show_bad_words_admin();
			break;
			
		case "removebadword";
			if($READ_ONLY_MODE === FALSE && $DEMO_MODE === FALSE && isset($_POST['id'])) {
				remove_bad_word($_POST['id']);
			}
			show_bad_words_admin();
			break;
			
		case "addbadword";
			if($READ_ONLY_MODE === FALSE && $DEMO_MODE === FALSE && isset($_POST['word'])) {
				bad_word_add($_POST['word']);
			}
			show_bad_words_admin();
			break;
			
		case "logout":
		
			// Kill session and force expire of session cookie on client
			$_SESSION = array();
			if(isset($_COOKIE[session_name()])) setcookie(session_name(), '', time()-7200, '/');
			session_destroy();

			// Redirect back to login page		
			relative_location('../login.php');
			exit();
			
		default:
			die('Invalid action.');
			
	}
	

} else {
	
	show_entries_admin();
	
}

function show_logout_button() {
	
?>
<div>
<form method="post" action=".">
<p>
<input type="hidden" name="action" value="logout" />
<input type="submit" value="Logout" class="submit" />
</p>
</form>
</div>
<?php

}

function show_checkboxes_js($formName) {
	
?>
<script type="text/javascript">
//<![CDATA[
function changeAllCheckboxes(checked) {
	var entriesForm = document.getElementById("<?php echo $formName; ?>");
	for (i = 0; i < entriesForm.length; i++) {
		if(entriesForm.elements[i].name == "id[]") {
			entriesForm.elements[i].checked = checked;
		}
	}
}
//]]>
</script>
<?php

}

function show_bans_admin() {
	
	include_from_template('header.php');
	show_logout_button();
	show_checkboxes_js("bansForm");
	
?>

<p>
<a href=".">Guestbook Entries Maintenace</a> | 
<a href=".?action=showbadwords">Bad Words Maintenace</a>
</p>

<form method="post" action="." id="bansForm">
<fieldset>
<input type="hidden" name="action" value="unban" />
<input type="submit" value="Unban Selected" class="submit" />
<p>
<a href="#" onclick="changeAllCheckboxes(true); return false;">Check All</a> - 
<a href="#" onclick="changeAllCheckboxes(false); return false;">Uncheck All</a>
</p>
<?php

	// Show bans
	echo("<h2>Current Bans</h2>");
	show_bans();

?>
</fieldset>
</form>
<?php
	include_from_template('footer.php');
}

function show_bad_word_add_form() {
 	global $MAX_BAD_WORD_LENGTH;
 	
?>
<form method="post" action=".">
<fieldset>
<legend><?php echo htmlspecialchars("Add Bad Word"); ?></legend>
<p>

<label for="word">Word:</label>
<input type="text" name="word" id="word" maxlength="<?php echo htmlspecialchars($MAX_BAD_WORD_LENGTH); ?>" class="inputText" />

</p>
<input type="hidden" name="action" value="addbadword" class="submit" />
<input type="submit" value="Add" class="submit" />
</fieldset>  
</form>
<?php
 	
}

function show_bad_words_admin() {
	
	include_from_template('header.php');
	show_logout_button();
	show_checkboxes_js("badWordsForm");
	
?>

<p>
<a href=".">Guestbook Entries Maintenace</a> | <a href=".?action=showbans">Banned IP Address Maintenace</a>
</p>

<?php show_bad_word_add_form(); ?>

<form method="post" action="." id="badWordsForm">
<fieldset>
<input type="hidden" name="action" value="removebadword" />
<input type="submit" value="Remove Selected" class="submit" />
<p>
<a href="#" onclick="changeAllCheckboxes(true); return false;">Check All</a> - 
<a href="#" onclick="changeAllCheckboxes(false); return false;">Uncheck All</a>
</p>
<?php

	// Show bad words
	echo("<h2>Current Bad Words</h2>");
	show_bad_words();

?>
</fieldset>
</form>
<?php
	include_from_template('footer.php');
}

function show_entries_admin() {

	include_from_template('header.php');
	show_logout_button();
	show_checkboxes_js("entriesForm");

?>

<p>
<a href=".?action=showbans">Banned IP Address Maintenace</a> | <a href=".?action=showbadwords">Bad Words Maintenace</a>
</p>

<form method="post" action="." id="entriesForm">
<fieldset>
<input type="hidden" name="action" value="delete" />
<input type="submit" value="Delete Selected" class="submit" />
<p>
Ban IP addresses of deleted entries: <input type="checkbox" name="banip" value="true" />
</p>
<p>
<a href="#" onclick="changeAllCheckboxes(true); return false;">Check All</a> - 
<a href="#" onclick="changeAllCheckboxes(false); return false;">Uncheck All</a>
</p>

<?php

// Show entries
echo("<h2>Current Entries</h2>");
show_entries(NULL, NULL, TRUE, TRUE, TRUE);

?>
</fieldset>
</form>
<?php

include_from_template('footer.php');

}

?>
