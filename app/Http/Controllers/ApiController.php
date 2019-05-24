<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    /**
     * The request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * Create a new controller instance.
     * @param Request $request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function getLoggedInUser()
    {
        $token = $this->request->header('Authorization');

        if ($token) {
            return JWT::decode(substr($token, 6), env('JWT_SECRET'), ['HS256']);
        }
    }
}
