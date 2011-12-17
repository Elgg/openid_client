<?php
/**
 * Helper functions for the OpenID client plugin
 */

/**
 * Serves a page to the new user to determine account values
 *
 * This should only be called after validating the OpenID response.
 *
 * @param array $data Key value pairs extracted from the response
 * @return bool
 */
function openid_client_registration_page_handler(array $data) {

	if (!is_array($data)) {
		return false;
	}

	$title = elgg_echo('openid_client:create');

	$vars = openid_client_prepare_registration_vars($data);
	$content = elgg_view('openid_client/register', $vars);

	$params = array(
		'title' => $title,
		'content' => $content,
	);
	$body = elgg_view_layout('one_column', $params);
	echo elgg_view_page($title, $body);

	return true;
}

/**
 * Create the form vars for registration
 *
 * @param array $data
 * @return array
 */
function openid_client_prepare_registration_vars(array $data) {
	$vars = array();

	$vars['openid_identifier'] = $data['openid_identifier'];

	// username
	if (isset($data['username'])) {
		$vars['username'] = $data['username'];
	} else if (isset($data['email'])) {
		$vars['username'] = array_shift(explode('@', $data['email']));
	} else {
		$vars['username'] = null;
	}

	// is the username available
	if ($vars['username']) {
		$vars['is_username_available'] = openid_client_is_username_available($vars['username']);
	}

	// is the username valid
	try {
		$vars['is_username_valid'] = validate_username($vars['username']);
	} catch (RegistrationException $e) {
		$vars['is_username_valid'] = false;
	}

	// the rest
	$vars['email'] = elgg_extract('email', $data);
	$vars['name'] = elgg_extract('name', $data);

	if ($vars['email']) {
		$vars['is_email_available'] = openid_client_is_email_available($vars['email']);
	}

	return $vars;
}

/**
 * Is this username available?
 *
 * @param string $username The username
 * @return bool 
 */
function openid_client_is_username_available($username) {
	$db_prefix = elgg_get_config('dbprefix');
	$username = sanitize_string($username);

	$query = "SELECT count(*) AS total FROM {$db_prefix}users_entity WHERE username = '$username'";
	$result = get_data_row($query);
	if ($result->total == 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * Is this email address available?
 *
 * @param string $email Email address
 * @return bool
 */
function openid_client_is_email_available($email) {
	$db_prefix = elgg_get_config('dbprefix');
	$email = sanitize_string($email);

	$query = "SELECT count(*) AS total FROM {$db_prefix}users_entity WHERE email = '$email'";
	$result = get_data_row($query);
	if ($result->total == 0) {
		return true;
	} else {
		return false;
	}
}
