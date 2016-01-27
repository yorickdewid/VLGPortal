<?php

namespace VLG\GSSAuth;

class JwtToken implements Contracts\Token
{
    /**
     * The redirect URL.
     *
     * @var string
     */
    protected $rawToken;

    /**
     * Create a new provider instance.
     *
     * @param  Request  $request
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  string  $redirectUrl
     * @return void
     */
    public function __construct($raw)
    {
    	$this->parseTokenString($raw);
    }

    public function __toString()
    {
        return $this->rawToken;
    }

    public function parseTokenString($raw)
    {
    	$this->rawToken = $raw;

    	$parts = explode(".", $raw);
    	if (count($parts) != 3){
    		// thow something
    	}

    	$header = base64_decode($parts[0]);
    	$payload = base64_decode($parts[1]);

    	$this->parseHeader($header);
    	$this->map(json_decode($payload));
    }

    private function parseHeader($payload)
    {
    	// TODO
    }

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function isValid()
    {
    	return $this->exp > time();
    }

    /**
     * Map the given array onto the user's properties.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function map($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }
}
