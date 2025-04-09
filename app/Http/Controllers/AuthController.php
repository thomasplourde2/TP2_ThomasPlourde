<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use App\Http\Resources\UserResource;

 /** * @OA\Info(title="Films API", version="0.1") */

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

/**
    *@OA\Post(
    *path="/api/signup",
    *tags={"Register"},
    *summary="Register a user",
    *@OA\Response(
        * response = 201,
        * description = "Created"),
        * @OA\RequestBody(
            * @OA\MediaType(
            * mediaType="application/json",
            * @OA\Schema(
                * @OA\Property(
                    * property="login",
                    * type="string"
                * ),
                * @OA\Property(
                    * property="password",
                    * type="string"
                * ),
                * @OA\Property(
                    * property="email",
                    * type="string"
                * ),
                * @OA\Property(
                    * property="last_name",
                    * type="string"
                * ),
                * @OA\Property(
                    * property="first_name",
                    * type="string"
                * ),
            * )
        * )
    * )
* )
*/

    public function register(Request $request)
    {
        try
        {
          $request->validate([
                'login' => [
                    'required'
                ],
                'password' => [
                    'required'
                ],
                'email' => [
                    'required','email'
                ],
                'last_name' => [
                    'required'
                ],
                'first_name' => [
                    'required'
                ]
            ]);
            $user = User::create([
                'login' => $request->login,
                'password' => bcrypt($request->password),
                'email' => $request->email,
                'last_name' => $request->last_name, 
                'first_name' => $request->first_name
            ]);
            return (new UserResource($user))->response()->setStatusCode(CREATED);
        }
        catch(QueryException $ex)
        {
            abort(INVALID_DATA , 'Invalid data');
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
    }

/**
    *@OA\Post(
    *path="/api/signin",
    *tags={"Login"},
    *summary="Log a user in",
    *@OA\Response(
        * response = 200,
        * description = "Ok"),
        * @OA\RequestBody(
            * @OA\MediaType(
            * mediaType="application/json",
            * @OA\Schema(
                * @OA\Property(
                    * property="login",
                    * type="string"
                * ),
                * @OA\Property(
                    * property="password",
                    * type="string"
                * ),
            * )
        * )
    * )
* )
*/

    public function login(Request $request)
    {
        try
        {
            if (Auth::attempt(['login' => $request->login, 'password' => $request->password])) {
                $user = $request->user();
                $token = $user->createToken('User token');

                return ['token' => $token->plainTextToken];
            }
        }
        catch(QueryException $ex)
        {
            abort(INVALID_DATA , 'Invalid data');
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
    }

/**
    *@OA\Post(
    *path="/api/signout",
    *tags={"Logout"},
    *summary="Log a user out",
    *@OA\Response(
        * response = 204,
        * description = "No content"),
        * @OA\RequestBody(
            * @OA\MediaType(
            * mediaType="application/json",
            * @OA\Schema(
                * @OA\Property(
                    * property="Authorization",
                    * type="string"
                * ),
            * )
        * )
    * )
* )
*/

    public function logout(Request $request)
    {
        try
        {
            $request->user()->tokens()->delete();
            return response()->json('No content', NO_CONTENT);
        }
        catch(QueryException $ex)
        {
            abort(INVALID_DATA , 'Invalid data');
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
    }
}
