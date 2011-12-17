<?php
/**
 * OpenID client English language file
 */

$english = array(

	'openid_client:login:header' => 'Log in with',
	'openid_client:or:header' => 'or',
	'openid_client:login:instructs' => 'Login in with %s',
	'openid_client:provider:google' => 'Google',
	'openid_client:provider:yahoo' => 'Yahoo',
	'openid_client:provider:blogger' => 'Blogger',
	'openid_client:provider:wordpress' => 'Wordpress',

	'openid_client:create' => 'Create an account',
	'openid_client:create:instructs' => 'Your account has been approved. We just need you to confirm or set the below information.',

	'openid_client:success:register' => 'Your account has been created.',
	'openid_client:error:bad_register' => 'Unable to create an account. Please contact a site administrator.',
	'openid_client:error:bad_response' => 'Bad response from the OpenID server',
	'openid_client:warning:username_not_available' => 'The username %s is not available. Please pick another.',
	'openid_client:warning:username_valid' => 'The username %s is not valid as this site. Please pick another.',
	'openid_client:warning:email_not_available' => 'The email address %s is not available. Please pick another.',
);

add_translation('en', $english);
