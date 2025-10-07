<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Requests\CategoriaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
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
    
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $estado = $request->input('estado');

        $categorias = Categoria::query()
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($w) use ($buscar) {
                    $w->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            ->when($estado !== null && $estado !== '', function ($q) use ($estado) {
                $q->where('estado', $estado);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('categoria.index', compact('categorias', 'buscar', 'estado'));
    }

    /**
     * Form de creación (reutilizado para edición).
     */
    public function create(): View
    {
        return view('categoria.create');
    }

    /**
     * Guardar nueva categoría.
     */
    public function store(CategoriaRequest $request): RedirectResponse
    {
        Categoria::create($request->validated());

        return redirect()
            ->route('categoria.index')
            ->with('success', 'Categoría agregada con éxito.');
    }

    /**
     * Form de edición (reutiliza la vista create).
     * Usa route-model binding: {categoria}
     */
    public function edit(Categoria $categoria): View
    {
        return view('categoria.create', compact('categoria'));
    }

    /**
     * Actualizar categoría existente.
     */
    public function update(CategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $categoria->update($request->validated());

        return redirect()
            ->route('categoria.index')
            ->with('success', 'Categoría actualizada con éxito.');
    }

    /**
     * Eliminar categoría.
     */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        $categoria->delete();

        return redirect()
            ->route('categoria.index')
            ->with('success', 'Categoría eliminada con éxito.');
    }
}
