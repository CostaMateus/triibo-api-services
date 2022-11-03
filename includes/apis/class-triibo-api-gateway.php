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
    const LOGIN         = "/login";
    const SET_USER_INFO = "/setUserInfo_v1";

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

        $this->status = get_option( "triibo_api_services_gate_status" );
        $this->env    = get_option( "triibo_api_services_gate_env"    );

        if ( $this->status == "on" )
        {
            $this->url     = "https://triibo-api-gateway" . ( $this->env ? "-hml" : "" ) . ".triibo.com.br";

            $this->token   = $this->env ? get_option( "triibo_api_services_gate_hml_tkn"     ) : get_option( "triibo_api_services_gate_prd_tkn"     );
            $this->chn_id  = $this->env ? get_option( "triibo_api_services_gate_hml_chn_id"  ) : get_option( "triibo_api_services_gate_prd_chn_id"  );
            $this->chn_tkn = $this->env ? get_option( "triibo_api_services_gate_hml_chn_tkn" ) : get_option( "triibo_api_services_gate_prd_chn_tkn" );
        }

        $this->triibo_header = [
            "channelName"   => self::GP_NM,
            "channelGroup"  => self::GP_NM,
            "apiToken"      => $this->token,
            "channelId"     => $this->chn_id,
            "sessionId"     => "001",
            "transactionId" => "002",
        ];
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

    private function hash( string $uid = null )
    {
        $passPhrase = floor( rand( 1000000000, 9999999999 ) ) . $uid . floor( rand( 1000000000, 9999999999 ) );

        $challenge1 = md5( $passPhrase . $this->token   );
        $challenge2 = md5( $challenge1 . $this->chn_tkn );

        return [
            "passPhrase" => $passPhrase,
            "challenge"  => $challenge2,
        ];
    }

    public function get_channel_token( string $uid = null )
    {
        $hash   = $this->hash( $uid );
        $params = [
            "triiboHeader" => array_merge( $this->triibo_header, [
                "uId"        => $uid,
                "passPhrase" => $hash[ "passPhrase" ],
                "challenge"  => $hash[ "challenge"  ]
            ] ),
        ];

        return $this->call( self::CHN_TKN_ID, $params );
    }

    public function credit_points( string $triibo_id, int $value )
    {
        $response   = $this->get_channel_token();
        if ( !$response[ "success" ] || !$response[ "data" ][ "success" ] ) return $response;

        $chn_tkn_id = $response[ "data" ][ "success" ][ "newToken" ][ "channelTokenId" ];
        $params     = [
            "triiboHeader"   => array_merge( $this->triibo_header, [
                "channelTokenId" => $chn_tkn_id,
            ] ),
            "triiboId"       => "+{$triibo_id}@sms.triibo.com.br",
            "eventType"      => "Compra_Marketplace",
            "recursionValue" => $value,
        ];

        $response   = $this->call( self::CREDIT_POINTS, $params );
        if ( !$response[ "success" ] || !$response[ "data" ][ "success" ] ) return $response;

        return $response;
    }

    public function update_user_mkt( string $cellphone, string $email, string $action )
    {
        // $response = [
        // 	"code"    => $info[ "http_code" ],
        // 	"success" => true,
        // 	"message" => "ok",
        // 	"error"   => null,
        // 	"data"    => json_decode( $response, true ),
        // ];

        $response   = $this->get_channel_token();
        if ( !$response[ "success" ] || !$response[ "data" ][ "success" ] ) return $response;

        $cellphone  = "+" . preg_replace( "/\D/", "", $cellphone );
        $chn_tkn_id = $response[ "data" ][ "success" ][ "newToken" ][ "channelTokenId" ];

        $params     = [
            "triiboHeader"  => array_merge( $this->triibo_header, [
                "channelTokenId" => $chn_tkn_id
            ] ),
            "linkedAccount" => NULL,
            "cellphone"     => $cellphone,
        ];

        $response   = $this->call( self::LOGIN, $params );
        if ( !$response[ "success" ] || !$response[ "data" ][ "success" ] ) return $response;

        $user       = $response[ "data" ][ "user" ];
        $uid        = $user[ "uId"                ];
        $contact    = $user[ "contactList"        ];

        $key        = array_search( "emailMarketplace", array_column( $contact, "type" ) );

        // email marketplace
        if ( $key !== false )
        {
            $contact[ $key ][ "value"    ] = $email;
            $contact[ $key ][ "verified" ] = true;
        }
        else
        {
            array_push( $contact, [
                "type"     => "emailMarketplace",
                "value"    => $email,
                "verified" => true
            ] );
        }

        if ( in_array( $action, [ "UPDATE", "CREATE" ] ) )
        {
            $key = array_search( "email", array_column( $contact, "type" ) );

            // email
            if ( $key !== false )
            {
                $contact[ $key ][ "verified" ] = true;
            }
            else
            {
                array_push( $contact, [
                    "type"     => "email",
                    "value"    => $email,
                    "verified" => true
                ] );
            }
        }

        $response   = $this->get_channel_token( $uid );
        if ( !$response[ "success" ] || !$response[ "data" ][ "success" ] ) return $response;

        $chn_tkn_id = $response[ "data" ][ "success" ][ "newToken" ][ "channelTokenId" ];
        $params     = [
            "triiboHeader"    => array_merge( $this->triibo_header, [
                "channelTokenId" => $chn_tkn_id,
                "uId"            => $uid
            ] ),
            "queryPartnerAPI" => [ "setUserInfo" ],
            "userInfo"        => [
                "syncType"    => "override",
                "uId"         => $uid,
                "contactList" => [ ... $contact ],
            ],
        ];

        $response   = $this->call( self::SET_USER_INFO, $params );
        if ( !$response[ "success" ] || !$response[ "data" ][ "success" ] ) return $response;

        $response[ "uid" ] = $uid;

        return $response;
    }
}
