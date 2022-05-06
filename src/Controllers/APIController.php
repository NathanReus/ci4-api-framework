<?php

namespace NathanReus\CI4APIFramework\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use Psr\Log\LoggerInterface;
use NathanReus\CI4APIFramework\Config\API as APIConfig;
use \Config\Services;

class APIController extends Controller
{
    use ResponseTrait;
    
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['Myth\Auth\auth', 'NathanReus\CI4APIFramework\array'];

    /**
     * The Myth Auth authentication service
     */
    protected $auth;

    /**
	 * @var APIConfig
	 */
	protected $apiConfig;

    /**
     * Used to store user input, whether from POST data or JSON in the body.
     */
    protected $input;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();

        // Every API request needs this for the user object
        $this->auth = service('authentication');
        $this->apiConfig = config('API');

        // Retrieve user input, whether from POST or JSON.
        if ($this->apiConfig->permitPostData)
        {
            if (count($request->getPost()) > 0) {
                $this->input = arrayToObject($request->getPost());
            }
        }
        
        if (empty($this->input))
        {
            // There was no POST data in the request, let's get JSON body
            try {
                $this->input = $request->getJSON();
            } catch(\Exception $e) {
                $this->failValidationError('No data supplied.');
            }
        }
    }

    /**
     * Override the validate method provided by the system's Controller class for API calls.
     * This allows for running validation rules against a JSON body rather than just POST data.
     */
    protected function validate($rules, array $messages = []): bool
    {
        $this->validator = Services::validation();

        // If you replace the $rules array with the name of the group
        if (is_string($rules)) {
            $validation = config('Validation');

            // If the rule wasn't found in the \Config\Validation, we
            // should throw an exception so the developer can find it.
            if (! isset($validation->{$rules})) {
                throw ValidationException::forRuleNotFound($rules);
            }

            // If no error message is defined, use the error message in the Config\Validation file
            if (! $messages) {
                $errorName = $rules . '_errors';
                $messages  = $validation->{$errorName} ?? [];
            }

            $rules = $validation->{$rules};
        }

        return $this->validator->setRules($rules, $messages)->run(objectToArray($this->input));
    }
}
