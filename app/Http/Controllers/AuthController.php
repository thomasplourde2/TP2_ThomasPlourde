<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use App\Http\Resources\UserResource;

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
