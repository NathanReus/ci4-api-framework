<?php

use Myth\Auth\Models\UserModel;
use NathanReus\CI4APIFramework\Models\RefreshTokenModel;
use NathanReus\CI4APIFramework\Entities\RefreshToken;
use Config\Services;
use Config\Auth;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;

function getJWTFromRequest($authenticationHeader): string
{
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }
    //JWT is sent from client in the format Bearer XXXXXXXXX
    return explode(' ', $authenticationHeader)[1];
}

function validateAccessToken(string $encodedToken)
{
    $publicKey = new Key(Services::getPublicKey(), 'RS256');
    $decodedToken = JWT::decode($encodedToken, $publicKey);
    $userModel = new UserModel();
    $user = $userModel->where('email', $decodedToken->email)->first();
    if ($user == null) 
    {
        // Wasn't able to find the user, throw an error
        throw new Exception('User does not exist for specified email');
    }
}

function validateRefreshToken(string $encodedToken)
{
    $publicKey = new Key(Services::getPublicKey(), 'RS256');
    $decodedToken = JWT::decode($encodedToken, $publicKey);
    $userModel = new UserModel();
    $user = $userModel->where('email', $decodedToken->email)->first();
    if ($user == null) 
    {
        // Wasn't able to find the user, throw an error
        throw new Exception('User does not exist for specified email');
    }

    try {
        $family = $decodedToken->family;
    } catch (Exception $exception) {
        throw new Exception('Not a valid refresh token.');
    }
    
    $issuedAtTime = $decodedToken->iat;

    // Verify that this is a valid refresh token and is the latest one in its family
    $refreshTokenModel = new RefreshTokenModel();
    $refreshToken = $refreshTokenModel
                        ->where('user_id', $user->id)
                        ->where('family', $family)
                        ->first();

    if ($refreshToken == null)
    {
        // Wasn't able to find the token, throw an error
        throw new Exception('Not a valid refresh token.');
    }

    // Put the user's email into the token object for now (isn't saved to DB) - needed in the response for calling method
    $refreshToken->email = $user->email;

    if ($refreshToken->issued_at != $issuedAtTime)
    {
        // Valid token, except the issued at time is different, means this is a stolen token being reused
        $refreshTokenModel->delete($refreshToken->id);
        throw new Exception('Not a valid refresh token.');
    }

    // Token is a legitimate refresh token, pass it back
    return $refreshToken;
}

function getJWTForUser(string $email, int $tokenTimeToLive, bool $isRefresh = false, string $family = '')
{
    $issuedAtTime = time();
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $type = $isRefresh ? 'refresh' : 'access';
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
        'type' => $type,
    ];

    if ($isRefresh)
    {
        $payload['family'] = $family;

        // Check whether this one already exists in the DB
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();
        $refreshTokenModel = new RefreshTokenModel();
        $refreshToken = $refreshTokenModel
                        ->where('user_id', $user->id)
                        ->where('family', $family)
                        ->first();

        if ($refreshToken == null)
        {
            // Wasn't able to find the token, create a new one
            $refreshToken = new RefreshToken;
            $refreshToken->user_id = $user->id;
            $refreshToken->family = $family;
            
        }

        // Set the issued at time and save to DB (whether newly created or existing record)
        $refreshToken->issued_at = $issuedAtTime;
        $refreshTokenModel->save($refreshToken);
    }

    $token = JWT::encode($payload, Services::getPrivateKey(), 'RS256');
    return $token;
}

function getTokensForUser(string $email, $refreshTokenFamily) {
    try {
        $config = config('API');
        
        $tokens = [
            'access' => getJWTForUser($email, $config->accessTokenTime),
            'refresh' => getJWTForUser($email, $config->refreshTokenTime, true, $refreshTokenFamily),
        ];

        return $tokens;
    } catch (Exception $exception) {
        return false;
    }
}