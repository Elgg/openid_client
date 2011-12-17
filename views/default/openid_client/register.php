<?php
/**
 * Registration content view for OpenID client
 *
 */

echo '<p>';
echo elgg_echo('openid_client:create:instructs');
echo '<p>';

echo elgg_view_form('openid_client/register', array(), $vars);
