<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Home extends Controller
{
    public function HomePage ()
    {
        return view('welcome');
    }

    public function ViewUserEditPage ()
    {
        return view('welcome');
    }

    public function ViewUserPage ()
    {
        return view('welcome');
    }
}
