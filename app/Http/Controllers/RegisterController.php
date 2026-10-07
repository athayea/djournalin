<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('register');
    }

    /**
     * Register a new account, then send the user to the login page to sign in.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['username'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Pendaftaran berhasil. Silakan login.')
            ->withInput(['username' => $validated['username']]);
    }
}
