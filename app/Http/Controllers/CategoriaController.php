<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:categoria.create')->only(['create', 'store']);
        $this->middleware('can:categoria.index')->only('index');
        $this->middleware('can:categoria.update')->only(['edit', 'update']);
        $this->middleware('can:categoria.destroy')->only('destroy');
    }
}
