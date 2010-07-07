========================================
DRBGuestbook
http://www.dbscripts.net/guestbook/

By Don B

Version 1.1.19
========================================

Requirements
------------

PHP 4.4 or higher
GD library (if the challenge-response test is enabled)

    * It is strongly recommended that register_globals be set to off 
      in your php.ini file.
    * AllowOverride must be enabled for the directory where your guestbook 
      is installed in your Apache httpd.config file.

Installation
------------

1) Extract the ZIP file into the desired destination folder on your website.
   Optionally, you may rename the "guestbook" folder to whatever name is 
   desired, as this folder will be part of the guestbook URL.

2) Modify the settings in config.php to match your environment and desired 
   configuration.  Descriptions of the settings are included in the 
   comments within that file.
   
   Minimally, you will need to modify the administrator password.

3) Optionally, you may customize strings.php to use different labels
   than the default ones provided.

4) Confirm that the /data subfolder and the files within it have write  
   permissions enabled.

To perform administrative tasks, such as deleting posts, browse to 
the /admin folder with your web browser.  You will be prompted 
to log in with the username and password you configured in step 2.

Upgrade
-------

If you are already running DRBGuestbook and need to upgrade to the latest 
version, you must perform the following steps.

1) Backup your config.php and strings.php files, the contents of your "data" 
   folder, and any files in the "template" directory that you have customized.

2) Extract the ZIP file into the desired destination folder on your website, 
   overwriting the existing files.  If you renamed the "guestbook" folder, 
   you may need to copy the files into the correctly named folder.
   
3) Replace your config.php and strings.php files, "data" folder contents, and 
   any customized template files that you backed up.
   
4) If you do not have GD library installed, you must disable the 
   challenge-response test by setting the $CHALLENGE_ENABLED variable to 
   FALSE in your config.php file.

Challenge-Response Test
-----------------------

DRBGuestbook supports a challenge-response test.  When this feature is 
enabled, an image containing a code is displayed to the end-user, who must 
enter the code into a text field.  The purpose of this test is to reduce spam 
postings by "bots" that cannot read the code in the image.

In order to use the challenge-response test, you must be using PHP 
with the GD library image functions properly installed and enabled.  In 
addition, this feature currently requires that cookies to be enabled 
on the client web browser.  The challenge-response test can be disabled by
setting the $CHALLENGE_ENABLED variable to FALSE in config.php.

Uninstall
---------

You can uninstall DRBGuestbook by following the steps below.

Please note that ALL DATA WILL BE LOST if you uninstall!

1) Delete the subfolder where you put the DRBGuestbook 
   files from your webserver.

Customization
-------------

You are free to customize the files in the template folder, and to 
create new templates.  You may not, however, remove or alter the credit 
link without the explicit permission of the author.

Modifying the PHP files other than config.php, strings.php, and those inside 
the themes folder is not recommended, as this may make it difficult or 
impossible to upgrade to newer versions of DRBGuestbook in the future.

Flat File Database
------------------

DRBGuestbook stores its data in a flat file database (i.e. text files).  
It is highly recommended that you make back up copies of these files 
on a frequent basis.  The files are named "guestbook.txt" and 
"guestbook_summary.txt" and they can be found in the "data" folder.

Note that manually editing these files can result in data corruption, 
therefore doing so is not recommended.

License
-------

To use DRBGuestbook free of charge, all that I ask is that the credit link at 
the bottom of the page not be removed or altered.

You may not distribute code modifications to DRBGuestbook without permission 
from the author.

DRBGuestbook is provided "as is", without warrant of any kind, either 
expressed or implied.

The author is not liable for anything that results from your use of this code.
