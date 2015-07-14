<?php

namespace Crew\Unsplash;

class Endpoint
{
	/**
	 * All parameters that an endpoint can have
	 * @var array
	 */
	private $parameters;

	/**
	 * List of accepted http actions
	 * @var array
	 */
	private static $acceptedHttpMethod = ['get', 'post', 'put'];

	/**
	 * Construct a new endpoint object and set the parameters from an array
	 * 
	 * @param array
	 */
	public function __construct($parameters = [])
	{
		// Cast array in case it's a stdClass
		$this->parameters = (array)$parameters;
	}

	/**
	 * Merge old parameters with the new one
	 * 
	 * @param  Array $parameter The parameters to update on the object
	 * @return void
	 */
	public function update(Array $parameters)
	{
		$this->parameters = array_merge($this->parameters, (array)$parameters);
	}

	/**
	 * Magic method to retrieve a specific parameter in the parameters array
	 * 
	 * @param  string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->parameters[$key];
	}

	/**
	 * Check if the HTTP method is accepted and send a HTTP request to it.
	 * Retrieve error from the request and throw a new error
	 * 
	 * @param  string $method HTTP action to trigger
	 * @param  array $arguments Array containing all the parameters pass to the magic method
	 * 
	 * @throws Crew\Unsplash\Exception if the HTTP request failed
	 *
	 * @see Crew\Unsplash\HttpClient::send()
	 * 
	 * @return string
	 */
	public static function __callStatic($method, $arguments)
	{
		//  Validate if the $method is part of the accepted http method array
		if (in_array($method, self::$acceptedHttpMethod)) {
			$httpClient = new HttpClient();

			$response = $httpClient->send($method, $arguments);

			//  Validate if the request failed
			if (! self::isGoodRequest($response)) {
				throw new Exception(self::getErrorMessage($response), $response->getStatusCode());
			}

			return $response;
		}
	}

	protected static function getArray($responseBody, $object)
	{
		return array_map(function ($array) use($object) {
			return new $object($array);
		}, json_decode($responseBody, true));
	}

	/**
	 * Retrieve the response status code and determine if the request was successful.
	 * 
	 * @param  GuzzleHttp\Psr7\Response $response of the HTTP request
	 * @return boolean
	 */
	private static function isGoodRequest($response)
	{
		return $response->getstatusCode() >= 200 && $response->getstatusCode() < 300;
	}

	/**
	 * Retrieve the error message in the body
	 * 
	 * @param  GuzzleHttp\Psr7\Response $response of the HTTP request
	 * @return string
	 */
	private static function getErrorMessage($response)
	{
		$message = json_decode($response->getBody(), true);
		$errors = [];

		if (is_array($message) && isset($message['errors'])) {
			$errors = $message['errors'];
		}

		return $errors;
	}
}