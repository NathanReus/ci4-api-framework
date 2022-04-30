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
    protected $helpers = ['Myth\Auth\auth'];

    /**
     * The Myth Auth authentication service
     */
    protected $auth;

    /**
	 * @var APIConfig
	 */
	protected $apiConfig;

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
    }
}
