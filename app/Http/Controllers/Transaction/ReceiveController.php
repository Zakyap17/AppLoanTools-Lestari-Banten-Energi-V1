<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceiveController extends Controller
{
    public function index()
    {
        // UI ONLY
        return view('transaction.receive.index');
    }
}
