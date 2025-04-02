<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\LanguageResource;
use App\Models\User;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        try
        {
            return UserResource::collection(User::all())->response()->setStatusCode(OK); 
        }        
        catch(Exception $ex)
        {
            abort(SERVER_ERROR,  'Server error');
        }   
    }
}
