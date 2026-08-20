<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function form(){ return view('auth.login'); }

    public function registerForm(){ return view('auth.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|max:255|unique:users,email',
            'role'=>'required|in:student,teacher',
            'password'=>'required|min:8|confirmed',
        ]);

        User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'role'=>$data['role'],
            'password'=>Hash::make($data['password']),
            'approved_at'=>null,
            'registration_requested_at'=>now(),
        ]);

        return redirect()->route('login')->with('success','Заявка на регистрацию отправлена. Войти можно после подтверждения администратором.');
    }

    public function login(Request $request)
    {
        $credentials=$request->validate(['email'=>'required|email','password'=>'required']);
        if(Auth::attempt($credentials,$request->boolean('remember'))){
            if (!auth()->user()->isApproved()) {
                Auth::logout();
                return back()->withErrors(['email'=>'Регистрация ещё не подтверждена администратором.'])->onlyInput('email');
            }
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
        return back()->withErrors(['email'=>'Неверный email или пароль'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
