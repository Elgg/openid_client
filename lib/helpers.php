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

	$title = 'register';

	$vars = openid_client_prepare_registration_vars($data);
	$content = elgg_view('openid_client/register', $vars);

	$body = elgg_view_layout('one_column', array('content' => $content));
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
		$vars['username'] = array_pop(explode('@', $data['email']));
	} else {
		$vars['username'] = null;
	}

	// is the username available
	$vars['is_username_available'] = true;

	// is the username valid
	try {
		$vars['is_username_valid'] = validate_username($vars['username']);
	} catch (RegistrationException $e) {
		$vars['is_username_valid'] = false;
	}

	// the rest
	$vars['email'] = elgg_extract('email', $data);
	$vars['name'] = elgg_extract('name', $data);

	return $vars;
}
