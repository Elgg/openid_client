<?php
/**
 * Callback for return_to url redirection.
 * 
 * The identity server will redirect back to this handler with the results of
 * the authentication attempt.
 * 
 * Note: the Janrain OpenID library is incompatible with Elgg's routing so
 * this script needs to be directly accessed.
 */

require_once dirname(dirname(dirname(__FILE__))).'/engine/start.php';

elgg_load_library('openid_consumer');
elgg_load_library('openid_client');

// get user data from the response
$consumer = new ElggOpenIDConsumer($store);
$url = elgg_get_site_url() . 'mod/openid_client/return.php';
$consumer->setReturnURL($url);
$data = $consumer->completeAuthentication();
if (!$data || !$data['openid_identifier']) {
	register_error(elgg_echo('openid_client:error:bad_response'));
	forward();
}

// is there an account already associated with this openid
$user = null;
$users = elgg_get_entities_from_annotations(array(
	'type' => 'user',
	'annotation_name' => 'openid_identifier',
	'annotation_value' => $data['openid_identifier'],
));
if ($users) {
	// there should only be one account
	$user = $users[0];
} else {
	$email = elgg_extract('email', $data);
	if ($email) {
		$users = get_user_by_email($email);
		if (count($users) === 1) {
			$user = $users[0];
			$user->annotate('openid_identifier', $data['openid_identifier'], ACCESS_PUBLIC);
		}
	}
}

if ($user) {
	// log in user and maybe update account (admin setting, user prompt?)
	try {
		login($user);
	} catch (LoginException $e) {
		register_error($e->getMessage());
		forward();
	}

	system_message(elgg_echo('loginok'));
	forward();
} else {
	// register the new user
	$result = openid_client_registration_page_handler($data);
	if (!$result) {
		register_error(elgg_echo('openid_client:error:bad_register'));
		forward();
	}
}
