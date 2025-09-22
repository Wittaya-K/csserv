<?php

namespace App\Services;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class PSUOAuthProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = ['profilepsu'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(config('services.psu.url') . '/?oauth=authorize', $state);
    }

    protected function getTokenUrl()
    {
        return config('services.psu.url') . '/oauth/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(config('services.psu.url') . '/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'username' => $user['username'] ?? null,
        ]);
    }
}
