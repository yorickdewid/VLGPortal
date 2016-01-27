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
    private $api_url = 'https://www.rotterdam-vlg.com';

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
     * Get the email for the given access token.
     *
     * @param  string  $token
     * @return string|null
     */
    protected function getCompanyByToken($token)
    {
        $companyUrl = $this->api_url . '/api/endpoint/' . $this->api_version . '/user_company?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

        try {
            $response = $this->getHttpClient()->get(
                $companyUrl, $this->getRequestOptions()
            );
        } catch (Exception $e) {
            return;
        }

        /*foreach (json_decode($response->getBody(), true) as $email) {
            if ($email['primary'] && $email['verified']) {
                return $email['email'];
            }
        }*/
    }

}
