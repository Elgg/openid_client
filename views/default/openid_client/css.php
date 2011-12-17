<?php
/**
 * OpenID client CSS
 */

$site_url = elgg_get_site_url();

?>

.openid-client-login-or {
	margin-top: 5px;
	text-align: center;
	color: #333;
}

.elgg-menu-openid-login {
	text-align: center;
}

.elgg-menu-openid-login > li {
	margin: 3px;
}

.elgg-menu-openid-login span {
	display: block;
	background: url("<?php echo $site_url; ?>mod/openid_client/graphics/openid_providers.png") no-repeat left;
	border: 1px solid #ccc;
}

.elgg-menu-openid-login-large span {
	height: 32px;
	width: 92px;
}

.elgg-menu-openid-login-small span {
	height: 20px;
	width: 20px;
}

.elgg-menu-item-google span {
	background-position: -5px -14px;
}

.elgg-menu-openid-login-large > .elgg-menu-item-yahoo span {
	background-position: -105px -14px;
}

.elgg-menu-item-blogger span {
	background-position: -170px -62px;
}

.elgg-menu-item-wordpress span {
	background-position: -146px -62px;
}
