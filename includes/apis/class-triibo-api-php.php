<?php
/**
 * The file that defines the Triibo PHP API
 *
 * A class definition that includes attributes and functions used for access the Triibo PHP API.
 *
 * @since      1.0.0
 * @package    Triibo_Api_Services
 * @subpackage Triibo_Api_Services/includes/apis
 * @author     Mateus Costa <mateus@costamateus.com.br>
 */

class Triibo_Api_Php
{
    private $status = null;
    private $env    = null;
    private $url    = null;
    private $key    = null;
    private $tkn    = null;

    public function __construct()
    {
        $this->status = get_option( "triibo_api_services_php_status" );

        if ( $this->status )
        {
            $this->env = get_option( "triibo_api_services_php_env"    );

            $this->url = "http://php" . ( $this->env ? "-hml" : "" ) . ".triibo.com.br";
            $this->key = $this->env ? get_option( "triibo_api_services_php_hml_key" ) : get_option( "triibo_api_services_php_prd_key" );
            $this->tkn = $this->env ? get_option( "triibo_api_services_php_hml_tkn" ) : get_option( "triibo_api_services_php_prd_prd" );
        }
    }

    /**
     * Returns whether the PHP API is active or not
     *
     * @since   1.0.0
     *
     * @return boolean
     */
    public function is_active()
    {
        return $this->status;
    }

    /**
     * Returns if the php api is configured for homol or prod
     * TRUE  for homol.
     * FALSE for prod.
     * NULL  for disabled API.
     *
     * @since   1.0.0
     *
     * @return boolean
     */
    public function is_homol()
    {
        if ( $this->env === null ) return null;

        return $this->env;
    }

    private function get_header()
    {
        return [
            "Accept: application/json",
            "Content-Type: application/json",
            "api-key: " . $this->key,
            "api-token: " . $this->tsoken,
        ];
    }

    public function call( array $params, string $method, string $endpoint )
    {
        $url  = $this->url . "/" . $endpoint;
        $args = [
            "body"    => $params,
            "method"  => $method,
            "timeout" => 30,
            "headers" => $this->get_header()
        ];

        $response = wp_remote_request( $url, $args );

        if ( !is_wp_error( $response ) && in_array( $response[ "response" ][ "code" ], [ 200, 201 ] ) )
        {
          return $response[ "body" ];
        }
        else
        {
          return false;
        }
    }


}
