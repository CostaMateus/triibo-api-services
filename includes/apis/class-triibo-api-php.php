<?php
/**
 * The file that defines the Triibo PHP API
 *
 * A class definition that includes attributes and functions used for access the Triibo PHP API.
 *
 * @author 		Mateus Costa <mateus@costamateus.com.br>
 * @package 	Triibo_Api_Services/Classes/Api
 * @version 	1.0.0
 */
defined( "ABSPATH" ) || exit;

class Triibo_Api_Php extends Triibo_Api
{
	/**
	 * Endpoint for api /freightcalculation
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const FREIGHT_CALC  = "/api/marketplace/wc/freightcalculation";

	/**
	 * Endpoint for api /updatesku/
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const UPDATE_SKU    = "/api/marketplace/wc/updatesku/";

	/**
	 * Endpoint for api /ordercreation/
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const O_CREATION    = "/api/marketplace/wc/ordercreation/";

	/**
	 * Endpoint for api /orderapproved/
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const O_APPROVED    = "/api/marketplace/wc/orderapproved/";

	/**
	 * Endpoint for api /ordercancelation/
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const O_CANCELATION = "/api/marketplace/wc/ordercancelation/";

	/**
	 * Internal ID for log
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $id         = "triibo-api-php";

	/**
	 * Status of this api
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $status     = null;

	/**
	 * Environment hml/prod
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $env        = null;

	/**
	 * Endpoint hml/prod
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $url        = null;

	/**
	 * Key
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $key        = null;

	/**
	 * Token
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $tkn        = null;

	/**
	 * Logger
	 *
	 * @since 	1.0.0
	 * @var 	wc_get_logger|WC_Logger|null
	 */
	private $log        = null;

	/**
	 * Construct
	 *
	 * @since 	1.0.0
	 */
    public function __construct()
    {
		$this->log    = ( function_exists( "wc_get_logger" ) ) ? wc_get_logger() : new WC_Logger();

        $this->status = get_option( "triibo_api_services_php_status" );

        if ( $this->status == "on" )
        {
            $this->env = get_option( "triibo_api_services_php_env" );

            $this->url = "http://php" . ( $this->env ? "-hml" : "" ) . ".triibo.com.br";
            $this->key = $this->env ? get_option( "triibo_api_services_php_hml_key" ) : get_option( "triibo_api_services_php_prd_key" );
            $this->tkn = $this->env ? get_option( "triibo_api_services_php_hml_tkn" ) : get_option( "triibo_api_services_php_prd_tkn" );
        }
    }

    /**
     * Returns the php api status
     * TRUE  for active.
     * FALSE for inactive.
     *
     * @since   1.0.0
     * @return boolean
     */
	public function status()
	{
		return $this->status == "on" ? true : false;
	}

    /**
     * Returns if the php api is configured for homol or prod or disabled
     * NULL  for disabled API.
     * TRUE  for homol.
     * FALSE for prod.
     *
     * @since   1.0.0
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
	 * @since 	1.0.0
	 * @param 	string $message
	 */
	private function log( string $message )
	{
		$this->log->add( $this->id, $message );
	}

	/**
	 * Provides basic header for all calls
	 *
	 * @since 	1.0.0
	 * @return 	array
	 */
	private function get_header()
	{
        return [
            "Accept: application/json",
            "Content-Type: application/json",
            "api-key: "   . $this->key,
            "api-token: " . $this->tkn,
        ];
    }

	/**
	 * Prepare header to make request to the API
	 *
	 * @since 	1.0.0
	 * @param 	string 	$endpoint 	Endpoint url
	 * @param 	string 	$url_param 	URL params
	 * @param 	array 	$params 	Body of request
	 * @return 	array
	 */
    private function call( string $endpoint, string $url_param = null, array $params = [] )
    {
		if ( !$this->status() )
		{
			$error = [
				"code"    => "500.4",
				"success" => false,
				"message" => "API está desabilitada.",
				"error"   => "API is disabled.",
				"data"    => null,
			];

			$this->log( json_encode( $error ) );

			return $error;
		}

		$url    = $this->url . $endpoint;
		$url    = ( $url_param ) ? $url . $url_param : $url;

		$header = $this->get_header();

		return self::exec( $url, $header, $params );
    }

	/**
	 * Call /api/marketplace/wc/freightcalculation endpoint
	 *
	 * @since 	1.0.0
	 * @param 	array $params 	Params of request
	 * @return 	array
	 */
	public function fs_freight_calculation( array $params )
	{
		return $this->call( self::FREIGHT_CALC, null, $params );
	}

	/**
	 * Call /api/marketplace/wc/updatesku/{sku} endpoint
	 *
	 * @since 	1.0.0
	 * @param 	string $sku 	SKU of product
	 * @return 	array
	 */
	public function fs_update_sku( string $sku )
	{
		return $this->call( self::UPDATE_SKU, $sku );
	}

	/**
	 * Call /api/marketplace/wc/ordercreation/{$order_id} endpoint
	 *
	 * @since 	1.0.0
	 * @param 	string $order_id 	ID of order
	 * @return 	array
	 */
	public function fs_order_creation( string $order_id )
	{
		return $this->call( self::O_CREATION, $order_id );
	}

	/**
	 * Call /api/marketplace/wc/orderapproved/{$order_id} endpoint
	 *
	 * @since 	1.0.0
	 * @param 	string $order_id 	ID of order
	 * @return 	array
	 */
	public function fs_order_approved( string $order_id )
	{
		return $this->call( self::O_APPROVED, $order_id );
	}

	/**
	 * Call /api/marketplace/wc/ordercancelation/{$order_id} endpoint
	 *
	 * @since 	1.0.0
	 * @param 	string $order_id 	ID of order
	 * @return 	array
	 */
	public function fs_order_cancelation( string $order_id )
	{
		return $this->call( self::O_CANCELATION, $order_id );
	}
}
