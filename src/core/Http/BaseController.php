<?php

namespace Core\Http;

use Buki\Router\Http\Controller;
use League\Plates\Engine;

abstract class BaseController extends Controller
{

    public static function view($view, $data = [])
    {
        $plates = new Engine(base_path('resources/views'));
        return $plates->render($view, $data);
    }
}