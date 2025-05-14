<?php

/**
 * The file that defines the Triibo Gateway API
 *
 * A class definition that includes attributes and functions used for access the Triibo Gateway API.
 *
 * @author 		Mateus Costa <mateus@costamateus.com.br>
 * @package 	Triibo_Api_Services/Classes/Api
 * @version 	1.10.0
 */
defined( "ABSPATH" ) || exit;

class Triibo_Api_Gateway extends Triibo_Api
{
    /**
     * GROUP/NAME composition of $header.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const GP_NM         = "triibo-webapp";

    /**
     * Default header of all calls. Can be incremented.
     *
     * @since 1.0.0
     *
     * @var array
     */
    const HEADER        = ["Content-type: application/json"];

    /**
     * Endpoint for api /getChannelToken_v1.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const CHN_TKN_ID    = "/getChannelToken_v1";

    /**
     * Endpoint for api /creditPoints.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const CREDIT_POINTS = "/creditPoints";

    /**
     * Endpoint for api /login.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const LOGIN         = "/login";

    /**
     * Endpoint for api /setUserInfo_v1.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const SET_USER_INFO = "/setUserInfo_v1";

    /**
     * Endpoint for api /authSMS_v1.
     *
     * @since 1.1.0
     *
     * @var string
     */
    const AUTH_SMS      = "/authSMS_v1";

    /**
     * Endpoint for api /validateSMS_v1.
     *
     * @since 1.1.0
     *
     * @var string
     */
    const VAL_SMS       = "/validateSMS_v1";

    /**
     * Endpoint for api /getUserInfo_v1.
     *
     * @since 1.1.0
     *
     * @var string
     */
    const GET_USER_INFO = "/getUserInfo_v1";

    /**
     * Endpoint for api /insertUserOrgs.
     *
     * @since 1.3.0
     *
     * @var string
     */
    const ADD_USER_ORG  = "/insertUserOrgs";

    /**
     * Endpoint for api /removeUserOrgs.
     *
     * @since 1.3.0
     *
     * @var string
     */
    const DEL_USER_ORG  = "/removeUserOrgs";

    /**
     * Option name for system UID.
     *
     * @since 1.14.0
     *
     * @var string
     */
    const SYS_UID_OPTION = "gateway_sys_uid";

    /**
	 * Internal ID for log.
	 *
	 * @since 2.0.0 	Same visibility as the parent
	 * @since 1.11.0 	Updated string format.
	 * @since 1.0.0
     *
	 * @var string
	 */
	protected string $id = "triibo-api-gateway";

	/**
	 * Environment hml/prod.
     *
	 * @since 2.0.0 	Same visibility as the parent
	 * @since 1.0.0
	 *
	 * @var null|string
	 */
	protected ?string $env = null;

	/**
	 * Status of this api.
	 *
	 * @since 2.0.0 	Same visibility as the parent
	 * @since 1.0.0
	 *
	 * @var null|string
	 */
	protected ?string $status = null;

	/**
     * Token.
     *
	 * @since 2.0.0 	Same visibility as the parent
     * @since 1.0.0
	 *
	 * @var null|string
	 */
	protected ?string $token = null;

	/**
	 * Endpoint hml/prod.
	 *
	 * @since 2.0.0
	 *
	 * @var null|string
	 */
	protected ?string $url = null;

	/**
	 * Header for api calls.
	 *
	 * @since 2.0.0 	Same visibility as the parent
	 * @since 1.0.0
	 *
	 * @var null|array
	 */
    protected ?array $header = null;

    /**
     * Channel ID.
     *
     * @since 1.0.0
     *
	 * @var null|string
     */
    private ?string $chn_id = null;

    /**
     * Channel Token.
     *
     * @since 1.0.0
     *
	 * @var null|string
     */
    private ?string $chn_tkn = null;

    /**
     * Construct.
     *
     * @since 1.10.0 	Removed $log class property
     * @since 1.0.0
     */
    public function __construct()
    {
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
     * Exec api call.
     *
     * @since 1.0.0
     *
     * @param string  $endpoint
     * @param array   $params
     *
     * @return array
     */
    private function call( string $endpoint, array $params ) : array
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

		$url = $this->url . $endpoint;

		return self::exec( $url, self::HEADER, $params );
    }

    /**
     * Get hash for channel token.
     *
     * @since   1.0.0
     *
     * @param   null|string $uid
     *
     * @return  array
     */
    private function hash( ?string $uid = null ) : array
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
     * Process contact list.
     *
     * @since 1.1.6
     *
     * @param array   $contact
     * @param string  $email
     * @param string  $action
     *
     * @return array
     */
    private function process_contact_list( array $contact, string $email, string $action ) : array
    {
        $key      = array_search( "emailMarketplace", array_column( $contact, "type" ) );
        $verified = false;

        // email marketplace
        if ( $key !== false )
        {
            $verified        = ( $email == $contact[ $key ][ "value" ] )
                                ? ( $contact[ $key ][ "verified" ]
                                    ? true
                                    : false )
                                : false;

            $contact[ $key ] = [
                "type"     => "emailMarketplace",
                "value"    => $email,
                "verified" => $verified,
            ];
        }
        else
        {
            array_push( $contact, [
                "type"     => "emailMarketplace",
                "value"    => $email,
                "verified" => false
            ] );
        }

        if ( in_array( $action, [ "UPDATE", "CREATE" ] ) )
        {
            $key = array_search( "email", array_column( $contact, "type" ) );

            // email
            if ( $key === false )
            {
                array_push( $contact, [
                    "type"     => "email",
                    "value"    => $email,
                    "verified" => $verified
                ] );
            }
        }

        return $contact;
    }

    /**
     * Process contact.
     *
     * @since 1.1.6
     *
     * @param array   $terms
     *
     * @return array
     */
    private function process_terms( array $terms ) : array
    {
        $id   = "-Kzj_F6wDcfHkLMO_q4c";
        $term = [
            "accept"         => true,
            "dateAcceptance" => time() * 1000,
            "optInId"        => $id,
            "type"           => "Termos de Uso da Triibo",
            "version"        => 1
        ];

        if ( count( $terms ) == 0 )
        {
            $terms = [ $term ];
        }
        else
        {
            $key   = array_search( $id, array_column( $terms, "optInId" ) );

            if ( $key !== false )
            {
                $terms[ $key ][ "accept"         ] = true;
                $terms[ $key ][ "dateAcceptance" ] = time() * 1000;
            }
            else
            {
                array_push( $terms, $term );
            }
        }

        return $terms;
    }

    /**
     * Process org action.
     *
     * @since 1.4.2   Removed parameter $action
     * @since 1.3.0
     *
     * @param string  $uid
     * @param string  $org_id
     *
     * @return array
     */
    private function process_org( string $api, string $uid, string $org_id ) : array
    {
        $response   = $this->get_channel_token( self::SYS_UID );

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
        {
            $this->log( "error", json_encode( $response ), true );
            return $response;
        }

        $chn_tkn_id = $response[ "data" ][ "success" ][ "newToken" ][ "channelTokenId" ];

        $params     = [
            "triiboHeader" => array_merge( $this->header, [
                "channelTokenId" => $chn_tkn_id,
                "uId"            => self::SYS_UID,
            ] ),
            "orgID"        => $org_id,
            "uId"          => $uid,
        ];

        $response   = $this->call( $api, $params );

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
            $this->log( "error", json_encode( $response ), true );

        return $response;
    }

    /**
     * Get channel token.
     *
     * @since 1.0.0
     *
     * @param null|string   $uid
     *
     * @return array
     */
    public function get_channel_token( ?string $uid = null ) : array
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
     * Credits points to the user.
     *
     * @since 1.7.0       Added new plans
     * @since 1.1.2       Added param $event
     * @since 1.0.0
     *
     * @param string      $triibo_id
     * @param integer     $value
     * @param string      $event
     *
     * @return array
     */
    public function credit_points( string $triibo_id, int $value, string $event ) : array
    {
        $events = [
            "Compra_Marketplace",
            "Assinatura_Triibo_Vip",
            "Assinatura_Triibo_Vip_Plano_2",
            "Assinatura_Triibo_Vip_Plano_3",

            "Assinatura_Triibo_Vip_Plano_4",
            "Assinatura_Triibo_Vip_Plano_5",
            "Assinatura_Triibo_Vip_Plano_6",
        ];

        if ( !in_array( $event, $events ) )
        {
            $response   = [ "success" => false, "event" => "Not found" ];
        }
        else
        {
            $response   = $this->get_channel_token();

            if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
            {
                $this->log( "error", json_encode( $response ), true );
                return $response;
            }

            $chn_tkn_id = $response[ "data" ][ "success" ][ "newToken" ][ "channelTokenId" ];
            $params     = [
                "triiboHeader"   => array_merge( $this->header, [
                    "channelTokenId" => $chn_tkn_id,
                ] ),
                "triiboId"       => "+{$triibo_id}@sms.triibo.com.br",
                "eventType"      => $event,
                "recursionValue" => $value,
            ];

            $response   = $this->call( self::CREDIT_POINTS, $params );
        }

        return $response;
    }

    /**
     * Update marketplace user.
     *
     * @since 1.5.0   Change param $cellphone to $user
     * @since 1.1.6   Add param $terms
     * @since 1.1.5   ContactList email/emailMarketplace no longer verified
     * @since 1.1.1   Fix array contactList
     * @since 1.1.0   Add params $name and $nick
     * @since 1.0.0
     *
     * @param array         $user
     * @param string        $email
     * @param string        $action
     * @param string        $name
     * @param string        $nick
     * @param null|bool     $terms
     *
     * @return array
     */
    public function update_user_mkt( array $user, string $email, string $action, string $name = "", string $nick = "", ?bool $terms = null ) : array
    {
        // $response = [
        // 	"code"    => $info[ "http_code" ],
        // 	"success" => true,
        // 	"message" => "ok",
        // 	"error"   => null,
        // 	"data"    => json_decode( $response, true ),
        // ];

        $uid        = $user[ "uId" ];
        $contact    = $this->process_contact_list( $user[ "contactList" ], $email, $action );

        $response   = $this->get_channel_token( $uid );

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
        {
            $this->log( "error", json_encode( $response ), true );
            return $response;
        }

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
                "name"        => ( $user[ "name" ] && $user[ "name" ] != "Triiber" ) ? $user[ "name" ] : ( $name ?? "Triiber" ),
                "aliasName"   => ( $user[ "aliasName" ] ) ? $user[ "aliasName" ] : $nick,
                "contactList" => [ ... $contact ],
            ],
        ];

        if ( $terms )
        {
            $user_terms                          = ( isset( $user[ "optInList" ] ) ) ? $user[ "optInList" ] : [];
            $params[ "userInfo" ][ "optInList" ] = [ ... $this->process_terms( $user_terms ) ];
        }

        $response   = $this->call( self::SET_USER_INFO, $params );

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
        {
            $this->log( "error", json_encode( $response ), true );
            return $response;
        }

        $response[ "uid" ] = $uid;

        return $response;
    }

    /**
     * Send code SMS.
     *
     * @since 1.1.0
     *
     * @param string  $cellphone
     *
     * @return array
     */
    public function auth_sms( string $cellphone ) : array
    {
        $response   = $this->get_channel_token();

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
        {
            $this->log( "error", json_encode( $response ), true );
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
            $this->log( "error", json_encode( $response ), true );

        return $response;
    }

    /**
     * Validates code SMS.
     *
     * @since 1.1.0
     *
     * @param string  $code
     * @param string  $cellphone
     * @param string  $trans_id
     *
     * @return array
     */
    public function validate_sms( string $code, string $cellphone, string $trans_id ) : array
    {
        $response   = $this->get_channel_token();

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
        {
            $this->log( "error", json_encode( $response ), true );
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
            $this->log( "error", json_encode( $response ), true );

        return $response;
    }

    /**
     * Get user info.
     *
     * @since 1.1.7   Add $is_uid param, change $cellphone param to $data
     * @since 1.1.0
     *
     * @param string  $data
     *
     * @return array
     */
    public function user_info( string $data, bool $is_uid = false ) : array
    {
        $response   = $this->get_channel_token();

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
        {
            $this->log( "error", json_encode( $response ), true );
            return $response;
        }

        $chn_tkn_id = $response[ "data" ][ "success" ][ "newToken" ][ "channelTokenId" ];

        if ( $is_uid )
        {
            $user_info = [ "uId" => $data ];
        }
        else
        {
            $user_info = [
                "contactList" => [
                    [
                        "type"  => "cellPhone",
                        "value" => "+" . preg_replace( "/\D/", "", $data )
                    ]
                ]
            ];
        }

        $params     = [
            "triiboHeader"    => array_merge( $this->header, [
                "channelTokenId" => $chn_tkn_id
            ] ),
            "queryPartnerAPI" => [ "getUserInfo" ],
            "userInfo"        => $user_info,
        ];

        $response   = $this->call( self::GET_USER_INFO, $params );

        if ( !$response[ "success" ] || ( isset( $response[ "data" ] ) && !$response[ "data" ][ "success" ] ) )
            $this->log( "error", json_encode( $response ), true );

        return $response;
    }

    /**
     * Add org to user.
     *
     * @since 1.3.0
     *
     * @param string  $uid
     * @param string  $org_id
     *
     * @return array
     */
    public function add_org( string $uid, string $org_id ) : array
    {
        return $this->process_org( self::ADD_USER_ORG, $uid, $org_id );
    }

    /**
     * Remove org from user.
     *
     * @since 1.3.0
     *
     * @param string  $uid
     * @param string  $org_id
     *
     * @return array
     */
    public function del_org( string $uid, string $org_id ) : array
    {
        return $this->process_org( self::DEL_USER_ORG, $uid, $org_id );
    }
}
