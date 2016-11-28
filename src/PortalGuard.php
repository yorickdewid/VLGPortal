<?php

namespace VLG\GSSAuth;

use \Portal;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;

class PortalGuard implements Guard {

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function authenticateSSO()
    {
        return Portal::driver('vlgportal')->redirect();
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function callback()
    {
        try {
            $portal = Portal::driver('vlgportal')->handle();
            if ($portal->isTokenValid()) {
                $user = $portal->user();
                $token = $portal->token();

                if ($user && $token) {
                    session()->set('portaluser', $portal->user());
                    session()->set('portaltoken', $portal->token());

                    return true;
                }
            }
        } catch (Exception $e) {
            session()->forget('portaluser');
            session()->forget('portaltoken');
            return false;
        } catch (InvalidStatusException $e) {
            session()->forget('portaluser');
            session()->forget('portaltoken');
            return false;
        }

        return false;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return void
     */
    public function logout()
    {
        session()->flush();
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        if (!session()->has('portaltoken')) {
            return false;
        }
        
        if (!session()->get('portaltoken')->isValid()) {
            return false;
        }

        if (!session()->has('portaluser')) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return session()->get('portaluser');
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function token()
    {
        return session()->get('portaltoken');
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->check()) {
            return session()->get('portaluser')->id;
        }
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return false;
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        // 
    }

}