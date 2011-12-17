<?php
/**
 * OpenID client login choices
 */

echo elgg_view('output/url', array(
	'text' => 'login with Google',
	'href' => 'action/openid_client/login',
	'is_action' => true,
));
