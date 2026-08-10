<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Core\LoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Resources\Core\AuthResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function __construct(
        protected LoginAction $loginAction
    ) {}

    /**
     * Show login page (Inertia).
     */
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle API login (returns JSON + token).
     */
    public function __invoke(LoginRequest $request)
    {
        $result = $this->loginAction->execute(
            $request->validated('email'),
            $request->validated('password'),
            $request->boolean('remember')
        );

        return $this->created(
            AuthResource::make($result),
            'Login successful'
        );
    }

    /**
     * Handle web login (session-based, redirects).
     */
    public function loginWeb(Request $request): Response|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Logout the user (web session).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
