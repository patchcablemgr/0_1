<?php
/*** *** *** *** *** ***
* @package Quadodo Login Script
* @file    Install.class.php
* @start   August 3rd, 2007
* @author  Douglas Rennehan
* @license http://www.opensource.org/licenses/gpl-license.php
* @version 1.1.6
* @link    http://www.quadodo.net
*** *** *** *** *** ***
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*** *** *** *** *** ***
* Comments are always before the code they are commenting.
*** *** *** *** *** ***/
if (!defined('IN_INSTALL')) {
    exit;
}

/**
 * Contains all the necessary components for an installation
 */
class Install {

/**
 * @var string $system_version - The version of the system
 */
var $system_version = '3.1.11';

/**
 * @var string $install_error - Contains the installation error
 */
var $install_error = 'There was an error with the installation! This is most likely a bug please report it <a href="http://www.quadodo.net/s.php" target="_blank">here</a>.';

	/**
	 * Construct the class
	 *	@return void but will output error if found
	 */
	function Install() {
        $this->install_directory = dirname(__FILE__);
        $this->main_directory = str_replace('/install', '', $this->install_directory);
        session_start();
        header('Content-Type: text/html; charset=iso-8859-1');

		// Check the version
		if (!version_compare('5.5.0', PHP_VERSION, '<=')) {
		    die('Minimum PHP version required to run this system is: <b>PHP 5.5.0</b>');
		}

		if (!is_readable($this->install_directory . '/schemas/mysql.sql')) {
		    die('All the files in the <b>install/schemas</b> directory must be CHMOD to 755.');
		}

		// Get rid of the slashes if it's turned on
		if (get_magic_quotes_gpc()) {
			// POST Method
			foreach ($_POST as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key2 => $value2) {
						if (is_array($value2)) {
							foreach ($value2 as $key3 => $value3) {
								if (is_array($value3)) {
									foreach ($value3 as $key4 => $value4) {
										// Can't go any deeper
										if (is_array($value4)) {
										    $_POST[$key][$key2][$key3][$key4] = $value4;
										}
										else {
										    $_POST[$key][$key2][$key3][$key4] = stripslashes($value4);
										}
									}
								}
								else {
								    $_POST[$key][$key2][$key3] = stripslashes($value3);
								}
							}
						}
						else {
						    $_POST[$key][$key2] = stripslashes($value2);
						}
					}
				}
				else {
				    $_POST[$key] = stripslashes($value);
				}
			}

			// GET Method
			foreach ($_GET as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key2 => $value2) {
						if (is_array($value2)) {
							foreach ($value2 as $key3 => $value3) {
								if (is_array($value3)) {
									foreach ($value3 as $key4 => $value4) {
										// Can't go any deeper
										if (is_array($value4)) {
										    $_GET[$key][$key2][$key3][$key4] = $value4;
										}
										else {
										    $_GET[$key][$key2][$key3][$key4] = stripslashes($value4);
										}
									}
								}
								else {
								    $_GET[$key][$key2][$key3] = stripslashes($value3);
								}
							}
						}
						else {
						    $_GET[$key][$key2] = stripslashes($value2);
						}
					}
				}
				else {
				    $_GET[$key] = stripslashes($value);
				}
			}

			// COOKIE Method
			foreach ($_COOKIE as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key2 => $value2) {
						if (is_array($value2)) {
							foreach ($value2 as $key3 => $value3) {
								if (is_array($value3)) {
									foreach ($value3 as $key4 => $value4) {
										// Can't go any deeper
										if (is_array($value4)) {
										    $_COOKIE[$key][$key2][$key3][$key4] = $value4;
										}
										else {
										    $_COOKIE[$key][$key2][$key3][$key4] = stripslashes($value4);
										}
									}
								}
								else {
								    $_COOKIE[$key][$key2][$key3] = stripslashes($value3);
								}
							}
						}
						else {
						    $_COOKIE[$key][$key2] = stripslashes($value2);
						}
					}
				}
				else {
				    $_COOKIE[$key] = stripslashes($value);
				}
			}
		}
	}

	/**
	 * Make the input safe, same as in Security.class.php
	 * @param string  $input - The input text
	 * @param boolean $html  - Whether to use htmlentities() or not
	 * @return clean string
	 */
	function make_safe($input, $html = true) {
		/**
		 * Loops through to a certain depth and uses the addslashes()
		 * or htmlentities() functions to make it safe.
		 */
		if (is_array($input)) {
			foreach ($input as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key2 => $value2) {
						if (is_array($value2)) {
							foreach ($value2 as $key3 => $value3) {
								if (is_array($value3)) {
									foreach ($value3 as $key4 => $value4) {
										// This is as far as we go
										if (is_array($value4)) {
									    	$input[$key][$key2][$key3][$key4] = $value4;
										}
										else {
											if ($html === false) {
											    $input[$key][$key2][$key3][$key4] = addslashes($value4);
											}
											else {
											    $input[$key][$key2][$key3][$key4] = htmlentities($value4, ENT_QUOTES);
											}
										}
									}
								}
								else {
									if ($html === false) {
									    $input[$key][$key2][$key3] = addslashes($value3);
									}
									else {
									    $input[$key][$key2][$key3] = htmlentities($value3, ENT_QUOTES);
									}
								}
							}
						}
						else {
							if ($html === false) {
							    $input[$key][$key2] = addslashes($value2);
							}
							else {
							    $input[$key][$key2] = htmlentities($value2, ENT_QUOTES);
							}
						}
					}
				}
				else {
					if ($html === false) {
				    	$input[$key] = addslashes($value);
					}
					else {
					    $input[$key] = htmlentities($value, ENT_QUOTES);
					}
				}
			}

		    return $input;
		}
		else {
			if ($html === false) {
			    return addslashes($input);
			}
			else {
			    return htmlentities($input, ENT_QUOTES);
			}
		}
	}

	/**
	 * Installs the system
	 *	@return true on success, false on failure
	 */
	function install_system() {
        // Get the user browser information
        $browser = strtolower($_SERVER['HTTP_USER_AGENT']);
        $another_mime = (strpos($browser, 'msie') === true || strpos($browser, 'opera') === true) ? 'application/octetstream' : 'application/octet-stream';
        $errors = null;

        // Check all the input data
        $database_prefix = (preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]{0,254}$/", $_POST['database_prefix']) || $_POST['database_prefix'] == '') ? $this->make_safe($_POST['database_prefix']) : false;
        $database_type = (isset($_POST['database_type'])) ? $_POST['database_type'] : false;
        $database_server_name = (isset($_POST['database_server_name'])) ? $_POST['database_server_name'] : false;
        $database_username = (isset($_POST['database_username'])) ? $_POST['database_username'] : false;
        $database_password = (isset($_POST['database_password'])) ? $_POST['database_password'] : false;
        $database_name = (isset($_POST['database_name'])) ? $_POST['database_name'] : false;
        $database_port = (isset($_POST['database_port']) && is_numeric($_POST['database_port']) && $_POST['database_port'] > 0) ? $_POST['database_port'] : false;
        $cookie_prefix = (preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]{0,254}$/", $_POST['cookie_prefix']) || $_POST['cookie_prefix'] == '') ? $this->make_safe($_POST['cookie_prefix'], false) : false;
        $max_username = (is_numeric($_POST['max_username']) && strlen($_POST['max_username']) < 3 && strlen($_POST['max_username']) > 0) ? $this->make_safe($_POST['max_username']) : false;
        $min_username = (is_numeric($_POST['min_username']) && strlen($_POST['min_username']) < 3 && strlen($_POST['min_username']) > 0) ? $this->make_safe($_POST['min_username']) : false;
        $max_password = (is_numeric($_POST['max_password']) && strlen($_POST['max_password']) < 3 && strlen($_POST['max_password']) > 0) ? $this->make_safe($_POST['max_password']) : false;
        $min_password = (is_numeric($_POST['min_password']) && strlen($_POST['min_password']) < 3 && strlen($_POST['min_password']) > 0) ? $this->make_safe($_POST['min_password']) : false;
        $cookie_path = (preg_match('/^\/.*?$/', $_POST['cookie_path'])) ? $this->make_safe($_POST['cookie_path']) : false;
        $cookie_secure = ($_POST['cookie_secure'] == 0 || $_POST['cookie_secure'] == 1) ? $this->make_safe($_POST['cookie_secure']) : false;
        $cookie_length = (is_numeric($_POST['cookie_length']) && strlen($_POST['cookie_length']) < 8 && strlen($_POST['cookie_length']) > 0) ? $this->make_safe($_POST['cookie_length']) : false;
        $cookie_domain = (isset($_POST['cookie_domain'])) ? $this->make_safe($_POST['cookie_domain']) : false;
        $max_tries = (is_numeric($_POST['max_tries']) && strlen($_POST['max_tries']) < 3 && strlen($_POST['max_tries']) > 0) ? $this->make_safe($_POST['max_tries']) : false;
        $user_regex = (isset($_POST['user_regex']) && strlen($_POST['user_regex']) <= 255) ? $this->make_safe($_POST['user_regex'], false) : false;
        $security_image = ($_POST['security_image'] == 'yes' || $_POST['security_image'] == 'no') ? $this->make_safe($_POST['security_image']) : false;
        $max_upload_size = (isset($_POST['max_upload_size']) && $_POST['max_upload_size'] > -1) ? $this->make_safe($_POST['max_upload_size']) : '1048576';
        $auth_registration = ($_POST['auth_registration'] == 1 || $_POST['auth_registration'] == 0) ? $this->make_safe($_POST['auth_registration']) : '1';
        $activation_type = ($_POST['activation_type'] == 0 || $_POST['activation_type'] == 1 || $_POST['activation_type'] == 2) ? $this->make_safe($_POST['activation_type']) : false;
        $login_redirect = (isset($_POST['login_redirect']) && strlen($_POST['login_redirect']) <= 255 && strlen($_POST['login_redirect']) > 0) ? $this->make_safe($_POST['login_redirect'], false) : false;
        $logout_redirect = (isset($_POST['logout_redirect']) && strlen($_POST['logout_redirect']) <= 255 && strlen($_POST['logout_redirect']) > 0) ? $this->make_safe($_POST['logout_redirect'], false) : false;
        $default_group_name = (isset($_POST['default_group_name']) && strlen($_POST['default_group_name']) > 0 && strlen($_POST['default_group_name']) <= 255) ? $this->make_safe($_POST['default_group_name']) : 'Default';
        $default_mask_name = (isset($_POST['default_mask_name']) && strlen($_POST['default_mask_name']) > 0 && strlen($_POST['default_mask_name']) <= 255) ? $this->make_safe($_POST['default_mask_name']) : 'Default';
        $redirect_type = ($_POST['redirect_type'] == '1' || $_POST['redirect_type'] == '2' || $_POST['redirect_type'] == '3') ? $this->make_safe($_POST['redirect_type']) : '1';
        $online_users_format = (isset($_POST['online_users_format']) && strlen($_POST['online_users_format']) <= 255) ? $this->make_safe($_POST['online_users_format'], false) : '{username}';
        $online_users_separator = (isset($_POST['online_users_separator']) && strlen($_POST['online_users_separator']) <= 255) ? $this->make_safe($_POST['online_users_separator'], false) : ',';
        $username = (isset($_POST['username']) && preg_match($_POST['user_regex'], $_POST['username']) && strlen($_POST['username']) <= $max_username && strlen($_POST['username']) >= $min_username) ? $this->make_safe($_POST['username']) : false;
        $password = (isset($_POST['password']) && strlen($_POST['password']) >= $min_password && strlen($_POST['password']) <= $max_password) ? $this->make_safe($_POST['password']) : false;
        $password_confirm = (isset($_POST['password_confirm']) && $password == $this->make_safe($_POST['password_confirm'])) ? true : false;
        $email = (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && strlen($_POST['email']) <= 255) ? $_POST['email'] : false;
        $email_confirm = (isset($_POST['email_confirm']) && $email == $_POST['email_confirm']) ? true : false;

		// Did they fail? If so add to the $errors variable
		if ($database_prefix === false) {
		    $errors[] = 'The database prefix you entered was not a valid format.';
		}

		if ($cookie_prefix === false) {
		    $errors[] = 'The cookie prefix you entered was not valid.';
		}

		if ($max_username === false) {
	    	$errors[] = 'The max. username length you entered was not valid.';
		}

		if ($min_username === false) {
		    $errors[] = 'The min. username length you entered was not valid.';
		}

		if ($max_password === false) {
	    	$errors[] = 'The max. password length you entered was not valid.';
		}

		if ($min_password === false) {
		    $errors[] = 'The min. password length you entered was not valid.';
		}

		if ($cookie_path === false) {
		    $errors[] = 'The cookie path you specified was not a valid format.';
		}

		if ($cookie_secure === false) {
		    $errors[] = 'The cookie secure choice you selected was not a valid format.';
		}

		if ($cookie_length === false) {
		    $errors[] = 'The cookie length you specified was not valid.';
		}

		if ($cookie_domain === false) {
		    $errors[] = 'The cookie domain you entered was not valid.';
		}

		if ($max_tries === false) {
		    $errors[] = 'The max. login tries you entered was not valid.';
		}

		if ($user_regex === false) {
		    $errors[] = 'The user regex you entered was not valid.';
		}

		if ($security_image === false) {
		    $errors[] = 'The security image choice was not a valid format.';
		}

		if ($activation_type === false) {
		    $errors[] = 'The activation type you entered was not valid.';
		}

		if ($login_redirect === false) {
		    $errors[] = 'The login redirect URL you specified was not valid.';
		}

		if ($logout_redirect === false) {
		    $errors[] = 'The logout redirect URL you specified was not valid.';
		}

		if ($username === false) {
		    $errors[] = 'The username you entered was not valid according to the user regex and lengths you specified.';
		}

		if ($password === false || $password_confirm === false) {
		    $errors[] = 'Either the password you entered was not valid, or the two passwords did not match.';
		}

		if ($email === false || $email_confirm === false) {
		    $errors[] = 'Either the email address you entered was not valid, or the two emails did not match.';
		}

		// Do we have some errors?
		if ($errors !== null) {
            // Make sure the values are saved
            $_SESSION['database_prefix'] = stripslashes($database_prefix);
            $_SESSION['cookie_prefix'] = stripslashes($cookie_prefix);
            $_SESSION['max_username'] = stripslashes($max_username);
            $_SESSION['min_username'] = stripslashes($min_username);
            $_SESSION['max_password'] = stripslashes($max_password);
            $_SESSION['min_password'] = stripslashes($min_password);
            $_SESSION['cookie_path'] = stripslashes($cookie_path);
            $_SESSION['cookie_secure'] = stripslashes($cookie_secure);
            $_SESSION['cookie_length'] = stripslashes($cookie_length);
            $_SESSION['cookie_domain'] = stripslashes($cookie_domain);
            $_SESSION['max_tries'] = stripslashes($max_tries);
            $_SESSION['user_regex'] = stripslashes($user_regex);
            $_SESSION['security_image'] = stripslashes($security_image);
            $_SESSION['max_upload_size'] = stripslashes($max_upload_size);
            $_SESSION['auth_registration'] = stripslashes($auth_registration);
            $_SESSION['activation_type'] = stripslashes($activation_type);
            $_SESSION['login_redirect'] = stripslashes($login_redirect);
            $_SESSION['logout_redirect'] = stripslashes($logout_redirect);
            $_SESSION['default_group_name'] = html_entity_decode(stripslashes($default_group_name));
            $_SESSION['default_mask_name'] = html_entity_decode(stripslashes($default_mask_name));
            $_SESSION['redirect_type'] = stripslashes($redirect_type);
            $_SESSION['online_users_format'] = stripslashes($online_users_format);
            $_SESSION['online_users_separator'] = stripslashes($online_users_separator);
            $_SESSION['username'] = stripslashes($username);
            $_SESSION['password'] = stripslashes($password);
            $_SESSION['password_confirm'] = stripslashes($password_confirm);
            $_SESSION['email'] = stripslashes($email);
            $_SESSION['email_confirm'] = stripslashes($email_confirm);
            $error_count = count($errors);

            // Create the HTML and return false
            $this->install_error = 'The following errors occured while trying to process the information you entered:<br /><ul>';

			for ($x = 0; $x < $error_count; $x++) {
			    $this->install_error .= "<li>{$errors[$x]}</li>";
			}

            $this->install_error .= '</ul><br /><br />Please <a href="install.php">go back</a> and try again.';
            return false;
		}
		else {
            // Get the Test class
            require_once('Test.class.php');
            $this->test = new Test($database_server_name,
                $database_username,
                $database_password,
                $database_name,
                $database_port,
                $database_type
            );

            // Test the connection and create necessary tables
            $this->test->test_connection();
            $this->test->create_system_tables($database_prefix);

            // Code generation
            $c_hash[] = md5($username . $password . md5($email));
            $c_hash[] = sha1($c_hash[0] . $c_hash[0]) . md5(sha1(sha1($email) . sha1($password)) . md5($username));
            $c_hash[] = sha1(sha1(sha1(sha1(md5(md5('   	') . sha1(' 	'))) . sha1($password . $username))));
            $c_hash[] = sha1($c_hash[0] . $c_hash[1] . $c_hash[2]) . sha1($c_hash[2] . $c_hash[0] . $c_hash[1]);
            $c_hash[] = sha1($username);
            $c_hash[] = sha1($password);
            $c_hash[] = md5(md5($email) . md5($password));
            $hash_count = count($c_hash);

			for ($x = 0; $x < $hash_count; $x++) {
			    $random_hash = rand(0, $hash_count);
			    $c_hash[] = sha1($c_hash[$x]) . sha1($password) . sha1($c_hash[$random_hash] . $username);
			}

            $user_code = sha1(sha1($c_hash[0] . $c_hash[1] . $c_hash[2] . $c_hash[3]) . sha1($c_hash[4] . $c_hash[5]) . md5($c_hash[6] . $c_hash[7] . $c_hash[8] . sha1($c_hash[9])) . $password . $email);

            // Password generation
            $hash[] = md5($password);
            $hash[] = md5($password . $user_code);
            $hash[] = md5($password) . sha1($user_code . $password) . md5(md5($password));
            $hash[] = sha1($password . $user_code . $password);
            $hash[] = md5($hash[3] . $hash[0] . $hash[1] . $hash[2] . sha1($hash[3] . $hash[2]));
            $hash[] = sha1($hash[0] . $hash[1] . $hash[2] . $hash[3]) . md5($hash[4] . $hash[4]) . sha1($user_code);
            $final_hash = sha1($hash[0] . $hash[1] . $hash[2] . $hash[3] . $hash[4] . $hash[5] . md5($user_code));

			// The permission mask for an Administrator
			if (!$this->test->query("INSERT INTO `{$database_prefix}masks` (`name`,`auth_admin`,`auth_admin_phpinfo`,`auth_admin_configuration`,`auth_admin_add_user`,`auth_admin_user_list`,`auth_admin_remove_user`,`auth_admin_edit_user`,`auth_admin_add_page`,`auth_admin_page_list`,`auth_admin_remove_page`,`auth_admin_edit_page`,`auth_admin_page_stats`,`auth_admin_add_mask`,`auth_admin_list_masks`,`auth_admin_remove_mask`,`auth_admin_edit_mask`,`auth_admin_add_group`,`auth_admin_list_groups`,`auth_admin_remove_group`,`auth_admin_edit_group`, `auth_admin_activate_account`,`auth_admin_send_invite`,`auth_356a192b7913b04c54574d18c28d46e6395428ab`) VALUES('Admin',1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1)")) {
		    	$this->test->output_error();
			}

			// The Default mask for new users
			if (!$this->test->query("INSERT INTO `{$database_prefix}masks` (`name`,`auth_admin`,`auth_admin_phpinfo`,`auth_admin_configuration`,`auth_admin_add_user`,`auth_admin_user_list`,`auth_admin_remove_user`,`auth_admin_edit_user`,`auth_admin_add_page`,`auth_admin_page_list`,`auth_admin_remove_page`,`auth_admin_edit_page`,`auth_admin_page_stats`,`auth_admin_add_mask`,`auth_admin_list_masks`,`auth_admin_remove_mask`,`auth_admin_edit_mask`,`auth_admin_add_group`,`auth_admin_list_groups`,`auth_admin_remove_group`,`auth_admin_edit_group`, `auth_admin_activate_account`,`auth_admin_send_invite`,`auth_356a192b7913b04c54574d18c28d46e6395428ab`) VALUES('{$default_mask_name}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1)")) {
		    	$this->test->output_error();
			}

			// The group for administrators
			if (!$this->test->query("INSERT INTO `{$database_prefix}groups` (`name`,`mask_id`,`is_public`,`leader`) VALUES('Admin',1,0,1)")) {
			    $this->test->output_error();
			}

			// The group for new users
			if (!$this->test->query("INSERT INTO `{$database_prefix}groups` (`name`,`mask_id`,`is_public`,`leader`) VALUES('{$default_group_name}',2,1,1)")) {
			    $this->test->output_error();
			}

			// The default member page
			if (!$this->test->query("INSERT INTO `{$database_prefix}pages` (`name`,`hits`) VALUES('members.php',0)")) {
			    $this->test->output_error();
			}

			// Add administrator
			if (!$this->test->query("INSERT INTO `{$database_prefix}users` (`username`,`password`,`code`,`active`,`last_login`,`last_session`,`blocked`,`tries`,`last_try`,`email`,`mask_id`,`group_id`) VALUES('{$username}','{$final_hash}','{$user_code}','yes','0','0','no','0','0','{$email}',1,1)")) {
			    $this->test->output_error();
			}

            // Configuration information to be inserted
            $sql_start = "INSERT INTO `{$database_prefix}config` (`name`,`value`) VALUES(";
            $sql_end = ")";
            $sql[] = "'cookie_prefix','{$cookie_prefix}'";
            $sql[] = "'max_username','{$max_username}'";
            $sql[] = "'min_username','{$min_username}'";
            $sql[] = "'max_password','{$max_password}'";
            $sql[] = "'min_password','{$min_password}'";
            $sql[] = "'cookie_path','{$cookie_path}'";
            $sql[] = "'cookie_secure','{$cookie_secure}'";
            $sql[] = "'cookie_length','{$cookie_length}'";
            $sql[] = "'cookie_domain','{$cookie_domain}'";
            $sql[] = "'max_tries','{$max_tries}'";
            $sql[] = "'user_regex','{$user_regex}'";
            $sql[] = "'security_image','{$security_image}'";
            $sql[] = "'activation_type','{$activation_type}'";
            $sql[] = "'login_redirect','{$login_redirect}'";
            $sql[] = "'logout_redirect','{$logout_redirect}'";
            $sql[] = "'max_upload_size','{$max_upload_size}'";
            $sql[] = "'auth_registration','{$auth_registration}'";
            $sql[] = "'current_version','{$this->system_version}'";
            $sql[] = "'redirect_type','{$redirect_type}'";
            $sql[] = "'online_users_format','{$online_users_format}'";
            $sql[] = "'online_users_separator','{$online_users_separator}'";
            $sql_count = count($sql);

            // Insert the config data
            $this->test->query('BEGIN');

			for ($x = 0; $x < $sql_count; $x++) {
				if (!$this->test->query($sql_start . $sql[$x] . $sql_end)) {
                    $this->test->query('ROLLBACK');
                    $this->test->output_error();
                    return false;
				}
			}

		    $this->test->query('COMMIT');

            // Make sure the port shows up false if it's false
            $database_port = ($database_port === false) ? 'false' : $database_port;

            // We don't need these anymore
            unset($_SESSION['database_prefix'],
                $_SESSION['cookie_prefix'],
                $_SESSION['max_username'],
                $_SESSION['min_username'],
                $_SESSION['max_password'],
                $_SESSION['min_password'],
                $_SESSION['cookie_path'],
                $_SESSION['cookie_secure'],
                $_SESSION['cookie_length'],
                $_SESSION['cookie_domain'],
                $_SESSION['max_tries'],
                $_SESSION['user_regex'],
                $_SESSION['security_image'],
                $_SESSION['max_upload_size'],
                $_SESSION['auth_registration'],
                $_SESSION['activation_type'],
                $_SESSION['login_redirect'],
                $_SESSION['logout_redirect'],
                $_SESSION['default_group_name'],
                $_SESSION['default_mask_name'],
                $_SESSION['redirect_type'],
                $_SESSION['online_users_format'],
                $_SESSION['online_users_separator'],
                $_SESSION['username'],
                $_SESSION['password'],
                $_SESSION['password_confirm'],
                $_SESSION['email'],
                $_SESSION['email_confirm']
            );

            // The database_info.php file
            $time = date('F jS, Y', time());
            $database_info = <<<DATABASE_INFO
<?php
/*** *** *** *** *** ***
* @package   Quadodo Login Script
* @file      database_info.php
* @author    Douglas Rennehan
* @generated {$time}
* @link      http://www.quadodo.net
*** *** *** *** *** ***
* Comments are always before the code they are commenting
*** *** *** *** *** ***/
if (!defined('QUADODO_IN_SYSTEM')) {
exit;
}

define('SYSTEM_INSTALLED', true);
\$database_prefix = '{$database_prefix}';
\$database_type = '{$database_type}';
\$database_server_name = '{$database_server_name}';
\$database_username = '{$database_username}';
\$database_password = '{$database_password}';
\$database_name = '{$database_name}';
\$database_port = {$database_port};

/**
 * Use persistent connections?
 * Change to true if you have a high load
 * on your server, but it's not really needed.
 */
\$database_persistent = false;
?>
DATABASE_INFO;

			if (is_writable($this->main_directory . '/includes')) {
				if ($file_handle = fopen($this->main_directory . '/includes/database_info.php', 'w')) {
                    fwrite($file_handle, $database_info);
                    fclose($file_handle);
                    die('You have successfully installed the system! Please move/rename/remove this directory and then you can access all of your pages!');
				}
				else {
                    // Prepare for download, then send the information
                    header('Content-Type: application/x-php');
                    header("Content-Type: {$another_mime}");
                    header('Content-Disposition: attachment; filename=database_info.php');
                    header('Content-Length: ' . strlen($database_info));

					if (strpos($browser, 'msie 6.0') === true) {
					    header('Expires: -1');
					}
					else {
					    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 3144900));
					}

                    // Print out the database_info.php file
                    echo $database_info;
                    exit;
				}
			}
			else {
                // Prepare for download, then send the information
                header('Content-Type: application/x-php');
                header("Content-Type: {$another_mime}");
                header('Content-Disposition: attachment; filename=database_info.php');
                header('Content-Length: ' . strlen($database_info));

				if (strpos($browser, 'msie 6.0') === true) {
				    header('Expires: -1');
				}
				else {
				    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 3144900));
				}

                // Print out the database_info.php file
                echo $database_info;
                exit;
			}
		}
	}
}