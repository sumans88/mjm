<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        			OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          			OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         			OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   			OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  			OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') 			OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     			OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       			OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      			OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')     			OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


defined('APP_NAME') 					OR define('APP_NAME','MJM CMS');
defined('BACKUP_DIR') 					OR define('BACKUP_DIR',	$_SERVER['DOCUMENT_ROOT'].'/../backup/');
defined('UPLOAD_DIR') 					OR define('UPLOAD_DIR',	dirname(__FILE__)."/../../images/article/");
defined('UPLOAD_DIR_PROFILE') 			OR define('UPLOAD_DIR_PROFILE',	dirname(__FILE__)."/../../images/member/");
defined('EMAIL_TEMPLATE_DIR') 			OR define('EMAIL_TEMPLATE_DIR',	dirname(__FILE__)."/../../application/views/layout/ddi/email_template/");
defined('PAGING_PERPAGE') 				OR define('PAGING_PERPAGE',	6);
defined('PAGING_PERPAGE_MORE') 			oR define('PAGING_PERPAGE_MORE',	6);
defined('PAGING_PERPAGE_GALLERY') 		OR define('PAGING_PERPAGE_GALLERY',	12);
defined('PAGING_PERPAGE_GALLERY_MORE') 	OR define('PAGING_PERPAGE_GALLERY_MORE',	9);
defined('PAGING_PERPAGE_LOG') 			OR define('PAGING_PERPAGE_LOG',	6);
defined('LANGUAGE') 					OR define('LANGUAGE','indonesia');
defined('EXP_DATE_ACTIVATION_EMAIL') 	OR define('EXP_DATE_ACTIVATION_EMAIL', 7); //DAYS
defined('EXP_RESET_PASSWORD_MEMBER') 	OR define('EXP_RESET_PASSWORD_MEMBER', 3); //DAYS
defined('EXP_CHANGE_EMAIL_MEMBER') 		OR define('EXP_CHANGE_EMAIL_MEMBER', 7); //DAYS
defined('EXP_MAX_COUNT_FAILED_LOGIN') 	OR define('EXP_MAX_COUNT_FAILED_LOGIN', 5); //COUNT
defined('EXP_MAX_TIME_FAILED_LOGIN') 	OR define('EXP_MAX_TIME_FAILED_LOGIN', 30); //MINUTES
defined('IS_HTTPS') 					OR define('IS_HTTPS', FALSE); //TRUE OR FALSE
defined('GOOGLE_CAPTCHA_SITE_KEY') 		OR define('GOOGLE_CAPTCHA_SITE_KEY', '6LcX2ZQeAAAAAKPerzeiPumsE931nX8rCHjZy56q');
defined('GOOGLE_CAPTCHA_SECRET_KEY') 	OR define('GOOGLE_CAPTCHA_SECRET_KEY', '6LcX2ZQeAAAAACiR6OtwVLWLkjdii04rMuEn7EDB');
defined('USE_API_EXPERIAN') 			OR define('USE_API_EXPERIAN', FALSE);
defined('MAX_LENGTH_CHAR_COMMENT') 		OR define('MAX_LENGTH_CHAR_COMMENT', 800);
defined('MAX_UPLOAD_SIZE') 				OR define('MAX_UPLOAD_SIZE', 2000000);
defined('MAX_UPLOAD_SIZE_CHEETAH') 		OR define('MAX_UPLOAD_SIZE_CHEETAH', 2000000);
defined('NEWSLETTER_NON_MEMBER_KEY') 	OR define('NEWSLETTER_NON_MEMBER_KEY', 'KMhQn4J_Ea15hqAv');
defined('MARKETING_MEMBER_KEY') 		OR define('MARKETING_MEMBER_KEY', 'LO10SD_ikMAzZs');
defined('NEWSLETTER_MEMBER_KEY') 		OR define('NEWSLETTER_MEMBER_KEY', 'ZEunLA3Oh5AKZ2Yp');

defined('REPLACE_BLACK_LIST_WORDS') 	OR define('REPLACE_BLACK_LIST_WORDS', '#');
defined('DEVELOPMENT_MEMBER')			OR define('DEVELOPMENT_MEMBER', FALSE); //TRUE OR FALSE
defined('WEBTRENS_WIDGET')				OR define('WEBTRENS_WIDGET', FALSE); //TRUE OR FALSE
defined('GOOGLE_ANALYTICS')				OR define('GOOGLE_ANALYTICS', FALSE); //TRUE OR FALSE
defined('CUSTOME_LANG_DIR') 			OR define('CUSTOME_LANG_DIR', dirname(__FILE__)."/../../application/language/");
defined('ROUTE_URL_DIR') 				OR define('ROUTE_URL_DIR',	dirname(__FILE__)."/../../application/config/");
define('KEY_GENERATE_RSS_FEED', '123'); //RSS FEED KEY

define('EMAIL_ADMIN_TO_SEND', '');
// define('EMAIL_ADMIN_TO_SEND', 'amar.ronaldo.m@gmail.com');
// define('EMAIL_ALWAYS_SEND_HERE', 'ammar@deptechdigital.com');
define('CRON_EMAIL_ADMIN', '');

define('FILE_EBOOKS',	dirname(__FILE__)."/../../document/material/");

define('ASSETS_VERSIONING', '1.0.0.14');
/* End of file constants.php */
/* Location: ./application/config/constants.php */

