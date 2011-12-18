<?php
/**
 * OpenID client login action
 */

elgg_load_library('openid_consumer');

$provider = get_input('openid_provider');
$username = get_input('openid_username');

$consumer = new ElggOpenIDConsumer($store);
$consumer->setProvider($provider);
$consumer->setUsername($username);
$consumer->setReturnURL(elgg_get_site_url() . 'mod/openid_client/return.php');

$html = $consumer->requestAuthentication();
if ($html) {
	echo $html;
	exit;
} else {
	$provider_name = elgg_echo("openid_client:provider:$provider");
	register_error(elgg_echo('openid_client:error:no_html', array($provider_name)));
	forward();
}
