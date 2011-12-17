<?php
/**
 * OpenID register form body
 *
 * @uses $vars['openid_identifier']
 * @uses $vars['username']
 * @uses $vars['is_username_available']
 * @uses $vars['is_username_valid']
 * @uses $vars['email']
 * @uses $vars['is_email_available']
 * @uses $vars['name']
 */

$username_label = elgg_echo('username');
$username_warning = '';
if (!elgg_extract('is_username_available', $vars, true)) {
	$username_warning = elgg_echo('openid_client:warning:username_not_available', array($vars['username']));
	$username_warning = "($username_warning)";
} else if (!elgg_extract('is_username_valid', $vars, true)) {
	$username_warning = elgg_echo('openid_client:warning:username_valid', array($vars['username']));
	$username_warning = "($username_warning)";
}
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
$email_available = elgg_extract('is_email_available', $vars, true);
$email_warning = '';
if (!$email_available) {
	$email_warning = elgg_echo('openid_client:warning:email_not_available', array($vars['email']));
	$email_warning = "($email_warning)";
}
$email_input = elgg_view('input/email', array(
	'name' => 'email',
	'value' => $vars['email'],
	'disabled' => !$email_available,
));

$openid_input = elgg_view('input/hidden', array(
	'name' => 'openid_identifier',
	'value' => $vars['openid_identifier'],
));
$button = elgg_view('input/submit', array('value' => elgg_echo('save')));

echo <<<HTML
<div>
	<label>$username_label</label> $username_warning
	$username_input
</div>
<div>
	<label>$name_label</label>
	$name_input
</div>
<div>
	<label>$email_label</label> $email_warning
	$email_input
</div>
<div class="elgg-foot">
	$openid_input
	$button
</div>

HTML;
