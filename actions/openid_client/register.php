<?php
/**
 * Register OpenID user action
 */

elgg_set_context('openid_client');

$username = get_input('username');
$name = get_input('name');
$email = get_input('email');
$openid_identifier = get_input('openid_identifier');

$password = generate_random_cleartext_password();

try {
	$guid = register_user($username, $password, $name, $email, false);
} catch (RegistrationException $e) {
	register_error($e->getMessage());
	forward(REFERER);
}
$user = get_entity($guid);
openid_client_set_subtype($user);

$user->annotate('openid_identifier', $openid_identifier, ACCESS_PUBLIC);
elgg_set_user_validation_status($guid, true, 'openid');

if (!elgg_trigger_plugin_hook('register', 'user', array('user' => $user), true)) {
	$user->delete();
	register_error(elgg_echo('registerbad'));
	forward(REFERER);
}

login($user);
system_message(elgg_echo('openid_client:success:register'));
forward();
