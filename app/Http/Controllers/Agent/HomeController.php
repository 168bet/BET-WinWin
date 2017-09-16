<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\AppBaseController;
use App\Models\CarrierUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends AppBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:agent');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Agent.home');
    }

}
