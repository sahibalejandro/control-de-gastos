<?php
class TwitterAPI
{
  private $_consumer_key    = null;
  private $_consumer_secret = null;
  private $_access_token    = null;
  
  public function __construct($consumer_key, $consumer_secret)
  {
    $this->_consumer_key = $consumer_key;
    $this->_consumer_secret = $consumer_secret;
  }

  /**
   * Get user's screen name before access token request.
   * @return string
   */
  public function getUserScreenName()
  {
    return $this->_access_token['screen_name'];
  }

  /**
   * Get user's ID before access token request.
   * @return string
   */
  public function getUserId()
  {
    return $this->_access_token['user_id'];
  }

  /**
   * Set the "access token" data recovered from accessToken() method, to use in
   * future requests that need authorization, like homeTimeline().
   * Call this before any method that needs authorization.
   * 
   * @param array $access_token Key/value array
   */
  public function setAccessToken($access_token)
  {
    $this->_access_token = $access_token;
  }

  /**
   * Returns the URL for authentication in your app.
   * 
   * @param  string $callback The URL callback for the authentication
   * @return string The URL for authentication
   */
  public function getAuthenticationURL($callback)
  {
    $request_token = $this->requestToken($callback);
    return 'https://api.twitter.com/oauth/authenticate?oauth_token='
      . $request_token['oauth_token'];
  }

  /**
   * Make a request to "POST 1/statuses/update" with the status $status, using
   * extra parameters $parameters if needed.
   * $oauth_token and $oauth_token_secret are obtained from accessToken() method.
   * 
   * To see a list of extra parameters please read:
   * https://dev.twitter.com/docs/api/1/post/statuses/update
   *
   * @param string $status Status to publish.
   * @param array $parameters Aditional POST parameters, or null.
   * @param string $oauth_token Token from accessToken().
   * @param string $oauth_token_secret Token secret from accessToken().
   * @param stdClass Result object.
   */
  public function tweet($status, $parameters)
  {
    if(!is_array($parameters)){
      $parameters = array();
    }
    $parameters['status'] = $status;
    $response_json = $this->request(
      'https://api.twitter.com/1/statuses/update.json'
      , 'POST'
      , array('oauth_token' => $this->_access_token['oauth_token'])
      , null
      , $parameters
      , $this->_access_token['oauth_token_secret']
    );
    return json_decode($response_json);
  }

  /**
   * Make a request to "POST oauth/request_token" for request tokens.
   * @param  string $callback URL callback
   * @return array Key/value array with elements:
   *               oauth_token, oauth_token_secret and oauth_callback_confirmed
   */
  public function requestToken($callback)
  {
    $response = $this->request('https://api.twitter.com/oauth/request_token'
      , 'POST'
      , array('oauth_callback' => $callback)
      , null // No GET parameters
      , null // No POST parameters
      , null // No token_secret
    );
    
    $this->searchForError($response);
    parse_str($response, $request);
    
    // Verify $request
    if (isset($request['oauth_token'])) {
      return $request;
    } else {
      throw new TwitterAPIException(
        __METHOD__ . " Can't get auth_token [".$response."]",
        TwitterAPIException::ERR_RESPONSE
      );
      return null;
    }
  }

  /**
   * Make a request to "POST oauth/access_token" for get access tokens.
   * 
   * @param  string $oauth_verifier oauth_verifier from twitter
   * @param  string $oauth_token    oauth_token from twitter
   * @return array Key/value array with elements:
   *               oauth_token, oauth_token_secret, user_id and screen_name
   */
  public function getAccessToken($oauth_verifier, $oauth_token)
  {
    $response = $this->request('https://api.twitter.com/oauth/access_token'
      , 'POST'
      , array('oauth_token' => $oauth_token)
      , null // No GET parameters needed.
      , array('oauth_verifier' => $oauth_verifier)
      , $oauth_token
    );

    parse_str($response, $request);
    return $request;
  }

  /**
   * Make a reques to $url using method $method (POST or GET), with the extra
   * parameters if needed, using de token secret $token_secret
   *
   * @param string $url URL to request, without get parameters.
   * @param string $method Request method, 'POST' or 'GET'.
   * @param array $oauth_parameters Key/value array with extra oauth_* parameters
   *                                or null if not needed.
   * @param array $get_parameters Key/value array with extra GET parameters to
   *                              be appened to de $url, or null if not needed.
   * @param array $post_parameters Key/value array with extra POST parameters to
   *                                be appened to the request body, or null if not
   *                                needed.
   * @param string $token_secret Token secret for the request, or null if not needed.
   *
   * @return string The raw response of the request.
   */
  public function request($url, $method, $oauth_parameters
    , $get_parameters, $post_parameters, $token_secret)
  {
    // Initialize paramters arrays if they're null
    if($oauth_parameters == null){
      $oauth_parameters = array();
    }
    if($get_parameters == null){
      $get_parameters = array();
    }
    if($post_parameters == null){
      $post_parameters = array();
    }

    // Make Authorization header
    $header = array(
      $this->_makeAuthHeader(
        $url
        , $method
        , $oauth_parameters
        , $get_parameters
        , $post_parameters
        , $token_secret)
    );

    // Make GET query string (if needed) for append to the URL
    if(empty($get_parameters)){
      $query_string = '';
    } else {
      $query_string = array();
      foreach($get_parameters as $key => $val){
        $query_string[] = $key.'='.rawurlencode($val);
      }
      $query_string = '?' . implode('&', $query_string);
    }


    // Start cURL session
    $ch = curl_init();

    // Config cURL session
    $options = array(
      CURLOPT_HTTPHEADER     => $header,
      CURLOPT_HEADER         => false,
      CURLOPT_URL            => $url . $query_string,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false
    );
    curl_setopt_array($ch, $options);
    
    if($method == 'POST'){
      // Make POST query string (if needed) for append to the request's body
      $post_parameters_string = '';
      if(!empty($post_parameters)){
        $post_parameters_string = array();
        foreach($post_parameters as $key => $val){
          $post_parameters_string[] = rawurlencode($key).'='.rawurlencode($val);
        }
        $post_parameters_string = implode('&', $post_parameters_string);
      }
      curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post_parameters_string
      ));
    }

    // Execute request
    $response = curl_exec($ch);
    
    // Close request
    curl_close($ch);

    // Check for error response, QuarkTwitter don't work with XML responses, in case
    // a response is an XML document we search for an error message.
    if( strpos($response, '<?xml') === false){
      // Return the raw respose of the request.
      return $response;
    } else {
      // The response is an XML, search for error message
      if( preg_match_all('/<error>(.*)<\/error>/', $response, $matches) !== false){
        $err_msg = $matches[1][0];
      } else {
        $err_msg = 'QuarkTwitter::request() Can\'t handle the response, see messages.log for details.';
        Quark::log('TWITTER RESPONSE: ', $response);
      }
      throw new TwitterAPIException($err_msg, TwitterAPIException::ERR_RESPONSE);
    }

  }

  /**
   * Make the Authorization string header for a request, using the arguments to
   * build the signature.
   *
   * @param string $url URL to request
   * @param string $method Request, generally 'POST' or 'GET'
   * @param array $oauth_parameters Key/value array with extra oauth_* parameters
   *                                or null if not needed.
   * @param array $get_parameters Key/value array with extra GET parameters to
   *                              be appened to de $url, or null if not needed.
   * @param array $post_parameters Key/value array with extra POST parameters to
   *                                be appened to the request body, or null if not
   *                                needed.
   * @param string $token_secret Token secret for the request, or null if not needed.
   *
   * @return string The authorization header
   */
  private function _makeAuthHeader($url, $method, $oauth_parameters
    , $get_parameters, $post_parameters, $token_secret)
  {
    // Merge all arguments in one array.
    $parameters = array_merge($oauth_parameters, $get_parameters, $post_parameters);
    
    // Add default oauth parameters
    $parameters['oauth_consumer_key']     = $this->_consumer_key;
    $parameters['oauth_nonce']            = md5(uniqid());
    $parameters['oauth_signature_method'] = 'HMAC-SHA1';
    $parameters['oauth_timestamp']        = time();
    $parameters['oauth_version']          = '1.0';

    // Parameters must be alphabetically ordered to build the parameters string
    ksort($parameters);

    // "rawurlencode" key an values of all parameters to build the parameters string
    $parameters_encoded = array();
    foreach($parameters as $key => $val){
      $parameters_encoded[] = rawurlencode($key) . '=' . rawurlencode($val);
    }

    // Build parameters string
    $parameters_string = implode('&', $parameters_encoded);

    // Signature string, in format: METHOD&url&parameters_string
    $signature_base_string = strtoupper($method)
      .'&'.rawurlencode($url)
      .'&'.rawurlencode($parameters_string);

    // Signing key, in format consumer_secret&token_secret
    // If the request is for an access token then token_secret is ignored.
    $signing_key = rawurlencode($this->_consumer_secret) . '&';
    if($token_secret != null){
      $signing_key .= rawurlencode($token_secret);
    }

    $signature = base64_encode(hash_hmac('sha1', $signature_base_string
      , $signing_key, true));

    // Add signature to oauth_parameters
    $parameters['oauth_signature'] = $signature;

    /*
     * Build Autorization header string, only with oauth_* fields
     */
    ksort($parameters);
    $auth_header_parameters = array();
    foreach($parameters as $key => $val){
      if( strpos($key, 'oauth_') === 0 ){
        $auth_header_parameters[] = rawurlencode($key)
          . '="' . rawurlencode($val). '"';
      }
    }

    // Return authorization header.
    $auth = 'Authorization: OAuth ' . implode(', ', $auth_header_parameters);
    return $auth;
  }
  
  /*
   * Protected methods
   ================================================================================*/

  /**
   * Search for an error in a JSONObject response, if error exists throw an excetion.
   * @param  Object $JSONObject JSONObject from a request
   * @throws TwitterAPIException If Error exists.
   */
  protected function searchForError($JSONObject)
  {
    if(isset($JSONObject->error)){
      throw new TwitterAPIException('ERROR: "'. $JSONObject->error. '" ON: "'. $JSONObject->request .'"', TwitterAPIException::ERR_REQUEST);
    }
  }
}
