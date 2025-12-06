<?php

namespace App\Controllers;

use App\Models\User;
use Core\Http\BaseController;
use Illuminate\Support\Facades\Crypt;
use View;

class HomeController extends BaseController
{
    public function index()
    {
        $animals = ["Php", "Mysql"];
        return View::render("home", compact("animals"));
    }
}