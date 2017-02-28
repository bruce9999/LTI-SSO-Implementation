<?php
include_once('AbstractModel.php');
class lti_login_model extends AbstractModel {

	public function __construct() {
        parent::__construct();
        
        //These are OAuth Libraries downloaded from Google
        $this->load->library('oauth/OAuthRequestVerifier');
        $this->load->library('oauth/OAuthRequest');

    }
    
    
     //before querying database, make sure there is at least a valid signature type
	 public function requestIsSigned() {
	 	if ($this->oauthrequestverifier->requestIsSigned()) {
	 		return true;
	 	}
	 	return false;
	 }
   
	 //verify the request by creating a signature based on the secret
	 public function verifySignature($secret, $token_secret = null, $token_type = false) {
	 	if ($this->oauthrequestverifier->verifySignature($secret, $token_secret, $token_type )) {
	 		return true;
	 	}
	 	return false;
	 }
    
	//See if there is a matching consumer record
	public function validateLtiAccess () {
		$oauth_consumer_key = $this->input->post('oauth_consumer_key');
		
		$result = $this->dbLms->select('consumer_secret, consumer_id')->from('lti_consumer')->
			where('consumer_key', $oauth_consumer_key)->
			get()->row_array();
		if ($result) {
			return $result;
		} else {
			return false;
		}
	}
	
	
	
	public function getLtiUserInfo($consumerId) {
	    //user ID is sent in via the POST
	    $user_id = $this->input->post('use_id');
	    //user INFO will come from the db once we know the POST info is valid
		$result = $this->dbLms->from('lti_user')->
		where('lti_user_id',  $user_id)->
		where('lti_consumer_id',  $consumerId)->
		get()->row_array();
		if (empty($result)) {
			return false;
		}
		return $result;
	}
	
	
