<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserGuideController extends Controller
{
    /**
     * Display the embedded interactive HTML user guide with workflows and diagrams.
     */
    public function index()
    {
        return view('user_guide.index');
    }
}
