<?php

namespace App\Http\Controllers\Service;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function create()
    {
        $content = json_decode(file_get_contents('php://input'), true);
        // try catch
        $user = User::create([
            'name' => $content['name'],
            'email' => $content['email'],
            'password' => Hash::make($content['pass']),
        ]);
    }
}