<?php
/**
 * The file that defines the Triibo Gateway API
 *
 * A class definition that includes attributes and functions used for access the Triibo Gateway API.
 *
 * @since      1.0.0
 * @package    Triibo_Api_Services
 * @subpackage Triibo_Api_Services/includes/apis
 * @author     Mateus Costa <mateus@costamateus.com.br>
 */

class Triibo_Api_Gateway extends Triibo_Api
{
    const GP_NM         = "triibo-webapp"; // GROUP/NAME
    const HEADER        = [ "Content-type: application/json" ];

    const CHN_TKN_ID    = "/getChannelToken_v1";
    const CREDIT_POINTS = "/creditPoints";

	private $id         = "triibo-api-gateway";

    private $status     = null;
    private $env        = null;
    private $url        = null;

    private $token      = null;
    private $chn_id     = null;
    private $chn_tkn    = null;

	private $log        = null;

    public function __construct()
    {
		$this->log    = ( function_exists( "wc_get_logger" ) ) ? wc_get_logger() : new WC_Logger();

        $this->status = get_option( "triibo_api_services_gateway_status" );

        if ( $this->status == "on" )
        {
            $this->env     = get_option( "triibo_api_services_gateway_env" );

            $this->url     = "https://triibo-api-gateway" . ( $this->env ? "-hml" : "" ) . ".triibo.com.br";

            $this->token   = $this->env ? get_option( "triibo_api_services_gate_hml_tkn"     ) : get_option( "triibo_api_services_gate_prd_tkn"     );
            $this->chn_id  = $this->env ? get_option( "triibo_api_services_gate_hml_chn_id"  ) : get_option( "triibo_api_services_gate_prd_chn_id"  );
            $this->chn_tkn = $this->env ? get_option( "triibo_api_services_gate_hml_chn_tkn" ) : get_option( "triibo_api_services_gate_prd_chn_tkn" );
        }
    }

    /**
     * Returns the php api status
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
     * Returns if the php api is configured for homol or prod or disabled
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

	public function empty( string $attr )
	{
		return empty( $this->{$attr} ) ? true : false;
	}

	/**
	 * Register logs
	 *
	 * @param string $message
	 */
	private function log( string $message )
	{
		$this->log->add( $this->id, $message );
	}

    private function call( string $endpoint, array $params )
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

		$url = $this->url . $endpoint;

		return self::exec( $url, self::HEADER, $params );
    }

    private function hash( string $passPhrase )
    {
        $challenge1 = md5( $passPhrase . $this->token   );
        $challenge2 = md5( $challenge1 . $this->chn_tkn );

        return $challenge2;
    }

    public function get_channel_token( string $uid = null )
    {
        $passPhrase = floor( rand( 1000000000, 9999999999 ) ) . $uid . floor( rand( 1000000000, 9999999999 ) );
        $challenge  = $this->hash( $passPhrase );

        $params     = [
            "triiboHeader" => [
                "channelName"   => self::GP_NM,
                "channelGroup"  => self::GP_NM,
                "apiToken"      => $this->token,
                "channelId"     => $this->chn_id,
                "sessionId"     => "001",
                "transactionId" => "002",
                "uId"           => $uid,
                "passPhrase"    => $passPhrase,
                "challenge"     => $challenge
            ],
        ];

        return $this->call( self::CHN_TKN_ID, $params );
    }

    public function credit_points( string $triiboId, int $value )
    {
        $params = [
            "triiboId"       => "{$triiboId}@sms.triibo.com.br",
            "eventType"      => "Compra_Marketplace_Triibo",
            "recursionValue" => $value,
        ];

        return $this->call( self::CREDIT_POINTS, $params );
    }
}
