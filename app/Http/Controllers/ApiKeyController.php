<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ApiKeyController extends Controller
{
    /**
     * Display a listing of the API keys.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user();
        $apiKeys = PersonalAccessToken::all();
        return response()->json($apiKeys);
    }

    /**
     * Store a newly created API key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->user();
        $token = $request->user()->createToken($request->input('name'))->plainTextToken;
        return response()->json(['token' => $token]);
    }

    /**
     * Update the specified API key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->user();
        $token = PersonalAccessToken::find($id);
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }
        $token->update($request->all());
        return response()->json($token);
    }

    /**
     * Remove the specified API key.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $request->user();
        $token = PersonalAccessToken::find($id);
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }
        $token->delete();
        return response()->json(['message' => 'Token deleted successfully']);
    }
}
