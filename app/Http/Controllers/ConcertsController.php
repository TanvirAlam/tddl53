<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;

class ConcertsController extends Controller
{
    public function show($id)
    {
        $concert = Concert::published()->findOrfail($id);
        return view('concerts.show', ['concert' => $concert]);
    }
}
