<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function registerForm()
    {
        return view('pages.auth.register');
    }

    public function storeRegister(RegisterRequest $request)
    {
        $data = $request->validated();

        $this->authRepository->register($data);

        return redirect()->route('home');
    }

    public function loginForm()
    {
        return view('pages.auth.login');
    }

    public function storeLogin(LoginRequest $request)
    {
        $data = $request->validated();

        $this->authRepository->login($data);

        return redirect()->route('home');
    }

    public function logout()
    {
        $this->authRepository->logout();

        return redirect()->route('home');
    }
}
