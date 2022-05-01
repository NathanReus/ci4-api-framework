<?php

namespace NathanReus\CI4APIFramework\Controllers;

use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;
use RandomLib\Factory;
use Exception;

class AuthController extends APIController
{

	public function __construct()
	{

	}

	//--------------------------------------------------------------------
	// Login/out
	//--------------------------------------------------------------------

	/**
	 * Attempts to verify the user's credentials
	 * through a POST request.
	 */
	public function attemptLogin()
	{
		// Configure the validation rules for the fields
		// TODO: Change this back to user email or username like Myth Auth
        $rules = [
            'email'     => 'required|valid_email',
            'password'  => 'required|min_length[' . $this->apiConfig->passwordMinLength . ']|max_length[' . $this->apiConfig->passwordMaxLength . ']',
        ];

        // Run the rules and return a failure if there's any errors
		// TODO: Update error message to include username
        if (! $this->validate($rules))
		{
            return $this->failUnauthorized('Please provide a valid email address and password.');
		}

        // Get the submitted values
		// TODO: Update for username
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = false;

        // Attempt to auth and return a failure if there's any errors
		// TODO: Update for username
        if (! $this->auth->attempt(['email' => $email, 'password' => $password], $remember))
        {
            return $this->failUnauthorized('Invalid login credentials provided.');
        }

        // Check if the user is being forced to change their password
        if ($this->auth->user()->force_pass_reset === true)
        {
            // TODO: Update link to a valid page to reset password
            return $this->failUnauthorized('Your password must be changed. Please visit XXXX in your browser.');
        }

        // Login successful, return the required tokens
		return $this->getTokens($email);
	}

	/**
	 * Log the user out.
	 */
    public function logout() {
		helper('jwt');
		$refreshToken = validateRefreshToken(getJWTFromRequest($this->request->getServer('HTTP_AUTHORIZATION')));
		deleteRefreshTokenFamily($refreshToken);

		return $this->respondNoContent();
    }

	/**
	 * Request a new access token, using a refresh token
	 */
	public function refreshToken() {
	
		$authenticationHeader = $this->request->getServer('HTTP_AUTHORIZATION');

        try {

            helper('jwt');
            $encodedToken = getJWTFromRequest($authenticationHeader);
            
			$refreshToken = validateRefreshToken($encodedToken);

			return $this->getTokens($refreshToken->email, $refreshToken->family);

        } catch (Exception $e) {
            
			return $this->failUnauthorized($e->getMessage());

        }

	}

	protected function getTokens($email, $refreshTokenFamily = null) {
		helper('jwt');
        if ($refreshTokenFamily === null)
		{
			// No existing Refresh Token Family has been passed in, let's make a new one
			$randomFactory = new Factory;
			$randomGenerator = $randomFactory->getLowStrengthGenerator();
			$refreshTokenFamily = $randomGenerator->generateString($this->apiConfig->refreshTokenFamilyHashLength, $this->apiConfig->refreshTokenFamilyCharacters);
		}

		$tokens = getTokensForUser($email, $refreshTokenFamily);

		if ($tokens === false)
		{
			return $this->failServerError('Error while generating tokens.');
		}

		// Get the user's ID to put into the response
		$userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        return $this->respond(
            [
                'message' => 'User authenticated successfully',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
                'tokens' => $tokens,
            ]
        );
	}
}
