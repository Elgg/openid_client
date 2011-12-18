<?php
/**
 * OpenID JavaScript
 */

?>

// OpenID toggle
elgg.register_hook_handler('init', 'system', function() {
	$(".openid-client-toggle").click(function(event) {
		$("#openid-client-login-form").slideDown();

		var provider_input = $("#openid-client-login-form input[name=openid_provider]");
		provider_input.attr('value', $(this).attr('rel'));

		event.preventDefault();
	});
});
