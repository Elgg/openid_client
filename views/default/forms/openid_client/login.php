<?php
/**
 * OpenID login if username or full url required
 */

echo '<label>';
echo elgg_echo('username');
echo '</label>';
echo elgg_view('input/text', array('name' => 'openid_username', 'class' => 'mbs'));

echo elgg_view('input/hidden', array('name' => 'openid_provider'));

echo elgg_view('input/submit', array('value' => elgg_echo('submit')));