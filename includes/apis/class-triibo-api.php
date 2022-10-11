<?php
/**
 * The file that defines the Triibo API
 *
 * A class definition that includes attributes and functions used for access the Triibo API.
 *
 * @since      1.0.0
 * @package    Triibo_Api_Services
 * @subpackage Triibo_Api_Services/includes/apis
 * @author     Mateus Costa <mateus@costamateus.com.br>
 */

class Triibo_Api
{
	/**
	 * Make request to the API
	 *
	 * @param string 	$endpoint 	Endpoint url
	 * @param array 	$header		Header of request
	 * @param array 	$params 	Body of request
	 * @return array
	 */
    public static function exec( string $endpoint, array $header, array $params = [] )
    {
		$curl     = curl_init();

		if ( !$curl )
		{
			return [
				"code"    => "500.1",
				"success" => false,
				"message" => "Falha na comunicação com a API.",
				"error"   => "Couldn't initialize a cURL handle",
			];
		}

        $params   = ( !empty( $params ) ) ? json_encode( $params ) : null;

		curl_setopt_array( $curl, [
			CURLOPT_URL            => $endpoint,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 0,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_SSL_VERIFYPEER => TRUE,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_POSTFIELDS     => $params,
			CURLOPT_HTTPHEADER     => $header,
        ] );

        $response = curl_exec( $curl );

		if ( empty( $response ) )
		{
			// some kind of an error happened
			$err      = curl_error( $curl );
			$response = [
				"code"    => "500.2",
				"success" => false,
				"message" => "Falha na comunicação com a API.",
				"error"   => $err,
				"data"    => null,
			];
		}
		else
		{
			$info = curl_getinfo( $curl );

			if ( empty( $info[ "http_code" ] ) )
			{
				$response = [
					"code"    => "500.2",
					"success" => false,
					"message" => "Falha na comunicação com a API.",
					"error"   => "No HTTP code was returned.",
					"data"    => json_decode( $response, true ),
				];
			}
			else if ( $info[ "http_code" ] < 200 || $info[ "http_code" ] >= 300 )
			{
				$response = [
					"code"    => $info[ "http_code" ],
					"success" => false,
					"message" => "Falha na comunicação com a API.",
					"error"   => self::HTTP_CODES[ $info[ "http_code" ] ],
					"data"    => json_decode( $response, true ),
				];
			}
			else
			{
				$response = [
					"code"    => $info[ "http_code" ],
					"success" => true,
					"message" => "ok",
					"error"   => null,
					"data"    => json_decode( $response, true ),
				];
			}
		}

        curl_close( $curl );

		return $response;
    }
}