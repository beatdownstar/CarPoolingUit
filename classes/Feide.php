<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 23.04.2019
 * Time: 21:20
 */

class Feide {

    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $auth;
    private $token_uri;
    private $responseType;

    function __construct($client_id, $client_secret, $redirect_uri, $auth, $token_uri, $responseType) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
        $this->auth = $auth;
        $this->token_uri = $token_uri;
        $this->responseType = $responseType;
    }

    function getClientId() {
        return $this->client_id;
    }

    function getClientSecret(){
        return $this->client_secret;
    }

    function getRedirectUri() {
        return $this->redirect_uri;
    }

    function getAuth() {
        return $this->auth;
    }

    function getToken_uri() {
        return $this->token_uri;
    }

    function getResponseType() {
        return $this->responseType;
    }

    function checkState($state) {
        if($state != $_SESSION['state']){
            include('error.php');
            die();
        }
    }

    function getAccessToken($code) {
        $params = array(
            'grant_type' 	=> 'authorization_code',
            'client_id'  	=> $this->client_id,
            'client_secret' => $this->client_secret,
            'code' 			=> $code,
            'redirect_uri'  => $this->redirect_uri,
        );

        $result_obj = $this->getResults($params, $this->token_uri, 'POST', NULL);
        $access_token = $result_obj['access_token'];

        return $access_token;
    }

    function getUserData($accessToken){
        $params = array(
            'access_token' => $accessToken,
        );

        $userInfo = $this->getResults($params, 'https://auth.dataporten.no/userinfo', 'GET', $accessToken);
        $extendedUserInfo = $this->getResults($params, 'https://api.dataporten.no/userinfo/v1/userinfo', 'GET', $accessToken);

        $userInfo['extendedUserInfo']['givenName'] = implode("",$extendedUserInfo['givenName']);
        $userInfo['extendedUserInfo']['lastName'] = implode("",$extendedUserInfo['sn']);
        // $userInfo['extendedUserInfo']['affiliation'] = implode("",$extendedUserInfo['eduPersonPrimaryAffiliation']);

        return $userInfo;
    }

    function getResults($params, $url, $method, $token){
        $options = array(
            'http' => array(
                'header' => array(
                    'Content-type: application/x-www-form-urlencoded',
                    (isset($token)) ? ('Authorization: Bearer ' . $token) : NULL
                ),
                'method'  => $method,
                'content' => http_build_query($params)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result_obj = json_decode($result, true);

        return $result_obj;
    }

}