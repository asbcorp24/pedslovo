<?php
namespace App\Http\Controllers;

class HelpController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = 'student';

        if ($user) {
            if ($user->role === 'admin' || $user->role === 'editor') {
                $role = 'admin';
            } elseif ($user->role === 'teacher') {
                $role = 'teacher';
            }
        }

        return view('help.index', compact('role'));
    }
}
