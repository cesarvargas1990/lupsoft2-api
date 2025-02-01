<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use  App\User;
use DB;

class UserController extends Controller
{
     /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function profile()
    {
        return response()->json(['user' => Auth::user()], 200);
    }

    /**
     * Get all User.
     *
     * @return Response
     */
    public function allUsers()
    {
         return response()->json(['users' =>  User::all()], 200);
    }

    /**
     * Get one user.
     *
     * @return Response
     */
    public function singleUser($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'user not found!'], 404);
        }

    }
	
    public function getUsers($id)
    {
        try {
            $data = User::select('id as value', 'name as label')
                        ->where('id_user', $id)
                        ->get();
            if ($data->isEmpty()) {
                return response()->json(['message' => 'User not found!'], 404);
            }
            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving user!'], 500);
        }
    }

}
