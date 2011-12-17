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

	elgg_extend_view('core/account/login_box', 'openid_client/login');
	elgg_register_plugin_hook_handler('register', 'menu:openid_login', 'openid_client_setup_menu');
	
	$base = elgg_get_plugins_path() . 'openid_client/actions/openid_client';
	elgg_register_action('openid_client/login', "$base/login.php", 'public');
	elgg_register_action('openid_client/register', "$base/register.php", 'public');

	$base = elgg_get_plugins_path() . 'openid_client/lib';
	elgg_register_library('openid_client', "$base/helpers.php");

	elgg_register_event_handler('create', 'user', 'openid_client_set_subtype', 1);

	//elgg_register_page_handler('openid_client', 'openid_client_page_handler');
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
		'large' => array('google', 'yahoo'),
		'small' => array('blogger', 'wordpress'),
	);
	$items = elgg_trigger_plugin_hook('register', 'openid_login', null, $items);

	$priority = 100;
	foreach ($items as $type => $providers) {
		foreach ($providers as $provider) {
			$provider_name = elgg_echo("openid_client:provider:$provider");
			$menu[] = ElggMenuItem::factory(array(
				'name' => $provider,
				'text' => '<span></span>',
				'title' => elgg_echo('openid_client:login:instructs', array($provider_name)),
				'href' => "action/openid_client/login?provider=$provider",
				'is_action' => true,
				'section' => $type,
				'priority' => $priority,
			));
			$priority += 10;
		}
	}

	return $menu;
}

/**
 * OpenID client page handler
 *
 * @param type $page Array of URL segments
 * @return bool
 */
function openid_client_page_handler($page) {

	// this is test code for right now
	elgg_load_library('openid_client');
	openid_client_registration_page_handler(array(
		'username' => 'john',
		'email' => 'john@example.org',
		'name' => 'John Doe',
		'openid_identifier' => 'abcdefghijklmnopqrstuvwxyz',
	));

	return true;
}
