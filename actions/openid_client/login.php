<?php
/**
 * OpenID client login action
 */

elgg_load_library('openid_consumer');

$store = new Auth_OpenID_FileStore('/tmp');

$consumer = new ElggOpenIDConsumer($store);
$consumer->setProvider(get_input('provider'));
$consumer->setReturnURL(elgg_get_site_url() . 'mod/openid_client/return.php');

$html = $consumer->requestAuthentication();
if ($html) {
	echo $html;
	exit;
} else {
	register_error('oops');
}
