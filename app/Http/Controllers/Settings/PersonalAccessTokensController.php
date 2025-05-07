<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonalAccessTokensController extends Controller
{
    /**
     * Display a listing of the user's personal access tokens.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tokens = Auth::user()->tokens;

        return Inertia::render('settings/tokens', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * Store a newly created personal access token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ]);

        $token = Auth::user()->createToken($request->input('name'), $request->input('permissions'))->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    /**
     * Display the specified personal access token.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($token)
    {
        // 
    }

    /**
     * Revoke the specified personal access token.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function revoke($token)
    {
        Auth::user()->tokens()->where('id', $token->id)->delete();

        return response()->json([], 200);
    }

    /**
     * Regenerate the specified personal access token.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function regenerate(Request $request, $token)
    {
        $token = Auth::user()->tokens()->find($token);

        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        $token->delete();

        $newToken = Auth::user()->createToken($request->input('name'), $request->input('permissions'))->plainTextToken;

        return response()->json(['token' => $newToken], 200);
    }

    /**
     * Remove the specified personal access token.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($token)
    {
        Auth::user()->tokens()->where('id', $token->id)->delete();

        return response()->json([], 200);
    }
}
