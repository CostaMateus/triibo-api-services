<?php
/**
 * The file that defines the Triibo PHP API
 *
 * A class definition that includes attributes and functions used for access the Triibo PHP API.
 *
 * @author 		Mateus Costa <mateus@costamateus.com.br>
 * @package 	Triibo_Api_Services/Classes/Api
 * @version 	1.10.0
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
	const FREIGHT_CALC   = "/api/marketplace/wc/freightcalculation";

	/**
	 * Endpoint for api /updatesku/
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const UPDATE_SKU     = "/api/marketplace/wc/updatesku/";

	/**
	 * Endpoint for api /ordercreation/
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const O_CREATION     = "/api/marketplace/wc/ordercreation/";

	/**
	 * Endpoint for api /orderapproval/
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const O_APPROVAL     = "/api/marketplace/wc/orderapproval/";

	/**
	 * Endpoint for api /ordercancellation/
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	const O_CANCELLATION = "/api/marketplace/wc/ordercancellation/";

	/**
	 * Internal ID for log
	 *
	 * @since 	1.11.0 	Updated string format.
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $id          = "triibo-api-php";

	/**
	 * Status of this api
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $status      = null;

	/**
	 * Environment hml/prod
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $env         = null;

	/**
	 * Endpoint hml/prod
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $url         = null;

	/**
	 * Key
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $key         = null;

	/**
	 * Token
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $tkn         = null;

	/**
	 * Header for api calls
	 *
	 * @since 	1.11.0
	 * @var 	array|null
	 */
    private $header      = null;

	/**
	 * Construct.
	 *
	 * @since 	1.11.0 	Added global header.
	 * @since 	1.10.0 	Removed $log class property.
	 * @since 	1.0.0
	 */
    public function __construct()
    {
        $this->status = get_option( "triibo_api_services_php_status" );

        if ( $this->status == "on" )
        {
            $this->env    = get_option( "triibo_api_services_php_env" );

			$this->url    = ( get_home_url() === "http://shopkeeper.local" )
							? "http://127.0.0.1:8000"
							: "http://php" . ( $this->env ? "-hml" : "" ) . ".triibo.com.br";

            $this->key    = $this->env ? get_option( "triibo_api_services_php_hml_key" ) : get_option( "triibo_api_services_php_prd_key" );
            $this->tkn    = $this->env ? get_option( "triibo_api_services_php_hml_tkn" ) : get_option( "triibo_api_services_php_prd_tkn" );

			$this->header = [
				"Accept: application/json",
				"Content-Type: application/json",
				"api-key: {$this->key}",
				"api-token: {$this->tkn}",
			];
        }
    }

    /**
     * Returns the php api status.
     * TRUE  for active.
     * FALSE for inactive.
     *
     * @since   1.0.0
     * @return 	boolean
     */
	public function status()
	{
		return $this->status == "on" ? true : false;
	}

    /**
     * Returns if the php api is configured for homol or prod or disabled.
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
     * Check if $attr is empty.
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
	 * Register logs.
	 *
	 * @since 	1.10.0 	Changing to the new woocommerce standard.
	 * 					Added params $level and $is_error.
	 * @since 	1.0.0
	 * @param 	string 	$level
	 * @param 	string 	$message
	 * @param 	bool 	$is_error
	 * @return 	void
	 */
	private function log( string $level, string $message, bool $is_error = false )
	{
		$logger  = ( function_exists( "wc_get_logger" ) ) ? wc_get_logger() : new WC_Logger();

		$context = [ "source" => $this->id ];

		if ( $is_error || $this->env() === true )
			$logger->$level( $message, $context );
	}

	/**
	 * Provides basic header for all calls.
	 *
	 * @since 	1.11.0 	Updated to global header.
	 * @since 	1.0.0
	 * @return 	array
	 */
	private function get_header()
	{
		return $this->header;
    }

	/**
	 * Add header in basic header.
	 *
     * @since   1.11.0
	 * @return 	void
	 */
	private function add_to_header( string $add )
	{
		array_push( $this->header, $add );
	}

	/**
	 * Prepare header to make request to the API.
	 *
	 * @since 	1.0.0
	 * @param 	string 	$endpoint 	Endpoint url.
	 * @param 	string 	$url_param 	URL params.
	 * @param 	array 	$params 	Body of request.
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

			$this->log( "critical", json_encode( $error ), true );

			return $error;
		}

		$url    = $this->url . $endpoint;
		$url    = ( $url_param ) ? $url . $url_param : $url;

		$header = $this->get_header();

		return self::exec( $url, $header, $params );
    }

	/**
	 * Call /api/marketplace/wc/freightcalculation endpoint.
	 *
	 * @since 	1.11.0 	Added 'seller' param.
	 * @since 	1.0.0
	 * @param 	array 	$params 	Params of request.
	 * @param 	array 	$seller 	Seller.
	 * @return 	array
	 */
	public function freight_calculation( array $params, string $seller )
	{
		$this->add_to_header( "seller: {$seller}" );

		return $this->call( self::FREIGHT_CALC, null, $params );
	}

	/**
	 * Call /api/marketplace/wc/updatesku/{sku} endpoint.
	 *
	 * @since 	1.11.0 	Added 'seller' param.
	 * @since 	1.0.0
	 * @param 	string 	$sku 	SKU of product.
	 * @param 	array 	$seller 	Seller.
	 * @return 	array
	 */
	public function update_sku( string $sku, string $seller )
	{
		$this->add_to_header( "seller: {$seller}" );

		return $this->call( self::UPDATE_SKU, $sku );
	}

	/**
	 * Call /api/marketplace/wc/ordercreation/{$order_id} endpoint.
	 *
	 * @since 	1.11.0 	Added 'seller' param.
	 * @since 	1.0.0
	 * @param 	string 	$order_id 	ID of order.
	 * @param 	array 	$seller 	Seller.
	 * @return 	array
	 */
	public function order_creation( string $order_id, string $seller )
	{
		$this->add_to_header( "seller: {$seller}" );

		return $this->call( self::O_CREATION, $order_id );
	}

	/**
	 * Call /api/marketplace/wc/orderapproval/{$order_id} endpoint.
	 *
	 * @since 	1.11.0 	Added 'seller' param.
	 * @since 	1.0.0
	 * @param 	string 	$order_id 	ID of order.
	 * @param 	array 	$seller 	Seller.
	 * @return 	array
	 */
	public function order_approval( string $order_id, string $seller )
	{
		$this->add_to_header( "seller: {$seller}" );

		return $this->call( self::O_APPROVAL, $order_id );
	}

	/**
	 * Call /api/marketplace/wc/ordercancellation/{$order_id} endpoint.
	 *
	 * @since 	1.11.0 	Added 'seller' param.
	 * @since 	1.0.0
	 * @param 	string 	$order_id 	ID of order.
	 * @param 	array 	$seller 	Seller.
	 * @return 	array
	 */
	public function order_cancellation( string $order_id, string $seller )
	{
		$this->add_to_header( "seller: {$seller}" );

		return $this->call( self::O_CANCELLATION, $order_id );
	}
}
