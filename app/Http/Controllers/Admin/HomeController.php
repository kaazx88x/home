<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;

class HomeController extends Controller 
{
    public function index()
    {
        return view('admin.dashboard');
    }
}
