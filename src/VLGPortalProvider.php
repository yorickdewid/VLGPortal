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
    // private $api_url = 'http://localhost:7070';
    private $api_url = 'https://portal.rotterdam-vlg.com';

    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    private $api_version = 'v1';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase($this->api_url . '/login');
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken()
    {
        $userUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/user?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        $response = $this->getHttpClient()->get(
            $userUrl
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdminByToken()
    {
        $adminUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/user_isadmin?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        $response = $this->getHttpClient()->get($adminUrl);

        return json_decode($response->getBody(), true);
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

        $response = $this->getHttpClient()->get($companyUrl);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUsers()
    {
        $usersUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/users?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        $response = $this->getHttpClient()->get($usersUrl);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompanies()
    {
        $usersUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/companies?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        $response = $this->getHttpClient()->get($usersUrl);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompanyUsers($id)
    {
        $usersUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/company/' . $id . '/users?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        $response = $this->getHttpClient()->get($usersUrl);

        return json_decode($response->getBody(), true);
    }

}
