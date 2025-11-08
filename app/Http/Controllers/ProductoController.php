<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Http\Requests\ProductoRequest;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        // Validación ligera de filtros (opcional pero recomendable)
        $request->validate([
            'q'            => 'nullable|string|max:100',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio_min'   => 'nullable|numeric|min:0',
            'precio_max'   => 'nullable|numeric|min:0',
            'stock'        => 'nullable|in:con,sin',
        ]);

        // Consulta con relaciones + scope de filtros
        $query = Producto::with('categoria')
            ->filtrar(
                $request->q,              // buscar en nombre/descripcion
                $request->categoria_id,   // categoría
                $request->precio_min,     // precio mínimo (usa precio_venta en el scope)
                $request->precio_max      // precio máximo
            );

        // Filtro de stock (como ya lo tenías)
        if ($request->stock === 'con') {
            $query->where('stock', '>', 0);
        } elseif ($request->stock === 'sin') {
            $query->where('stock', '<=', 0);
        }

        $productos = $query
            ->orderBy('nombre')                   // o 'id', 'DESC' si prefieres
            ->paginate(10)
            ->appends($request->query());         // mantiene filtros en paginación

        // Para el <select> de categorías en la vista
        $categorias = Categoria::orderBy('nombre')->get(['id','nombre']);

    // 👇 Si el usuario es ADMIN → muestra la vista de admin
    if (auth()->user()->hasRole('admin')) {
        return view('producto.index', compact('productos', 'categorias'));
    }

    // 👇 Si el usuario es USER → muestra la vista de usuario
    return view('user.productos', compact('productos'));

    if (auth()->user()->hasRole('admin')) {
    return view('producto.index', compact('productos', 'categorias'));
}

return view('user.productos', compact('productos'));

}


    public function create()
    {
        // Solo categorías activas
        $categorias = Categoria::where('estado', 1)
            ->orderBy('nombre')
            ->get(['id','nombre']);

        return view('producto.create', compact('categorias'));
    }

    public function store(ProductoRequest $request)
    {
        // Manejo de imagen (igual que tu versión)
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '.' . $imagen->getClientOriginalExtension();
            $imagen->move(public_path('img'), $nombreImagen);
        } else {
            $nombreImagen = null;
        }

        $data = $request->except('imagen');
        $data['imagen'] = $nombreImagen;

        Producto::create($data);

        return redirect()->route('producto.index')
            ->with('success', 'Producto agregado con éxito');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('estado', 1)
            ->orderBy('nombre')
            ->get(['id','nombre']);

        return view('producto.create', compact('producto', 'categorias'));
    }

    public function update(ProductoRequest $request, Producto $producto)
    {
        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            // borra imagen anterior si existe
            if ($producto->imagen && file_exists(public_path('img/' . $producto->imagen))) {
                @unlink(public_path('img/' . $producto->imagen));
            }

            $imagen = $request->file('imagen');
            $nombreImagen = time() . '.' . $imagen->getClientOriginalExtension();
            $imagen->move(public_path('img'), $nombreImagen);
            $data['imagen'] = $nombreImagen;
        } else {
            // conserva la imagen anterior
            $data['imagen'] = $producto->imagen;
        }

        $producto->update($data);

        return redirect()->route('producto.index')
            ->with('success', 'Producto actualizado con éxito');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen && file_exists(public_path('img/' . $producto->imagen))) {
            @unlink(public_path('img/' . $producto->imagen));
        }

        $producto->delete();

        return redirect()->route('producto.index')
            ->with('success', 'Producto eliminado con éxito');
    }
}
