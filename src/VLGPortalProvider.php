<?php

namespace VLG\GSSAuth;

use Exception;
use VLG\GSSAuth\Contracts\Provider as ProviderContract;

class VLGPortalProvider extends AbstractProvider implements ProviderContract
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase('https://www.rotterdam-vlg.com/login');
        // return $this->buildAuthUrlFromBase('http://localhost:7070/login');
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken()
    {
        $userUrl = 'https://www.rotterdam-vlg.com/api/endpoint/user?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;
        // $userUrl = 'http://localhost:7070/api/endpoint/user?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

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
        $companyUrl = 'https://www.rotterdam-vlg.com/api/endpoint/user_company?token=' . $this->jwtToken . '&privkey=' . $this->privateToken;

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
