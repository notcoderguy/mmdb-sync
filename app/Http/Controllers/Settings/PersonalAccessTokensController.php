<?php
namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonalAccessTokensController extends Controller
{
    public function index()
    {
        $tokens = Auth::user()->tokens;
        
        // Get any flash messages from the session
        $flash = session()->get('flash', []);
        
        return Inertia::render('settings/tokens', [
            'tokens' => $tokens,
            // Include flash messages in the initial props
            'flash' => $flash,
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ]);
    
        $token = Auth::user()->createToken(
            $request->input('name'), 
            $request->input('permissions')
        )->plainTextToken;
    
        return redirect()->back()->with([
            'flash' => [  // Wrap in 'flash' key
                'message' => 'Token created successfully.',
                'newToken' => [
                    'token' => $token,
                    'name' => $request->input('name')
                ]
            ]
        ]);
    }
    
    // Similarly update your other methods (regenerate, destroy):
    public function regenerate(Request $request, $id)
    {
        $token = Auth::user()->tokens()->find($id);
        if (!$token) {
            return redirect()->back()->with([
                'flash' => [
                    'error' => 'Token not found'
                ]
            ]);
        }
    
        $token->delete();
        $newToken = Auth::user()->createToken(
            $token->name, 
            $request->input('permissions', ['*'])
        )->plainTextToken;
    
        return redirect()->back()->with([
            'flash' => [
                'message' => 'Token regenerated successfully',
                'newToken' => [
                    'token' => $newToken,
                    'name' => $token->name
                ]
            ]
        ]);
    }
    
    public function destroy($id)
    {
        $token = Auth::user()->tokens()->find($id);
        if (!$token) {
            return redirect()->back()->with([
                'flash' => [
                    'error' => 'Token not found'
                ]
            ]);
        }
    
        $token->delete();
        return redirect()->back()->with([
            'flash' => [
                'message' => 'Token deleted successfully'
            ]
        ]);
    }
}