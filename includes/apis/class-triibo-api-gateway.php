<?php
/**
 * The file that defines the Triibo Gateway API
 *
 * A class definition that includes attributes and functions used for access the Triibo Gateway API.
 *
 * @author 		Mateus Costa <mateus@costamateus.com.br>
 * @package 	Triibo_Api_Services/Classes/Api
 * @version 	1.0.0
 */
defined( "ABSPATH" ) || exit;

class Triibo_Api_Gateway extends Triibo_Api
{
	/**
	 * GROUP/NAME composition of $header
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
    const GP_NM         = "triibo-webapp"; // GROUP/NAME

	/**
	 * Default header of all calls. Can be incremented
	 *
	 * @since 	1.0.0
	 * @var 	array
	 */
    const HEADER        = [ "Content-type: application/json" ];

	/**
	 * Endpoint for api /getChannelToken_v1
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
    const CHN_TKN_ID    = "/getChannelToken_v1";

	/**
	 * Endpoint for api /creditPoints
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
    const CREDIT_POINTS = "/creditPoints";

	/**
	 * Endpoint for api /login
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
    const LOGIN         = "/login";

	/**
	 * Endpoint for api /setUserInfo_v1
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
    const SET_USER_INFO = "/setUserInfo_v1";

	/**
	 * Endpoint for api /authSMS_v1
	 *
	 * @since 	1.1.0
	 * @var 	string
	 */
    const AUTH_SMS = "/authSMS_v1";

	/**
	 * Endpoint for api /validateSMS_v1
	 *
	 * @since 	1.1.0
	 * @var 	string
	 */
    const VAL_SMS  = "/validateSMS_v1";

	/**
	 * Internal ID for log
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $id         = "triibo-api-gateway";

	/**
	 * Header of this api calls
	 *
	 * @since 	1.0.0
	 * @var 	array|null
	 */
    private $header     = null;

	/**
	 * Status of this api
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $status     = null;

	/**
	 * Environment hml/prod
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $env        = null;

	/**
	 * Endpoint hml/prod
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $url        = null;

	/**
	 * Token
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $token      = null;

	/**
	 * Channel ID
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $chn_id     = null;

	/**
	 * Channel Token
	 *
	 * @since 	1.0.0
	 * @var 	string|null
	 */
    private $chn_tkn    = null;

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
     * @since   1.0.0
     */
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

        $this->header = [
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
     * @return  boolean
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
     * @return  boolean|null
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
     * Exec api call
     *
     * @since   1.0.0
     * @param   string $endpoint
     * @param   array $params
     * @return  array
     */
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

    /**
     * Get hash for channel token
     *
     * @since   1.0.0
     * @param   string|null $uid
     * @return  array
     */
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

    /**
     * Get channel token
     *
     * @since   1.0.0
     * @param   string|null $uid
     * @return  array
     */
    public function get_channel_token( string $uid = null )
    {
        $hash   = $this->hash( $uid );
        $params = [
            "triiboHeader" => array_merge( $this->header, [
                "uId"        => $uid,
                "passPhrase" => $hash[ "passPhrase" ],
                "challenge"  => $hash[ "challenge"  ]
            ] ),
        ];

        return $this->call( self::CHN_TKN_ID, $params );
    }

    /**
     * Credits points to the user
     *
     * @since   1.0.0
     * @param   string $triibo_id
     * @param   integer $value
     * @return  array
     */
    public function credit_points( string $triibo_id, int $value )
    {
        $response   = $this->get_channel_token();
        if ( !$response[ "success" ] || !$response[ "data" ][ "success" ] ) return $response;

        $chn_tkn_id = $response[ "data" ][ "success" ][ "newToken" ][ "channelTokenId" ];
        $params     = [
            "triiboHeader"   => array_merge( $this->header, [
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

    /**
     * Update marketplace user
     *
     * @since   1.0.0
     * @param   string $cellphone
     * @param   string $email
     * @param   string $action
     * @return  array
     */
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
            "triiboHeader"  => array_merge( $this->header, [
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
            "triiboHeader"    => array_merge( $this->header, [
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

    /**
     * Send code SMS
     *
     * @since   1.1.0
     * @param   string $cellphone
     * @return  array
     */
    public function auth_sms( string $cellphone )
    {
        $response   = $this->get_channel_token();

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
        {
            $this->log( json_encode( $response ) );
            return $response;
        }

        $cellphone  = "+" . preg_replace( "/\D/", "", $cellphone );
        $chn_tkn_id = $response[ "data" ][ "success" ][ "newToken" ][ "channelTokenId" ];

        $params     = [
            "triiboHeader" => array_merge( $this->header, [
                "channelTokenId" => $chn_tkn_id
            ] ),
            "platform"     => "web",
            "cellphone"    => $cellphone,
        ];

        $response   = $this->call( self::AUTH_SMS, $params );

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
            $this->log( json_encode( $response ) );

        return $response;
    }

    /**
     * Validates code SMS
     *
     * @since   1.1.0
     * @param   string $code
     * @param   string $cellphone
     * @param   string $trans_id
     * @return  void
     */
    public function validate_sms( string $code, string $cellphone, string $trans_id )
    {
        $response   = $this->get_channel_token();

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
        {
            $this->log( json_encode( $response ) );
            return $response;
        }

        $cellphone  = "+" . preg_replace( "/\D/", "", $cellphone );
        $chn_tkn_id = $response[ "data" ][ "success" ][ "newToken" ][ "channelTokenId" ];

        $params     = [
            "triiboHeader"  => array_merge( $this->header, [
                "channelTokenId" => $chn_tkn_id
            ] ),
            "code"          => $code,
            "cellphone"     => $cellphone,
            "transactionId" => $trans_id,
        ];

        $response   = $this->call( self::VAL_SMS, $params );

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
            $this->log( json_encode( $response ) );

        return $response;
    }
}
