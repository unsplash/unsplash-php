<?php

namespace Crew\Unsplash;

class Credential
{
	const SESSION_BASE_NAME = 'UnsplashWrapper';

	public function __construct($params = [])
	{
		self::startSession();

		if (! empty($params)) {
			foreach ($params as $key => $value) {
				$_SESSION[self::SESSION_BASE_NAME][$key] = $value;
			}
		}
	}

	public function __get($key)
	{
		return isset($_SESSION[self::SESSION_BASE_NAME][$key]) ? $_SESSION[self::SESSION_BASE_NAME][$key] : null;
	}

	public function __set($key, $value)
	{
		$_SESSION[self::SESSION_BASE_NAME][$key] = $value;

		return $this;
	}

	public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            // session_start();
        }
    }

    public function toArray()
    {
    	return $_SESSION[self::SESSION_BASE_NAME];
    }
}