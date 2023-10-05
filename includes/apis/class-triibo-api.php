<?php
/**
 * The file that defines the Triibo API
 *
 * A class definition that includes attributes and functions used for access the Triibo API.
 *
 * @author 		Mateus Costa <mateus@costamateus.com.br>
 * @package 	Triibo_Api_Services/Classes/Api
 * @version 	1.0.0
 */
defined( "ABSPATH" ) || exit;

class Triibo_Api
{
	/**
	 * Http codes
	 *
	 * @since 	1.0.0
	 * @var 	array
	 */
	const HTTP_CODES = [
		// [Informational 1xx]
		100 => "Continue",
		101 => "Switching Protocols",

		// [Successful 2xx]
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		203 => "Non-Authoritative Information",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",

		// [Redirection 3xx]
		300 => "Multiple Choices",
		301 => "Moved Permanently",
		302 => "Found",
		303 => "See Other",
		304 => "Not Modified",
		305 => "Use Proxy",
		306 => "(Unused)",
		307 => "Temporary Redirect",

		// [Client Error 4xx]
		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Timeout",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Request Entity Too Large",
		414 => "Request-URI Too Long",
		415 => "Unsupported Media Type",
		416 => "Requested Range Not Satisfiable",
		417 => "Expectation Failed",

		// [Server Error 5xx]
		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Timeout",
		505 => "HTTP Version Not Supported",
	];

	/**
	 * Make request to the API
	 *
	 * @since 	1.4.0 	Added $method parameter
	 * @since 	1.0.0
	 * @param 	string 	$endpoint 	Endpoint url
	 * @param 	array 	$header		Header of request
	 * @param 	array 	$params 	Body of request
	 * @param 	string 	$method 	Method of request
	 * @return 	array
	 */
    public static function exec( string $endpoint, array $header, array $params = [], string $method = "POST" )
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
			CURLOPT_CUSTOMREQUEST  => $method,
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
					"code"    => "500.3",
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