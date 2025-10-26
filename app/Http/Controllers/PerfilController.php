<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class PerfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('usuario.perfil', compact('user'));
    }

    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|confirmed|min:6',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::exists(str_replace('storage/', 'public/', $user->photo))) {
                Storage::delete(str_replace('storage/', 'public/', $user->photo));
            }

            $path = $request->file('photo')->store('public/fotos_perfil');
            $user->photo = str_replace('public/', 'storage/', $path);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['success' => true]);
    }
}
