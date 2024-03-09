<?php

namespace App\Http\Controllers;



class BalanceController extends Controller
{
    public function index()
    {
        return auth()->user()->USDBalance();
    }
}
