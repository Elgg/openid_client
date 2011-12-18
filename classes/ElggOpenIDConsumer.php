<?php
/**
 * Consumer for OpenID
 */

class ElggOpenIDConsumer {

	protected $provider;
	protected $username;
	protected $returnURL;

	protected $store;
	protected $consumer;
	protected $request;

	/**
	 * Constructor
	 *
	 * @param Auth_OpenID_OpenIDStore $store Optional persistence store
	 */
	public function __construct(Auth_OpenID_OpenIDStore $store = null) {
		if ($store) {
			$this->store = $store;
		} else {
			// use the default store
			$this->store = new OpenID_ElggStore();
		}
	}

	/**
	 * Set the name of the OpenID provider
	 * 
	 * @param string $provider
	 */
	public function setProvider($provider) {
		$this->provider = $provider;
	}

	/**
	 * Set the OpenID username
	 *
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * Set the return URL
	 *
	 * @param string $url The URL the OpenID provider returns the user to
	 */
	public function setReturnURL($url) {
		$this->returnURL = $url;
	}

	/**
	 * Send a request to the provider for authentication
	 *
	 * @return mixed HTMl form on success and false for failure
	 */
	public function requestAuthentication() {

		if (!$this->store) {
			return false;
		}

		$this->consumer = new Auth_OpenID_Consumer($this->store);
		if (!$this->consumer) {
			return false;
		}

		$url = $this->getProviderURL();
		if (!$url) {
			return false;
		}

		// discovers the identity server
		$this->request = $this->consumer->begin($url);
		if (!$this->request) {
			return false;
		}

		// request user information
		if (!$this->addAttributeRequests()) {
			return false;
		}

		// send browser for authentication
		return $this->getForm();
	}

	/**
	 * Complete the OpenID authentication by parsing the response
	 *
	 * This returns an array of key value pairs about the user.
	 * 
	 * @return array
	 */
	public function completeAuthentication() {

		if (!$this->store) {
			return false;
		}

		$this->consumer = new Auth_OpenID_Consumer($this->store);
		if (!$this->consumer) {
			return false;
		}

		$response = $this->consumer->complete($this->returnURL);
		switch ($response->status) {
			case Auth_OpenID_SUCCESS:
				$data = $this->getUserData($response);
				break;
			case Auth_OpenID_FAILURE:
			case Auth_OpenID_CANCEL:
				$data = array();
				break;
		}

		return $data;
	}

	/**
	 * Get the OpenID provider URL based on name
	 * 
	 * @return string
	 */
	protected function getProviderURL() {
		$url = null;
		$provider = $this->provider;
		$username = $this->username;
		switch ($provider) {
			case 'google':
				$url = 'https://www.google.com/accounts/o8/id';
				break;
			case 'yahoo':
				$url = 'https://me.yahoo.com/';
				break;
			case 'blogger':
				$url = "http://$username.blogspot.com/";
				break;
			case 'wordpress':
				// username is actually the blog name
				$url = "http://$username.wordpress.com/";
				break;
			case 'livejournal':
				$url = "http://$username.livejournal.com/";
				break;
			case 'aol':
				$url = "https://openid.aol.com/";
				break;
			case 'verisign':
				$url = "https://pip.verisignlabs.com/ ";
				break;
			case 'myopenid':
				$url = 'https://myopenid.com/';
				break;
			case 'myspace':
				$url = 'https://api.myspace.com/openid';
				break;
			default:
				$params = array(
					'provider' => $provider,
					'username' => $username,
				);
				$url = elgg_trigger_plugin_hook('set', 'openid_client:url', $params);
				break;
		}

		return $url;
	}

	/**
	 * Add attribute requests to the OpenID authentication request
	 * 
	 * @return bool
	 */
	protected function addAttributeRequests() {

		// Simple Registration
		$required = array();
		$optional = array('email', 'nickname', 'fullname', 'language');
		$sregRequest = Auth_OpenID_SRegRequest::build($required, $optional);
		if (!$sregRequest) {
			return false;
		}
		$this->request->addExtension($sregRequest);

		// Attribute Exchange
		$axRequest = new Auth_OpenID_AX_FetchRequest();
		$attributes[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email', 1, true, 'email');
		$attributes[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first', 1, true, 'firstname');
		$attributes[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last', 1, true, 'lastname');
		foreach ($attributes as $attribute) {
			$axRequest->add($attribute);
		}
		$this->request->addExtension($axRequest);

		return true;
	}

	/**
	 * Gets the form to send the user to the provider to authenticate
	 *
	 * This implements OpenID 2.0 by submitting a form through JavaScript against
	 * the provider. If JavaScript is not enabled, a plain html form with a
	 * continue button is displayed.
	 *
	 * This also supports OpenID 1.x but has not been tested as thoroughly.
	 *
	 * @return mixed
	 */
	protected function getForm() {
		if (!$this->request->shouldSendRedirect()) {
			// OpenID 2.0
			$html = $this->request->htmlMarkup(elgg_get_site_url(), $this->returnURL, false);
			return $html;
		} else {
			// OpenID 1.x
			$redirect_url = $this->request->redirectURL(elgg_get_site_url(), $this->returnURL);

			if (Auth_OpenID::isFailure($redirect_url)) {
				return false;
			} else {
				forward($redirect_url);
			}
		}
	}

	/**
	 * Get user data from the OpenID response
	 * 
	 * @param Auth_OpenID_ConsumerResponse $response
	 * @return array
	 */
	protected function getUserData($response) {
		if (!$response) {
			return array();
		}

		$sregResponse = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
		$sreg = $sregResponse->contents();

		$axResponse = Auth_OpenID_AX_FetchResponse::fromSuccessResponse($response);
		$ax = $axResponse->data;

		$data = $this->extractUserData($sreg, $ax);
		$data['openid_identifier'] = $response->getDisplayIdentifier();

		return $data;
	}

	/**
	 * Extract user data from the extensions in the response
	 *
	 * @param array $sreg Simple Registration data
	 * @param array $ax   Attribute Exchange data
	 * @return array
	 */
	protected function extractUserData($sreg, $ax) {
		$data = array();

		// email
		if (isset($sreg['email'])) {
			$data['email'] = $sreg['email'];
		}
		if (isset($ax['http://axschema.org/contact/email'])) {
			$data['email'] = $ax['http://axschema.org/contact/email'][0];
		}

		// display name
		if (isset($sreg['fullname'])) {
			$data['name'] = $sreg['fullname'];
		}
		if (isset($ax['http://axschema.org/namePerson/first'])) {
			$data['name'] = $ax['http://axschema.org/namePerson/first'][0];
		}
		if (isset($ax['http://axschema.org/namePerson/last'])) {
			$data['name'] .= ' ' . $ax['http://axschema.org/namePerson/last'][0];
			$data['name'] = trim($data['name']);
		}

		// username
		if (isset($sreg['nickname'])) {
			$data['username'] = $sreg['nickname'];
		}

		// language
		if (isset($sreg['language'])) {
			$languages = get_installed_translations();
			// @todo - find out format
		}

		return $data;
	}
}
