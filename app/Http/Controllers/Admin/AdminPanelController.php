<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loket;
use Illuminate\Http\Request;

class AdminPanelController extends Controller
{
    public function panggilanPanel()
    {
        $lokets = Loket::all();
        return view('admin.panggilan', compact('lokets'));
    }
}