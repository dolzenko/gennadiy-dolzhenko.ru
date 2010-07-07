<?php
/****************************************************************************
 * DRBGuestbook
 * http://www.dbscripts.net/guestbook/
 * 
 * Copyright © 2007-2008 Don B
 ****************************************************************************/
 
// Field min/max lengths
$MAX_NAME_LENGTH = 128;
$MAX_EMAIL_LENGTH = 256;
$MAX_URL_LENGTH = 512;
$MIN_COMMENTS_LENGTH = 10;
$MAX_COMMENTS_LENGTH = 8192;

// Field enabled status; set to FALSE to hide the corresponding field.
$ENABLE_EMAIL_FIELD = TRUE;
$ENABLE_URL_FIELD = TRUE;
$ENABLE_COMMENT_FIELD = TRUE;

// Template Name
//     Corresponds to the name of a subfolder under the "template" subfolder 
//     containing a valid HTML/XHTML template.
$TEMPLATE_NAME = "default";

// Site Name
//     Change this to match the name of your site.
$SITE_TITLE = "Guestbook";

// Admin login configuration
//     Change this to whatever you want the admin login to be.
$ADMIN_USERNAME = "admin";
$ADMIN_PASSWORD = "[etnthrf";

// Admin e-mail address
//     Change this to the e-mail address to send notifications to.
//     If left blank, notifications will not be sent.
$ADMIN_EMAIL_ADDRESS = "";

// Folder where guestbook data files will be stored 
$DATA_FOLDER = "data";

// Name of the subfolder where the administration panel is located.
// Change this if you decide to rename the admin folder.
$ADMIN_FOLDER = "admin";

// Max entries to display per page.
$MAX_ENTRIES_PER_PAGE = 25;

// Date/Time format
//     Format string used for date/time display.  For format string syntax,
//     see: http://php.net/strftime
$DATE_TIME_FORMAT = "%d.%m.%Y %H:%M";

// Challenge-response test for spam prevention
//     Set to FALSE if GD library is not installed/enabled
$CHALLENGE_ENABLED = TRUE;

// Prevents URLs from appearing in comments, as an anti-spam measure
$PREVENT_URLS_IN_COMMENTS = TRUE;

// Flood protection setting; forces users to wait the specified number of 
// seconds before adding another post.  Setting this to zero effectively
// disables flood protection.
$MIN_SECONDS_BETWEEN_POSTS = 120;

// Path to the main guestbook page.  This value will be used when 
// constructing URLs for links and forms on the guestbook page.    
// Change this value if the main guestbook file will be named something other
// than index.php, or if you are unable or unwilling to set index.php as the  
// directory index for some reason. 
$GUESTBOOK_URL_PATH = ".";

?>