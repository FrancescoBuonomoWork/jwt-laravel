<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    
    public function __construct()
    {
        # By default we are using here auth:api middleware 
        $this->middleware('auth:api', []);
    }

    public function test(){
        return "Token ok!";
    }

}
