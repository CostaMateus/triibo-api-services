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

class Triibo_Api_Node extends Triibo_Api
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

    private function call( string $endpoint, string $token, array $params = [] )
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

		// remover posteriormente
		$this->log( json_encode( [ $url, $params ] ) );

		return self::exec( $url, $header, $params );
    }

	public function auth( string $token )
	{
		$response = $this->call( self::AUTH, $token );

		// remover posteriormente
		$this->log( json_encode( $response ) );

		return $response;
	}

	public function brand( string $token, array $params )
	{
		$response = $this->call( self::BRAND, $token, $params );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function payment( string $token, array $params )
	{
		$response = $this->call( self::PAYMENT, $token, $params );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function check_token( string $token, array $params )
	{
		$response = $this->call( self::TKN_CHECK, $token, $params );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function create_token( string $token, array $params )
	{
		$response = $this->call( self::TKN_CREATE, $token, $params );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function delete_token( string $token, array $params )
	{
		$response = $this->call( self::TKN_DELETE, $token, $params );
		$this->log( json_encode( $response ) );
		return $response;
	}

	public function event( string $token, array $params )
	{
		$response = $this->call( self::TKN_DELETE, $token, $params );
		$this->log( json_encode( $response ) );
		return $response;
	}
}
