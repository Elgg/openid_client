<?php
/**
 * OpenID client login choices
 */

echo '<h3 class="openid-client-login-or">' . elgg_echo('openid_client:or:header') . '</h3>';
echo '<h3>' . elgg_echo('openid_client:login:header') . '</h3>';

echo elgg_view_menu('openid_login', array(
	'class' => 'elgg-menu-hz',
	'sort_by' => 'priority',
));
