<?php

namespace VLG\GSSAuth;

use Illuminate\Http\Request;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use VLG\GSSAuth\Contracts\Provider as ProviderContract;

abstract class AbstractProvider implements ProviderContract
{
    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The client ID.
     *
     * @var string
     */
    protected $publicToken;

    /**
     * The client secret.
     *
     * @var string
     */
    protected $privateToken;

    /**
     * The redirect URL.
     *
     * @var string
     */
    protected $jwtToken;

    /**
     * The type of the encoding in the query.
     *
     * @var int Can be either PHP_QUERY_RFC3986 or PHP_QUERY_RFC1738.
     */
    protected $encodingType = PHP_QUERY_RFC1738;

    /**
     * Create a new provider instance.
     *
     * @param  Request  $request
     * @param  string  $publicToken
     * @param  string  $privateToken
     * @return void
     */
    public function __construct(Request $request, $publicToken, $privateToken)
    {
        $this->request = $request;
        $this->publicToken = $publicToken;
        $this->privateToken = $privateToken;
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string
     * @return string
     */
    abstract protected function getAuthUrl();

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    abstract protected function getUserByToken();

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array  $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->map([
            'id' => $user['user']['id'],
            'name' => $user['user']['name'],
            'last_name' => $user['user']['last_name'],
            'email' => $user['user']['email'],
            'phone' => $user['user']['phone'],
            'mobile' => $user['user']['mobile'],
            'isActive' => $user['user']['active'],
            'created' => $user['user']['created_at'],
            'updated' => $user['user']['updated_at'],
        ]);
    }

    /**
     * Redirect the user of the application to the provider's authentication screen.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect()
    {
        if ($this->isTokenValid()) {
            throw new InvalidStatusException;
        }

        return new RedirectResponse($this->getAuthUrl());
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $url
     * @param  string
     * @return string
     */
    protected function buildAuthUrlFromBase($url)
    {
        return $url . '?' . http_build_query($this->getCodeFields(), '', '&', $this->encodingType);
    }

    /**
     * Get the GET parameters for the code request.
     *
     * @param  string
     * @return array
     */
    protected function getCodeFields()
    {
        return $fields = [
            'endpoint' => $_SERVER['SERVER_NAME'],
            'token' => $this->publicToken,
            'timestamp' => time(),
            'auth' => 'jwtgssauth',
            'origin' => $_SERVER['REMOTE_ADDR'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->jwtToken = new JwtToken($this->getRawToken());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if (!$this->isTokenValid()) {
            throw new InvalidStatusException;
        }

        $user = $this->mapUserToObject($this->getUserByToken());

        return $user;
    }

    /**
     * Get the code from the request.
     *
     * @return string
     */
    protected function getRawToken()
    {
        return $this->request->get('token');
    }

    /**
     * Get the code from the request.
     *
     * @return string
     */
    public function token()
    {
        return $this->jwtToken;
    }

    /**
     * Get a fresh instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        return new \GuzzleHttp\Client;
    }

    /**
     * Set the request instance.
     *
     * @param  Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Determine if the provider is operating as stateless.
     *
     * @return bool
     */
    public function isTokenValid()
    {
        if (!$this->jwtToken) {
            return false;
        }

        return $this->jwtToken->isValid();
    }
}
