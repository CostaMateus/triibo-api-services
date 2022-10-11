<?php
/**
 * The file that defines the Triibo Node API
 *
 * A class definition that includes attributes and functions used for access the Triibo Node API.
 *
 * @since      1.0.0
 * @package    Triibo_Api_Services
 * @subpackage Triibo_Api_Services/includes/apis
 * @author     Mateus Costa <mateus@costamateus.com.br>
 */

class Triibo_Api_Node
{
	const AUTH       = "/auth";
	const BRAND      = "/payments/brand";
	const PAYMENT    = "/payments/doPayment";
	const TKN_CHECK  = "/payments/checkToken";
	const TKN_CREATE = "/payments/createToken";
	const TKN_DELETE = "/payments/deleteToken";
	const EVENT      = "//";

	private $id      = "triibo-api-node";

    private $status  = null;
    private $env     = null;
    private $url_api = null;
    private $url_mkt = null;
    private $user    = null;
    private $pass    = null;

	private $log     = null;

    public function __construct()
    {
		$this->log    = ( function_exists( "wc_get_logger" ) ) ? wc_get_logger() : new WC_Logger();

        $this->status = get_option( "triibo_api_services_node_status" );

        if ( $this->status )
        {
            $this->env     = get_option( "triibo_api_services_node_env" );

            $this->url_api = "https://api" . ( $this->env ? "-hml" : "" ) . ".triibo.com.br";
            $this->url_mkt = "https://"    . ( $this->env ? "dev-" : "" ) . "marketplace.triibo.com.br";

            $this->user    = $this->env ? get_option( "triibo_api_services_node_hml_user" ) : get_option( "triibo_api_services_node_prd_user" );
            $this->pass    = $this->env ? get_option( "triibo_api_services_node_hml_pass" ) : get_option( "triibo_api_services_node_prd_pass" );
        }
    }

    /**
     * Returns the node api status
     * TRUE  for active.
     * FALSE for inactive.
     *
     * @since   1.0.0
     *
     * @return boolean
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
     *
     * @return boolean|null
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

	private function log( string $message )
	{
		$this->log->add( $this->id, $message );
	}

	private function get_header()
	{
		return [
			"Origin: {$this->url_mkt}",
			"Content-type: application/json",
		];
	}

    private function call( array $params, string $method, string $token, string $endpoint )
    {
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
		else
		{
			$token  = "Authorization: Bearer $token";
			array_push( $header, $token );
		}

        $args = [
			"method"  => $method,
            "timeout" => 30,
			"headers" => $header,
        ];

		$this->log( json_encode( $args ) );

		if ( $params ) $args[ "body" ] = $params;

		$response = wp_remote_request( $url, $args );

		return wp_remote_retrieve_body( $response );
    }

	public function auth( array $params, string $method, string $token )
	{
		$response = $this->call( $params, $method, $token, self::AUTH );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function brand( array $params, string $method, string $token )
	{
		$response = $this->call( $params, $method, $token, self::BRAND );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function payment( array $params, string $method, string $token )
	{
		$response = $this->call( $params, $method, $token, self::PAYMENT );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function check_token( array $params, string $method, string $token )
	{
		$response = $this->call( $params, $method, $token, self::TKN_CHECK );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function create_token( array $params, string $method, string $token )
	{
		$response = $this->call( $params, $method, $token, self::TKN_CREATE );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function delete_token( array $params, string $method, string $token )
	{
		$response = $this->call( $params, $method, $token, self::TKN_DELETE );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function event( array $params, string $method, string $token )
	{
		$response = $this->call( $params, $method, $token, self::TKN_DELETE );
		$this->log( json_encode( $response ) );
		return $response;
	}
}
