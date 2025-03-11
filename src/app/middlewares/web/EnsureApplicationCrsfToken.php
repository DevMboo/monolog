<?php

namespace Monolog\App\Middlewares\Web;

use Monolog\App\Http\Request;
use Monolog\App\Helpers\Crsf\Crsf;

use Monolog\Exceptions\Exception;

/**
 * Middleware to ensure that POST requests contain a valid CSRF token.
 */
class EnsureApplicationCrsfToken
{
    /**
     * Request instance.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Handles CSRF token validation.
     *
     * @throws Exception If the CSRF token is invalid or missing in POST requests.
     * @return bool Returns true if validation is successful.
     */
    public function handle(): bool
    {
        try {
            if ($this->request->method() === 'POST') {
                
                $csrfToken = $this->request->input('csrf_token');

                if (!$csrfToken || !(new Crsf)->validateToken($this->request->input('csrf_token'))) {
                    throw new Exception("Invalid or missing CSRF token", 400);
                }
            }
            
            return true;
        } catch (Exception $e) {
            echo $e->render();
            exit;
        }
    }
}