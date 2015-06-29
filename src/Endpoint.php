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
	 * List of accepted http action that the application can execute
	 * @var array
	 */
	private static $acceptedHttpMethod = ['get', 'post', 'put'];

	/**
	 * Construct an new endpoint object and set the parameters
	 * from an array
	 * 
	 * @param array
	 */
	public function __construct($parameters = [])
	{
		// Cast array in case it's a stdClass
		$this->parameters = (array)$parameters;
	}

	/**
	 * Merge the old parameters with the new one
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
	 * Validate if the http method is accepted and send a http request to it.
	 * Retrieve error from the request and throw a new error
	 * 
	 * @param  string $method Http action to trigger
	 * @param  array $arguments Array containing all the parameters pass to the magic method
	 * 
	 * @throws Crew\Unsplash\Exception if the http request failed
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
			if (! self::goodRequest($response)) {
				throw new Exception(self::getErrorMessage($response), $response->getStatusCode());
			}

			return $response->getBody();
		}
	}

	/**
	 * Retrieve the response status code and determine if the request was a success or not
	 * 
	 * @param  GuzzleHttp\Psr7\Response $response of the http request
	 * @return boolean
	 */
	public static function goodRequest($response)
	{
		return $response->getstatusCode() >= 200 && $response->getstatusCode() < 300;
	}

	/**
	 * Retrieve the error message in the body
	 * 
	 * @param  GuzzleHttp\Psr7\Response $response of the http request
	 * @return string
	 */
	public static function getErrorMessage($response)
	{
		$message = json_decode($response->getBody(), true);
		if (is_array($message) && isset($message['error'])) {
			$message = $message['error'];
		}

		return $message;
	}
}