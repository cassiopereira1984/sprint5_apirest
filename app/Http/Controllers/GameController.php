<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    public function play(Request $request, int $id)
    {
        $user = Auth::user();
        $user = User::find($id);

        if ($request->user()->id !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 403);
        }

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $diceResult = $this->throwDice();
    
        $game = new Game;
        $game->user_id = Auth::user()->id;
        $game->dice1 = $diceResult['dice1'];
        $game->dice2 = $diceResult['dice2'];
        $game->win = $diceResult['win'];;
        $game->save();
    
        // Devuelve la respuesta JSON con la estructura esperada
        return response()->json([
            'message' => 'Game created successfully',
            'game' => [
                'user_name' => $user->name,
                'die1' => $game->dice1,
                'die2' => $game->dice2,
                'win' => $game->win,
            ]
        ], 201);
    }

    public function throwDice() 
    {
        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
        $win = ($dice1 + $dice2) === 7;
    
        // Devuelve la respuesta JSON con la estructura esperada
        return [
                'dice1' => $dice1,
                'dice2' => $dice2,
                'win' => $win,
        ];
    }

    public function deleteGames($id)
    {
        $user = Auth::user();

        if ($user->id != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->games()->delete();

        return response()->json(['message' => 'Games deleted'], 200);
    }

    public function getGames($id)
    {
        $user = Auth::user();

        if ($user->id != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $games = User::find($id)->games;

        if ($games->isEmpty()) {
            return response(['message' => 'No tienes tirada tirada'], 200);
        }

        return response()->json($user->games, 200);
    }
}
