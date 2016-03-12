<?php

namespace VLG\GSSAuth;

use Exception;
use Illuminate\Support\Facades\Cache;
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
     * The type of the encoding in the query.
     *
     * @var int Can be either PHP_QUERY_RFC3986 or PHP_QUERY_RFC1738.
     */
    protected $cacheLifetime = 1440;

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
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    abstract protected function getAdminByToken();

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    abstract protected function getUsers();

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    abstract protected function getCompanies();

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    abstract protected function getCompanyUsers($id);

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
            'canRead' => $user['user']['app_read'],
            'canWrite' => $user['user']['app_write'],
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

        if ($this->jwtToken->pub != $this->publicToken) {
            throw new Exception("Token invalid for application");
        }

        if ($this->jwtToken->app != $_SERVER['SERVER_NAME']) {
            throw new Exception("Token invalid for application");
        }

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

        if (!isset($user->id)) {
            throw new Exception("No valid user");
        }

        $isadmin = $this->getAdminByToken();
        if ($isadmin['isadmin']) {
            $user->makeAdmin();
        }        

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
     * Get the code from the request.
     *
     * @return string
     */
    public function setToken($token)
    {
        return $this->jwtToken = $token;
    }

    /**
     * Get the code from the request.
     *
     * @return string
     */
    public function portalUsers()
    {
        $users = Cache::get('portal_users', function() {
            $new_users = $this->getUsers();
            if (!$new_users) {
                throw new InvalidStatusException;
            }

            Cache::put('portal_users', $new_users, $this->cacheLifetime);
            return $new_users;
        });

        return $users;
    }

    /**
     * Get the code from the request.
     *
     * @return string
     */
    public function portalCompanies()
    {
        $companies = Cache::get('portal_companies', function() {
            $new_companies = $this->getCompanies();
            if (!$new_companies) {
                throw new InvalidStatusException;
            }

            Cache::put('portal_companies', $new_companies, $this->cacheLifetime);
            return $new_companies;
        });

        return $companies;
    }

    /**
     * Get the code from the request.
     *
     * @return string
     */
    public function portalCompanyUsers($id)
    {
        $users = Cache::get('portal_company_users_' . $id, function() use ($id) {
            $new_users = $this->getCompanyUsers($id);
            if (!$new_users) {
                throw new InvalidStatusException;
            }

            Cache::put('portal_company_users_' . $id, $new_users, $this->cacheLifetime);
            return $new_users;
        });

        return $users;
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
