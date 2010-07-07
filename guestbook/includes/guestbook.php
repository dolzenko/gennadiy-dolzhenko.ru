<?php
/****************************************************************************
 * DRBGuestbook
 * http://www.dbscripts.net/guestbook/
 * 
 * Copyright © 2007-2008 Don B
 ****************************************************************************/

$guestbook_fp = NULL;
$MAX_BAD_WORD_LENGTH = 255;
$MIN_COMMENTS_LENGTH = 10;
$PREVENT_URLS_IN_COMMENTS = TRUE;
$ERROR_MSG_URLS_NOT_ALLOWED = "URL адреса запрещены.";
$ENABLE_EMAIL_FIELD = TRUE;
$ENABLE_URL_FIELD = TRUE;
$ENABLE_COMMENT_FIELD = TRUE;
$MIN_SECONDS_BETWEEN_POSTS = 120;
$ERROR_MSG_FLOOD_DETECTED = "Вы пытаетесь отправлять отзывы слишком часто.";
$READ_ONLY_MODE = FALSE;

function guestbook_file_path() {
 	global $DATA_FOLDER;
 	return dirname(__FILE__) . '/../' . $DATA_FOLDER . "/" . "guestbook.txt";
}
 
function guestbook_open_for_read() {
 	global $guestbook_fp;
 	
 	$guestbook_fp = @fopen(guestbook_file_path(), "rb");
 	if($guestbook_fp !== FALSE) {
 		if(@flock($guestbook_fp, LOCK_SH) === FALSE) {
 			guestbook_close();
 			return FALSE;
 		}
 	} 
 	return $guestbook_fp;
}
 
function guestbook_open_for_writing() {
 	global $guestbook_fp;
 	$guestbook_fp = @fopen(guestbook_file_path(), "r+b");
 	if($guestbook_fp !== FALSE) {
 		if(@flock($guestbook_fp, LOCK_EX) === TRUE) {
 			if(@ftruncate($guestbook_fp, 0) === FALSE) {
 				guestbook_close();
 				return FALSE;
 			}
 		} else {
 			guestbook_close();
 			return FALSE;
 		}
 	}
 	return $guestbook_fp;
}
 
function guestbook_forward($forward_count) {
 	global $guestbook_fp;
 	$count = 0;
 	while($count < $forward_count && !feof($guestbook_fp)) {
 		fgets($guestbook_fp);
 		$count += 1;
 	}
 	
}

function guestbook_next_id() {
	if(guestbook_open_for_read() === FALSE) {
		return 0;
	} else {
		$entry = guestbook_next();
		guestbook_close();
		if($entry === FALSE) {
			return 0;
		} else {
			return ((int)$entry['id']) + 1;
		}
	}
}

function entry_explode($raw_entry) {
	return explode('|', trim($raw_entry));
}
 
function guestbook_next() {
 	global $guestbook_fp;

	$entry_raw = fgets($guestbook_fp);
	if(feof($guestbook_fp)) {
		return FALSE;
	} else if(empty($entry_raw)) {
		return FALSE;
	} else {
		
		$entry_components = entry_explode($entry_raw);
		$entry_components = array_map('rawurldecode', $entry_components);
		$entry_components = array_map('htmlspecialchars', $entry_components);
		
		$entry = @Array(
			"id" => $entry_components[0],
			"name" => $entry_components[1],
			"email" => $entry_components[2],
			"url" => $entry_components[3],
			"comments" => $entry_components[4],
			"timestamp" => $entry_components[5],
			"ipaddress" => $entry_components[6]
		);
		return $entry;
		
	}

}

function getIdToIdxMap($entries) {
 	$id_to_idx = @Array();
 	$ent_idx = 0;
 	foreach ($entries as $entry) {
 		$id_to_idx[ $entry[0] ] = $ent_idx;
 		$ent_idx++;
 	} 
	return $id_to_idx;
}

function guestbook_delete_entries($idArray, $banip = FALSE) {
 	global $dbs_error;
 	global $guestbook_fp;
 	
 	// Get raw data from file
 	if(guestbook_open_for_read() === FALSE) {	// Aquires shared lock on guestbook file
 		die("Unable to open guestbook file for reading.");
 	}
 	$raw_entries = @file(guestbook_file_path());
 	guestbook_close();	// Releases shared lock 
 	if($raw_entries === FALSE) {
 		die("Unable to get entries from guestbook.");
 	}
 	
 	// Split entries into components
 	$entries = array_map('entry_explode', $raw_entries);
 	
 	// Get mapping between indices and ids
 	$id_to_idx = getIdToIdxMap($entries);
 	
 	// Remove entries by id
 	foreach ($idArray as $id) {
 		
 		// Validate ID
 		if(!isset($id_to_idx[$id])) {
 			die("Invalid entry ID.");
 		}
 		
	 	// Get array index of entry from id
	 	$idx = $id_to_idx[$id];
	 	
	 	if($idx === 0 || !empty($idx)) {

			// Handle IP ban
		 	if($banip) {
		 		
		 		// Get IP address
		 		$entry_components = $entries[$idx];
				$entry_components = array_map('rawurldecode', $entry_components);
				
				if(isset($entry_components[6])) {
					$ipAddress = $entry_components[6];
					
					// If not empty and not already banned, add to ban list
					if(!empty($ipAddress) && !is_banned($ipAddress)) {
						ban_add($ipAddress);
					}
				}
				
		 	}
		 	
		 	// Delete entry from raw entries list and decrement count
		 	unset($raw_entries[$idx]);
		 	
	 	}

 	}
 	
 	// Create flat data for file
 	$raw_entries_flat = implode("", $raw_entries);
 	unset($raw_entries); // Free memory
 	
	if(guestbook_open_for_writing() === FALSE) {
		die("Unable to open guestbook file for writing."); 
	}
	
	// Rewrite data to file
	fputs($guestbook_fp, $raw_entries_flat);
	unset($raw_entries_flat); // Free memory
 	
 	guestbook_close();
 	
 	// Update entry count
 	set_guestbook_entries_count(guestbook_count_entries());
 	
}

function guestbook_validate($entry) {
 	global $MAX_NAME_LENGTH;
 	global $MAX_EMAIL_LENGTH;
 	global $MAX_URL_LENGTH;
 	global $MAX_COMMENTS_LENGTH;
 	global $MIN_COMMENTS_LENGTH;
	global $NAME_FIELD_NAME;
	global $EMAIL_FIELD_NAME;
	global $URL_FIELD_NAME;
	global $COMMENTS_FIELD_NAME;
	global $ERROR_MSG_BAD_WORD;
 	global $dbs_error;
 	
 	$dbs_error = "";
 	
 	validate_notempty($entry, "name", $NAME_FIELD_NAME);
 	global $ENABLE_COMMENT_FIELD;
 	if($ENABLE_COMMENT_FIELD === TRUE) {
 		if(validate_notempty($entry, "comments", $COMMENTS_FIELD_NAME)) {
 			validate_minlength($entry, "comments", $MIN_COMMENTS_LENGTH, $COMMENTS_FIELD_NAME);
 		}
 	} else {
 		if(isset($entry["comments"])) {
 			die("Comments field is disabled.");
 		}
 	}
 	validate_length($entry, "name", $MAX_NAME_LENGTH, $NAME_FIELD_NAME);
 	validate_notags($entry, "name", $NAME_FIELD_NAME);
 	validate_length($entry, "email", $MAX_EMAIL_LENGTH, $EMAIL_FIELD_NAME);
 	validate_email($entry, "email", $EMAIL_FIELD_NAME);
 	validate_length($entry, "url", $MAX_URL_LENGTH, $URL_FIELD_NAME);
 	validate_url($entry, "url", $URL_FIELD_NAME);
 	validate_length($entry, "comments", $MAX_COMMENTS_LENGTH, $COMMENTS_FIELD_NAME);
 	validate_notags($entry, "comments", $COMMENTS_FIELD_NAME);
 	
 	if( (isset($entry["name"]) && has_bad_word($entry["name"])) || (isset($entry["comments"]) && has_bad_word($entry["comments"]))) {
 		$dbs_error .= htmlspecialchars($ERROR_MSG_BAD_WORD) . '<br />';
 	}
 	
 	global $PREVENT_URLS_IN_COMMENTS;
 	if($PREVENT_URLS_IN_COMMENTS === TRUE && isset($entry["comments"]) && has_url($entry["comments"])) {
 		global $ERROR_MSG_URLS_NOT_ALLOWED;
 		$dbs_error .= htmlspecialchars($ERROR_MSG_URLS_NOT_ALLOWED) . '<br />';
 	}
 	
	// Challenge-response test
 	global $CHALLENGE_ENABLED;
 	if($CHALLENGE_ENABLED === TRUE) {
 		
 		// Check entered value
 		global $CHALLENGE_FIELD_PARAM_NAME;
 		$entered_challenge_value = $entry[$CHALLENGE_FIELD_PARAM_NAME];
 		if(!isChallengeAccepted($entered_challenge_value)) {
 			
 			// Android!
 			global $ERROR_MSG_BAD_CHALLENGE_STRING;
 			$dbs_error .= htmlspecialchars($ERROR_MSG_BAD_CHALLENGE_STRING) . '<br />';
 			
 		}
 		
 	}

 	
 	return empty($dbs_error);
}
 
function guestbook_add($entry) {
	global $READ_ONLY_MODE;
 	global $dbs_error;
 	
 	if(guestbook_validate($entry)) {
 		
 		if($READ_ONLY_MODE === TRUE) {
 			$dbs_error = htmlspecialchars("This guestbook is in read-only mode.");
 			return FALSE;
 		}
 	
	 	$now = gmstrftime( time() );
	 	$ipaddress = $_SERVER['REMOTE_ADDR']; 
	 	
	 	if( is_flood_detected($ipaddress) ) {
	 		global $ERROR_MSG_FLOOD_DETECTED;
			$dbs_error = htmlspecialchars($ERROR_MSG_FLOOD_DETECTED); 
			return FALSE;
	 	}
	 	
	 	$entry_stripped = array_map("strip_tags", $entry);
	 	$entry_encoded = array_map("rawurlencode", $entry_stripped);
	 	
	 	// Create file if it does not exist
	 	if(!file_exists(guestbook_file_path())) {
	 		if(touch(guestbook_file_path()) === FALSE) {
	 			$dbs_error = htmlspecialchars("Unable to create guestbook file in data folder.");
	 			return FALSE;
	 		}
	 	}
	 	
	 	// Get existing entries
 		if(guestbook_open_for_read() === FALSE) {	// Aquires shared lock on guestbook file
 			$dbs_error = htmlspecialchars("Unable to open guestbook file for reading.");
 			return FALSE;
	 	}
	 	$oldContents = @file_get_contents(guestbook_file_path());
	 	guestbook_close();	// Releases shared lock
	 	if($oldContents === FALSE) {
			$dbs_error = htmlspecialchars("Unable to get guestbook file contents."); 
			return FALSE;
	 	}
	 	
	 	$nextId = guestbook_next_id();

		if(guestbook_open_for_writing() === FALSE) {
			$dbs_error = htmlspecialchars("Unable to open guestbook file for writing."); 
			return FALSE;
		}

		// Write new entry
	 	global $guestbook_fp;
	 	fputs($guestbook_fp,
	 		$nextId . "|" .
	 		value_or_blank($entry_encoded, 'name') . "|" . 
	 		value_or_blank($entry_encoded, 'email') . "|" . 
	 		value_or_blank($entry_encoded, 'url') . "|" . 
	 		value_or_blank($entry_encoded, 'comments') . "|" . 
	 		$now . "|" .
	 		$ipaddress . "\n"
	 	);
	 	
	 	// Append existing entries to file
	 	fputs($guestbook_fp, $oldContents);
	 	unset($oldContents);	// Free memory
	 	guestbook_close();
	 	
	 	// Update entry count
	 	set_guestbook_entries_count(guestbook_count_entries());
	 	
	 	// Send notification
	 	global $ADMIN_EMAIL_ADDRESS;
	 	if(isset($ADMIN_EMAIL_ADDRESS) && !empty($ADMIN_EMAIL_ADDRESS)) {
			if(mail($ADMIN_EMAIL_ADDRESS, "Your guestbook has been signed by " . 
				value_or_blank($entry_stripped, 'name'),
				"Name: " . value_or_blank($entry_stripped, 'name') . "\n" .
				"E-Mail: " . value_or_blank($entry_stripped, 'email') . "\n" .
				"URL: " . value_or_blank($entry_stripped, 'url') . "\n" . 
				"Comments: \n" . 
				value_or_blank($entry_stripped, 'comments'), $ADMIN_EMAIL_ADDRESS) !== TRUE
			) {
				$dbs_error = htmlspecialchars("Unable to send notification.");
				return FALSE;
			}
	 	}
	 	
	 	return TRUE;
	 	
 	}
 	
 	return FALSE;
 	
} 
 
function guestbook_close() {
 	global $guestbook_fp;
 	@fclose($guestbook_fp);
}

function ban_file_path() {
 	global $DATA_FOLDER;
 	return dirname(__FILE__) . '/../' . $DATA_FOLDER . "/" . "bans.txt";
}
 
function ban_list() {
	$banlist = @file(ban_file_path());
	if($banlist !== FALSE) {
		$banlist = array_map("trim", $banlist);
	}
 	return $banlist;
}

function is_banned($ipaddress) {
	$bans = ban_list();
	if($bans !== FALSE) {
		return( array_search(trim($ipaddress), $bans) !== FALSE );
	} else {
		return FALSE;
	}
}
 
function ban_add($ipaddress) {
 	$ban_fp = @fopen(ban_file_path(), "a");
 	if($ban_fp === FALSE) {
 		die("Unable to open ban file for writing");
 	}
 	@flock($ban_fp, LOCK_EX); 
 	fputs($ban_fp, $ipaddress . "\n");
 	@fclose($ban_fp);
}

function unban($ipAddressArray) {

 	// Remove bans by id
	$bans = ban_list();
	if($bans !== FALSE) {

		// Remove ip addresses from ban list
	 	foreach ($ipAddressArray as $ipaddress) {
	 		
			$idx = array_search(trim($ipaddress), $bans);
			if($idx !== FALSE) {
				unset($bans[$idx]);
			} else {
				die("An invalid IP address was specified.");
			}
	 		
	 	}
	 	
	 	// Create flat data for file
	 	$raw_bans_flat = implode("\n", $bans);
	 	if(!empty($raw_bans_flat)) $raw_bans_flat .= "\n"; 
	
		// Rewrite data to file 	
	 	$ban_fp = @fopen(ban_file_path(), "w");
	 	if($ban_fp === FALSE) {
	 		die("Unable to open ban file for writing.");
	 	}
		@flock($ban_fp, LOCK_EX);
		fputs($ban_fp, $raw_bans_flat);
		@fclose($ban_fp);
		
	} else {
		die("Unable to get list of current bans.");
	}

}

function bad_word_file_path() {
 	global $DATA_FOLDER;
 	return dirname(__FILE__) . '/../' . $DATA_FOLDER . "/" . "bad_words.txt";
}
 
function bad_word_list() {
	$bad_word_list = @file(bad_word_file_path());
	if($bad_word_list !== FALSE) {
		$bad_word_list = array_map("trim", $bad_word_list);
	}
 	return $bad_word_list;
}

function is_bad_word($word) {
	$bad_words = bad_word_list();
	if($bad_words !== FALSE) {
		return( array_casesearch(trim($word), $bad_words) !== FALSE );
	} else {
		return FALSE;
	}
}

function has_url($text) {
	if(preg_match('/http:|https:|ftp:/i', $text)) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function has_bad_word($text) {
	$bad_words = bad_word_list();
	
    if($bad_words !== FALSE) {
	 	foreach ($bad_words as $bad_word) {
	 		if(preg_match('/(\b|[^A-Za-z0-9])' . $bad_word . '(\b|[^A-Za-z0-9])/i', $text)) {
	 			return TRUE;
	 		}
	 	}
    }
	 	
	return FALSE;
}
 
function bad_word_add($word) {
	global $MAX_BAD_WORD_LENGTH;
	if($word === NULL || strlen($word) === 0) {
		return FALSE;
	}
	if(strlen($word) > $MAX_BAD_WORD_LENGTH) {
		die("That word is too long.");
	}
	if(is_bad_word($word)) {
		return FALSE;
	}
 	$bad_word_fp = @fopen(bad_word_file_path(), "a");
 	if($bad_word_fp === FALSE) {
 		die("Unable to open bad word file for writing");
 	}
 	@flock($bad_word_fp, LOCK_EX); 
 	fputs($bad_word_fp, $word . "\n");
 	@fclose($bad_word_fp);
}

function remove_bad_word($wordArray) {

 	// Remove bad_words by id
	$bad_words = bad_word_list();
	if($bad_words !== FALSE) {

		// Remove words from bad word list
	 	foreach ($wordArray as $word) {
	 		
			$idx = array_search(trim($word), $bad_words);
			if($idx !== FALSE) {
				unset($bad_words[$idx]);
			} else {
				die("An invalid bad word was specified.");
			}
	 		
	 	}
	 	
	 	// Create flat data for file
	 	$raw_bad_words_flat = implode("\n", $bad_words);
	 	if(!empty($raw_bad_words_flat)) $raw_bad_words_flat .= "\n";
	
		// Rewrite data to file 	
	 	$bad_word_fp = @fopen(bad_word_file_path(), "w");
	 	if($bad_word_fp === FALSE) {
	 		die("Unable to open bad word file for writing.");
	 	}
		@flock($bad_word_fp, LOCK_EX);
		fputs($bad_word_fp, $raw_bad_words_flat);
		@fclose($bad_word_fp);
		
	} else {
		die("Unable to get list of current bad words.");
	}

}

function is_flood_detected($ipaddress) {
	global $MIN_SECONDS_BETWEEN_POSTS;
	if($MIN_SECONDS_BETWEEN_POSTS <= 0) return FALSE;
 	$timestamp_threshold = time() - $MIN_SECONDS_BETWEEN_POSTS;
	$guestbookExists = (guestbook_open_for_read() !== FALSE); 	
 	if($guestbookExists) {

		// Iterate through entries that occured after flood threshold 		
 		while( ($entry = guestbook_next()) !== FALSE &&
 		intval($entry["timestamp"]) >= $timestamp_threshold) {
 			
 			if($entry["ipaddress"] === $ipaddress) {
 				guestbook_close();  
 				return TRUE; 
 			}
 			
 		}
 		
 		guestbook_close(); 
 	}
	
	return FALSE;
}

function guestbook_summary_file_path() {
 	global $DATA_FOLDER;
 	return dirname(__FILE__) . '/../' . $DATA_FOLDER . "/" . "guestbook_summary.txt";
}

function guestbook_count_entries() {
 	
 	// Get raw data from file
 	if(guestbook_open_for_read() === FALSE) {	// Aquires shared lock on guestbook file
 		return 0;
 	}
 	$raw_entries = @file(guestbook_file_path());
 	guestbook_close();	// Releases shared lock 
 	if($raw_entries === FALSE) {
 		return 0;
 	}
 	
 	// Return size of entry array
 	return sizeof($raw_entries);

}

$guestbook_entries_count = -1;

function get_guestbook_entries_count() {
	global $guestbook_entries_count;
	
	// Count has already been loaded, return cached value
	if($guestbook_entries_count >= 0) {
		return $guestbook_entries_count;
	}

	// Load existing
	$summarylist = @file(guestbook_summary_file_path());
	if($summarylist !== FALSE) {
		// Cache the count
		$guestbook_entries_count = intval($summarylist[0]);
		return $guestbook_entries_count;
	} else {
		// Create summary and return count
		$count = guestbook_count_entries();
		set_guestbook_entries_count($count);
		return $count;
	}

}

function set_guestbook_entries_count($count) {

	// Open/create summary file
 	$summary_fp = @fopen(guestbook_summary_file_path(), "w");
 	if($summary_fp === FALSE) {
 		die("Unable to open summary file for writing");
 	}
 	@flock($summary_fp, LOCK_EX);
 	fputs($summary_fp, $count . "\n");
 	fclose($summary_fp);
 	
 	// Update cached count
 	global $guestbook_entries_count;
 	$guestbook_entries_count = $count;

}


?>
