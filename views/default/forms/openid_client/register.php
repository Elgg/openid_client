<?php
/**
 * OpenID register form body
 *
 * @uses $vars['openid_identifier']
 * @uses $vars['username']
 * @uses $vars['is_username_available']
 * @uses $vars['is_username_valid']
 * @uses $vars['email']
 * @uses $vars['name']
 */

$username_label = elgg_echo('username');
$username_input = elgg_view('input/text', array(
	'name' => 'username',
	'value' => $vars['username'],
));

$name_label = elgg_echo('name');
$name_input = elgg_view('input/text', array(
	'name' => 'name',
	'value' => $vars['name'],
));

$email_label = elgg_echo('email');
$email_input = elgg_view('input/email', array(
	'name' => 'email',
	'value' => $vars['email'],
));

$openid_input = elgg_view('input/hidden', array(
	'name' => 'openid_identifier',
	'value' => $vars['openid_identifier'],
));
$button = elgg_view('input/submit', array('value' => elgg_echo('save')));

echo <<<HTML
<div>
	<label>$username_label</label>
	$username_input
</div>
<div>
	<label>$name_label</label>
	$name_input
</div>
<div>
	<label>$email_label</label>
	$email_input
</div>
<div class="elgg-foot">
	$openid_input
	$button
</div>

HTML;
