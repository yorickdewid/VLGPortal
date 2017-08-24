<?php

namespace VLG\GSSAuth;

use Exception;
use VLG\GSSAuth\Contracts\Provider as ProviderContract;

class VLGPortalProvider extends AbstractProvider implements ProviderContract
{
    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    private $api_url = 'https://portal.rotterdam-vlg.com';

    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    private $api_version = 'v1';

    /**
     * Provider options.
     *
     * @var Request
     */
    private $options = [];

    /**
     * {@inheritdoc}
     */
    protected function setOptions(array $options)
    {
        return $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function setAuthUrl($host)
    {
        return $this->api_url = $host;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase($this->api_url . '/login');
    }

    /**
     * Get the url for the given access token.
     *
     * @param  string  $token
     * @return string|null
     */
    protected function performRequest($url)
    {
        $request_options = [];

        if (array_key_exists('ssl_verify', $this->options)) {
            if ($this->options['ssl_verify'] === false) {
                $request_options['verify'] = false;
            }
        }

        $response = $this->getHttpClient()->get($url, $request_options);
        if ($response->getStatusCode() != 200) {
            throw new InvalidStatusException;
        }

        $response_object = json_decode($response->getBody(), true);
        if (is_array($response_object)) {
            if (isset($response_object[0])) {
                if ($response_object[0] == 'application_invalid') {
                    throw new InvalidStatusException;
                }
            }
        }

        return $response_object;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken()
    {
        $userUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/user?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        return $this->performRequest($userUrl);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdminByToken()
    {
        $adminUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/user_isadmin?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        return $this->performRequest($adminUrl);
    }

    /**
     * Get the email for the given access token.
     *
     * @param  string  $token
     * @return string|null
     */
    protected function getCompanyByToken($token)
    {
        $companyUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/user_company?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        return $this->performRequest($companyUrl);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUsers()
    {
        $usersUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/users?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        return $this->performRequest($usersUrl);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompanies()
    {
        $companiesUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/companies?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        return $this->performRequest($companiesUrl);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompanyUsers($id)
    {
        $usersUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/company/' . $id . '/users?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        return $this->performRequest($usersUrl);
    }

}
