<?php
/**
 * Elgg OpenID client
 *
 * This is a rewrite of the OpenID client written by Kevin Jardine for
 * Curverider Ltd for Elgg 1.0-1.7.
 */

elgg_register_event_handler('init', 'system', 'openid_client_init');

/**
 * OpenID client initialization
 */
function openid_client_init() {
	elgg_extend_view('css/elgg', 'openid_client/css');
	elgg_extend_view('js/elgg', 'openid_client/js');

	elgg_extend_view('core/account/login_box', 'openid_client/login');
	elgg_register_plugin_hook_handler('register', 'menu:openid_login', 'openid_client_setup_menu');
	
	$base = elgg_get_plugins_path() . 'openid_client/actions/openid_client';
	elgg_register_action('openid_client/login', "$base/login.php", 'public');
	elgg_register_action('openid_client/register', "$base/register.php", 'public');

	$base = elgg_get_plugins_path() . 'openid_client/lib';
	elgg_register_library('openid_client', "$base/helpers.php");

	elgg_register_event_handler('create', 'user', 'openid_client_set_subtype', 1);

	// don't let OpenID users set their passwords
	elgg_register_event_handler('pagesetup', 'system', 'openid_client_remove_email');
}

/**
 * Set the correct subtype for OpenID users
 *
 * @param string   $event  Event name
 * @param string   $type   Object type
 * @param ElggUser $user   New user
 */
function openid_client_set_subtype($event, $type, $user) {
	$db_prefix = elgg_get_config('dbprefix');
	$guid = (int)$user->getGUID();
	$subtype_id = (int)add_subtype('user', 'openid');

	$query = "UPDATE {$db_prefix}entities SET subtype = $subtype_id WHERE guid = $guid";
	update_data($query);
}

/**
 * Register login options
 *
 * @param string $hook
 * @param string $type
 * @param array  $menu
 * @param array  $params
 * @return array
 */
function openid_client_setup_menu($hook, $type, $menu, $params) {

	$items = array(
		'large' => array(
			'google' => '',
			'yahoo' => '',
		),
		'small' => array(
			'blogger' => 'toggle',
			'wordpress' => 'toggle',
		),
	);
	$items = elgg_trigger_plugin_hook('register', 'openid_login', null, $items);

	$priority = 100;
	foreach ($items as $type => $providers) {
		foreach ($providers as $provider => $toggle) {
			$provider_name = elgg_echo("openid_client:provider:$provider");

			$options = array(
				'name' => $provider,
				'text' => '<span></span>',
				'title' => elgg_echo('openid_client:login:instructs', array($provider_name)),
				'href' => "action/openid_client/login?openid_provider=$provider",
				'is_action' => true,
				'section' => $type,
				'priority' => $priority,
			);

			if ($toggle) {
				$options['link_class'] = 'openid-client-toggle';
				$options['rel'] = $provider;
			}

			$menu[] = ElggMenuItem::factory($options);

			$priority += 10;
		}
	}

	return $menu;
}

/**
 * Remove the password view from the account settings form
 */
function openid_client_remove_email() {
	$page_owner = elgg_get_page_owner_entity();
	if ($page_owner && elgg_instanceof($page_owner, 'user', 'openid')) {
		elgg_unextend_view('forms/account/settings', 'core/settings/account/password');
	}
}
