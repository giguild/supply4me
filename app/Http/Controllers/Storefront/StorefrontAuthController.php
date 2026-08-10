<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class StorefrontAuthController extends Controller
{
    public function showRegister()
    {
        return Inertia::render('Storefront/Register', [
            'cartCount' => $this->getCartCount(),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $company = \App\Models\Companies\Company::firstOrFail();

        $customer = Customer::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'customer_type' => 'individual',
            'status' => 'active',
            'credit_status' => 'good',
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('storefront.home');
    }

    public function showLogin()
    {
        return Inertia::render('Storefront/Login', [
            'cartCount' => $this->getCartCount(),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('storefront.checkout'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.home');
    }

    protected function getCartCount(): int
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
}
