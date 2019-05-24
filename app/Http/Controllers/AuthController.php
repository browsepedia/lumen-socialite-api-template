<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\User as User;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends ApiController
{
    /**
     * @param User $user
     *
     * @return string
     */
    protected function createJwt(User $user)
    {
        $payload = [
            'iss' => env('JWT_ISSUER'),
            'sub' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24,
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @return mixed
     *
     * @throws ValidationException
     */
    public function authenticate()
    {
        $this->validate($this->request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::query()->where('email', $this->request->input('email'))->first();

        if (!$user) {
            return response()->json(['error' => 'No email found'], 404);
        }

        if (Hash::check($this->request->get('password'), $user->password)) {
            $user->token = $this->createJwt($user);
            $user->lastLogin = time();
            $user->save();

            return response()->json($this->createLoginResponse($user), 200);
        }

        return response()->json(['error' => 'Email or password is incorrect'], 400);
    }

    /**
     * Register a new user and return the token.
     *
     * @return mixed
     *
     * @throws ValidationException
     */
    public function register()
    {
        $data = $this->request->all();

        $this->validate($this->request, [
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            return response()->json(['error' => 'An user with this email address already exists'], 401);
        }

        $user = new User();
        $user->fill($data);

        $user->password = Hash::make($this->request->get('password'), array('rounds' => 12));
        $user->token = $this->createJwt($user);
        $user->lastLogin = Date::now();
        $user->provider = 'register';

        $imageUrl = str_replace(' ', '+', $user->name);
        $user->imageUrl = 'https://ui-avatars.com/api/?name=' . $imageUrl;
        $user->save();

        return response()->json($this->createLoginResponse($user), 200);
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return Response
     */
    public function provider()
    {
        return Socialite::driver($this->request->query('provider'))->stateless()->redirect();
    }

    /**
     * Callback after authentication
     *
     * @return Response
     */
    public function callback()
    {
        $user = $this->createUserFromProvider($this->request->query('provider'));
        return response()->json($this->createLoginResponse($user));
    }

    public function reset()
    {
        return response()->json('allowed');
    }

    /**
     * Create user from social login
     *
     * @param string $provider
     *
     * @return User $user
     */
    private function createUserFromProvider(string $provider)
    {
        $providerUser = Socialite::driver($provider)->stateless()->user();
        $user = new User();

        $user->fill([
            'name' => $providerUser->name,
            'email' => $providerUser->email
        ]);
        $user->imageUrl = $providerUser->avatar;
        $user->lastLogin = Date::now();
        $user->provider = $provider;
        $user->token = $this->createJwt($user);

        $user->save();
        return $user;
    }

    private function createLoginResponse(User $user)
    {
        return (object)[
            'name' => $user->name,
            'email' => $user->email,
            'image' => $user->imageUrl,
            'token' => $user->token
        ];
    }
}
