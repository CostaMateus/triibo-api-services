<?php
/**
 * The file that defines the Triibo Node API
 *
 * A class definition that includes attributes and functions used for access the Triibo Node API.
 *
 * @author 		Mateus Costa <mateus@costamateus.com.br>
 * @package 	Triibo_Api_Services/Classes/Api
 * @version 	1.0.0
 */
defined( "ABSPATH" ) || exit;

class Triibo_Api_Node extends Triibo_Api
{
	/**
	 * Endpoint for api /auth
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const AUTH           = "/auth";

	/**
	 * Endpoint for api /auth/marketplace
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const AUTH_MKT       = "/auth/marketplace";

	/**
	 * Endpoint for api /authcode
	 *
	 * @since 	1.2.0
	 * @var 	string
	 */
	const AUTH_CODE      = "/authCode";

	/**
	 * Endpoint for api /login
	 *
	 * @since 	1.5.0
	 * @var 	string
	 */
    const LOGIN          = "/login";

	/**
	 * Endpoint for api /payments/brand
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const PAY_BRAND      = "/payments/brand";

	/**
	 * Endpoint for api /payments/doPayment
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const PAYMENT        = "/payments/doPayment";

	/**
	 * Endpoint for api /payments/status
	 *
	 * @since 	1.1.4
	 * @var 	string
	 */
	const PAYMENT_STATUS = "/payments/status";

	/**
	 * Endpoint for api /payments/checkToken
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const PAY_TKN_CHECK  = "/payments/checkToken";

	/**
	 * Endpoint for api /payments/createToken
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const PAY_TKN_CREATE = "/payments/createToken";

	/**
	 * Endpoint for api /payments/deleteToken
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const PAY_TKN_DELETE = "/payments/deleteToken";

	/**
	 * Endpoint for api /sms/validateSMS
	 *
	 * @since 	1.5.0
	 * @var 	string
	 */
	const SMS_VALIDATE   = "/sms/validateSMS";

	/**
	 * Endpoint for api /users/findUIDsByPhones
	 *
	 * @since 	1.1.3
	 * @var 	string
	 */
	const USER_FIND_UID  = "/users/findUIDsByPhones";

	/**
	 * Internal ID for log
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $id          = "triibo-api-node";

	/**
	 * Status of this api
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $status      = null;

	/**
	 * Environment hml/prod
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $env         = null;

	/**
	 * Endpoint hml/prod of api
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $url_api     = null;

	/**
	 * Endpoint hml/prod of marketplace
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $url_mkt     = null;

	/**
	 * User hml/prod to make the api calls
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $user        = null;

	/**
	 * Password hml/prod of user
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $pass        = null;

	/**
	 * Header for api calls
	 *
	 * @since 	1.4.0
	 * @var 	array|null
	 */
    private $header      = null;

	/**
	 * Extra token hml/prod for /auth/marketplace api call
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $x_key       = null;

	/**
	 * Logger
	 *
	 * @since 	1.0.0
	 * @var 	wc_get_logger|WC_Logger
	 */
	private $log         = null;

    /**
     * Construct
     *
     * @since   1.0.0
     */
    public function __construct()
    {
		$this->log    = ( function_exists( "wc_get_logger" ) ) ? wc_get_logger() : new WC_Logger();

        $this->status = get_option( "triibo_api_services_node_status" );
		$this->env    = get_option( "triibo_api_services_node_env"    );

        if ( $this->status == "on" )
        {
			// $this->url_api = ( get_home_url() === "http://localhost:8000" )
			// 					? "http://localhost:3000"
			// 					: "https://api" . ( $this->env == "on" ? "-hml" : "" ) . ".triibo.com.br";

			$this->url_api = "https://api" . ( $this->env == "on" ? "-hml" : "" ) . ".triibo.com.br";

			$this->url_mkt = "https://" . ( $this->env == "on" ? "dev-" : "" ) . "marketplace.triibo.com.br";

			$this->user    = ( $this->env == "on" ) ? get_option( "triibo_api_services_node_hml_user" ) : get_option( "triibo_api_services_node_prd_user" );
			$this->pass    = ( $this->env == "on" ) ? get_option( "triibo_api_services_node_hml_pass" ) : get_option( "triibo_api_services_node_prd_pass" );

			$this->x_key   = ( $this->env == "on" ) ? "652e0505-f010-48a4-a9a5-ce12783777ea" : "xp8bqzil-b15e-da2v-3jcm-25pniix0y859";

			$this->header  = [
				"origin: {$this->url_mkt}",
				"content-type: application/json",
			];
        }
    }

    /**
     * Returns the node api status
     * TRUE  for active.
     * FALSE for inactive.
     *
     * @since   1.0.0
     * @return  boolean
     */
	public function status()
	{
		return $this->status == "on" ? true : false;
	}

    /**
     * Returns if the node api is configured for homol or prod or disabled
     * NULL  for disabled API.
     * TRUE  for homol.
     * FALSE for prod.
     *
     * @since   1.0.0
     * @return 	boolean|null
     */
	public function env()
	{
		if ( $this->env === null )
			return null;

		else if ( $this->env === "on" )
			return true;

		else
			return false;
	}

    /**
     * Check if $attr is empty
     *
     * @since   1.0.0
     * @param   string $attr
     * @return  bool
     */
	public function empty( string $attr )
	{
		return empty( $this->{$attr} ) ? true : false;
	}

	/**
	 * Register logs
	 *
     * @since   1.0.0
	 * @param   string $message
	 */
	private function log( string $message )
	{
		$this->log->add( $this->id, $message );
	}

	/**
	 * Provides basic header for all calls
	 *
     * @since   1.0.0
	 * @return 	array
	 */
	private function get_header()
	{
		return $this->header;
	}

	/**
	 * Add header in basic header
	 *
     * @since   1.4.0
	 * @return 	void
	 */
	private function set_header( string $add )
	{
		array_push( $this->header, $add );
	}

	/**
	 * Prepare header to make request to the API
	 *
     * @since   1.4.0 	Added $method param
     * @since   1.0.0
	 * @param 	string 	$endpoint 	Endpoint url
	 * @param 	string 	$token 		Authorization of request
	 * @param 	array 	$params 	Body of request
	 * @param 	string 	$method 	Method of request
	 * @return 	array
	 */
    private function call( string $endpoint, string $token, array $params = [], string $method = "POST" )
    {
		if ( !$this->status() )
		{
			$error = [
				"code"    => "500.4",
				"success" => false,
				"message" => "API está desabilitada. {$endpoint}",
				"error"   => "API is disabled.",
				"data"    => null,
			];

			$this->log( json_encode( $error ) );

			return $error;
		}

		$url    = $this->url_api . $endpoint;
		$header = $this->get_header();

		if ( $endpoint === self::AUTH )
		{
			$basic  = base64_encode( $this->user . ":" . $this->pass );

			$aux    = [];
			$aux[]  = "Authorization: Basic {$basic}";

			if ( $token ) $aux[] = $token;

			$header = array_merge( $header, $aux );
		}
		else if ( $endpoint === self::AUTH_MKT )
		{
			$aux    = [
				"Authorization: {$token}",
				"x-api-key: {$this->x_key}"
			];

			$header = array_merge( $header, $aux );
		}
		else
		{
			$token  = "Authorization: Bearer $token";
			array_push( $header, $token );
		}

		return self::exec( $url, $header, $params, $method );
    }

	/**
	 * Encrypt a string
	 *
     * @since   1.4.0
	 * @param 	string $str
	 * @param 	string $key
	 * @return	string
	 */
    private function encrypt( $str, $key )
    {
        if ( !$str ) return null;

        $iv   = hex2bin( "00000000000000000000000000000000" );
        $encr = openssl_encrypt( $str, "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv );

        return urlencode( base64_encode( $encr ) );
    }

	/**
	 * Decrypt a string
	 *
     * @since   1.4.0
	 * @param 	string $str
	 * @param 	string $key
	 * @return 	string
	 */
    private function decrypt( $str, $key )
    {
        if ( !$str ) return null;

        $str = base64_decode( urldecode( $str ) );
        $iv  = hex2bin( "00000000000000000000000000000000" );

        return openssl_decrypt( $str, "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv );
    }

	/**
	 * Set security code
	 *
	 * @since 	1.5.0
	 * @param 	$code
	 * @return 	void
	 */
	private function set_security_code( string $code )
	{
		if ( $code ) $this->set_header( "securitycode: {$code}" );
	}

	/**
	 * Format cellphone for +55 DDD number
	 *
	 * @since 	1.5.0
	 * @param 	string 	$cellphone
	 * @return 	string
	 */
	private function format_cellphone( string $cellphone )
	{
        return "+" . preg_replace( "/\D/", "", $cellphone );
	}

	/**
	 * Call /auth endpoint
	 *
	 * @since 	1.5.0 	Added func to set security code
     * @since   1.4.0 	Added param $code
     * @since   1.0.0
	 * @param 	string 	$token 	Authorization of request
	 * @param 	string 	$code 	Security code
	 * @return 	array
	 */
	public function auth( string $token, string $code = "" )
	{
		$this->set_security_code( $code );

		return $this->call( self::AUTH, $token );
	}

	/**
	 * Call /auth endpoint, to get security code for validation
	 *
     * @since   1.4.0
	 * @return 	array
	 */
	public function get_security_code()
	{
		$response = $this->call( self::AUTH, "", [], "GET" );

		if ( !$response[ "success" ] ) return $response;

		$key64    = get_option( "_triibo_security_code" ); // insert manually in DB
		$key      = base64_decode( $key64 );

		$code     = $response[ "data" ][ "securityCode" ];

		$decrypt  = $this->decrypt( $code, $key );
        $decrypt  = json_decode( $decrypt );

        $decrypt->code *= 3;

		$encrypt  = $this->encrypt( json_encode( $decrypt ), $key );

		$response[ "data" ][ "securityCode" ] = $encrypt;

		return $response;
	}

	/**
	 * Call /payments/brand endpoint
	 *
     * @since   1.0.0
	 * @param 	string 	$token 		Authorization of request
	 * @param 	array 	$params 	Parameters of brand
	 * @return 	array
	 */
	public function brand( string $token, array $params )
	{
		return $this->call( self::PAY_BRAND, $token, $params );
	}

	/**
	 * Call /payments/doPayment endpoint
	 *
     * @since   1.0.0
	 * @param 	string 	$token 		Authorization of request
	 * @param 	array 	$params 	Parameters of payment
	 * @return 	array
	 */
	public function payment( string $token, array $params )
	{
		return $this->call( self::PAYMENT, $token, $params );
	}

	/**
	 * Call /payments/status endpoint
	 *
	 * @since 	1.1.4
	 * @param 	string 	$token 		Authorization of request
	 * @param 	array 	$params
	 * @return 	void
	 */
	public function payment_status( string $token, array $params )
	{
		return $this->call( self::PAYMENT_STATUS, $token, $params );
	}

	/**
	 * Call /payments/checkToken endpoint
	 *
     * @since   1.0.0
	 * @param 	string 	$token 		Authorization of request
	 * @param 	array 	$params 	Parameters of token, check
	 * @return 	array
	 */
	public function check_token( string $token, array $params )
	{
		return $this->call( self::PAY_TKN_CHECK, $token, $params );
	}

	/**
	 * Call /payments/createToken endpoint
	 *
     * @since   1.0.0
	 * @param 	string 	$token 		Authorization of request
	 * @param 	array 	$params 	Parameters of token, create
	 * @return 	array
	 */
	public function create_token( string $token, array $params )
	{
		return $this->call( self::PAY_TKN_CREATE, $token, $params );
	}

	/**
	 * Call /payments/deleteToken endpoint
	 *
     * @since   1.0.0
	 * @param 	string 	$token 		Authorization of request
	 * @param 	array 	$params 	Parameters of token, delete
	 * @return 	array
	 */
	public function delete_token( string $token, array $params )
	{
		return $this->call( self::PAY_TKN_DELETE, $token, $params );
	}

	/**
	 * Call /auth/marketplace endpoint
	 *
     * @since   1.0.0
	 * @param 	string 	$token 		Authorization of request
	 * @return 	array
	 */
    public function validate_token( string $token )
    {
		return $this->call( self::AUTH_MKT, $token );
    }

	/**
	 * Call /users/findUIDsByPhones
	 *
     * @since   1.1.3
	 * @param 	string 	$token 		Authorization of request
	 * @param 	string 	$phone 		Phone number to search
	 * @return 	array
	 */
	public function find_uid( string $token, string $phone )
	{
		return $this->call( self::USER_FIND_UID, $token, [ "phones" => [ $phone ] ] );
	}

    /**
     * Call /authcode
     *
	 * @since 	1.5.0 	Added func to format cellphone
     * @since   1.2.0
	 * @param 	string 	$token 		Authorization of request
	 * @param 	array 	$data 		Data to pass to parameters for send auth code
     * @return  array
     */
    public function auth_code( string $token, array $data )
    {
		$sms    = ( $data[ "type" ] == "sms" ) ? true : false;
		$wpp    = ( $data[ "type" ] == "wpp" ) ? true : false;

		$params = [
			"cellphone"    => $this->format_cellphone( $data[ "cellphone" ] ),
			"platform"     => "web",
			"languageCode" => "pt_BR",
			"sendType"     => [
				"sms"      => $sms,
				"whatsapp" => $wpp,
				"email"    => false,
			]
		];

		return $this->call( self::AUTH_CODE, $token, $params );
    }

	/**
	 * Get user triibo, create if not exists
	 *
     * @since 	1.5.0
	 * @param 	string 	$cellphone 	Cellphone of user
	 * @return 	array
	 */
	public function get_user( string $cellphone )
	{
        $response = $this->get_security_code();

        if ( $response[ "success" ] )
        {
			$code     = $response[ "data" ][ "securityCode" ];
			$response = $this->auth( "", $code );

            if ( $response[ "success" ] )
			{
                $token  = $response[ "data"  ][ "token" ];

				$params = [
					"cellphone" => $this->format_cellphone( $cellphone ),
				];

				return $this->call( self::LOGIN, $token, $params );
			}
		}

		return $response;
	}

	/**
	 * Validates SMS code
	 *
	 * @since 	1.5.0 	Removed param $token
	 * @param 	array 	$data 	Data to pass to parameters for validation of SMS code
	 * @return 	array
	 */
	public function validate_sms( array $data )
	{
        $response = $this->get_security_code();

        if ( $response[ "success" ] )
        {
			$code     = $response[ "data" ][ "securityCode" ];
			$response = $this->auth( "", $code );

            if ( $response[ "success" ] )
			{
                $token  = $response[ "data"  ][ "token" ];

				$params = [
					"code"          => $data[ "code"     ],
					"transactionId" => $data[ "trans_id" ],
					"cellphone"     => $this->format_cellphone( $data[ "cellphone" ] ),
				];

				return $this->call( self::SMS_VALIDATE, $token, $params );
			}
		}

		return $response;
	}
}
