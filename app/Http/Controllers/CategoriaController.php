<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Requests\CategoriaRequest;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('can:categoria.create')->only(['create', 'store']);
        $this->middleware('can:categoria.index')->only('index');
        $this->middleware('can:categoria.update')->only(['edit', 'update']);
        $this->middleware('can:categoria.destroy')->only('destroy');
    }
    
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $estado = $request->estado;

        $categorias = Categoria::filtrar($buscar, $estado)
            ->orderBy('id','DESC')
            ->paginate(4)
            ->withQueryString();

        return view('categoria.index', compact('categorias','buscar','estado'));
    }

    public function create()
    {
        return view('categoria.create');
    }

    public function store(CategoriaRequest $request)
    {
        Categoria::create($request->validated());
        return redirect()->route('categoria.index')->with('success', 'Categoría agregada con éxito');
    }

    public function show(Categoria $categoria)
    {

        return redirect()->route('categoria.index');
    }

    public function edit(Categoria $categoria)
    {
        return view('categoria.create', compact('categoria'));
    }

    public function update(CategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());
        return redirect()->route('categoria.index')->with('success', 'Categoría actualizada con éxito');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return redirect()->route('categoria.index')->with('success', 'Categoría eliminada con éxito');

