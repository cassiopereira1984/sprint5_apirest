<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|unique:users,name|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if($request['name'] == null) {
            $request['name'] = 'Anonymous';
        }
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ])->assignRole("user");

        return response()->json(['message' => 'Successfully created user!',
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $accessToken = $tokenResult->accessToken;
        
            return response()->json([
                'message' => 'Login Successful',
                'token' => $accessToken,
                'token_type' => 'Bearer',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate(['name' => 'nullable|string']);

        $user->name = $request->name ?? 'Anonymous';
        $user->save();

        return response()->json($user, 200);
    }

    public function getAllPlayers()
    {
        $user = User::all();
        return response()->json(["users" => $user]);
    }

    public function getWinner()
    {
        $users = User::with('games')->get();
    
        if ($users->isEmpty()) {
            return response()->json(['error' => 'No players found'], 404);
        }
    
        $winner = null;
        $maxSuccessRate = 0;
    
        foreach ($users as $user) {
            //if ($user != admin)  
            $totalGames = $user->games->count();
            $wins = $user->games->where('win', true)->count();
            $successPercentage = $totalGames > 0 ? ($wins / $totalGames) * 100 : 0;

            if ($successPercentage > $maxSuccessRate) {
                $maxSuccessRate = $successPercentage;
                $winner = $user;
            }
        }
    
        if ($winner) {
            return response()->json([
                'winner' => [
                    'name' => $winner->name,
                    'success_percentage' => $maxSuccessRate,
                ],
                'message' => 'Request successful',
            ], 200);
        } else {
            return response()->json(['error' => 'No winner found'], 404);
        }
    }

    public function getLoser()
    {
        $users = User::with('games')->get();
    
        if ($users->isEmpty()) {
            return response()->json(['error' => 'No players found'], 404);
        }
    
        $worstUser = null;
        $lowestSuccessRate = 101;
    
        foreach ($users as $user) {
            //if($user es != admin)
            $totalGames = $user->games->count();
            $wins = $user->games->where('win', true)->count();
            $successRate = $totalGames > 0 ? ($wins / $totalGames) * 100 : 0;
    
            if ($successRate < $lowestSuccessRate) {
                $lowestSuccessRate = $successRate;
                $worstUser = $user;
            }
        }
    
        if ($worstUser) {
            return response()->json([
                'loser_player' => $worstUser,
                'success_percentage' => $lowestSuccessRate,
                'message' => 'Request Successful'
            ], 200);
        } else {
            return response()->json(['error' => 'Request failed'], 400);
        }
    }
}