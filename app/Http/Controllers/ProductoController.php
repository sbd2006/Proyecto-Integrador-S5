<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Http\Requests\ProductoRequest;
use Illuminate\Http\Request;
use App\Models\Categoria;


class ProductoController extends Controller
{
    public function index(Request $request)
    {
        // ❌ Eliminamos el uso de Categoria para evitar errores
        $categorias = []; // Esto evita errores en la vista

        $query = Producto::query();

        // Filtro por stock
        if ($request->stock == 'con') {
            $query->where('stock', '>', 0);
        } elseif ($request->stock == 'sin') {
            $query->where('stock', '<=', 0);
        }

        // Filtro por nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $productos = $query->orderBy('id', 'DESC')->paginate(4);

        return view('producto.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        
        $categorias = Categoria::where('estado', 1)->get(); // solo las activas
        return view('producto.create', compact('categorias'));

    }

    public function store(ProductoRequest $request)
    {
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
        $categorias = []; // Evita errores en la vista
        $categorias = Categoria::where('estado', 1)->get();
        return view('producto.create', compact('producto', 'categorias'));
    }
    

    public function update(ProductoRequest $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            if ($producto->imagen && file_exists(public_path('img/' . $producto->imagen))) {
                unlink(public_path('img/' . $producto->imagen));
            }

            $imagen = $request->file('imagen');
            $nombreImagen = time() . '.' . $imagen->getClientOriginalExtension();
            $imagen->move(public_path('img'), $nombreImagen);
            $data['imagen'] = $nombreImagen;
        } else {
            $data['imagen'] = $producto->imagen;
        }

        $producto->update($data);

        return redirect()->route('producto.index')
            ->with('success', 'Producto actualizado con éxito');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen && file_exists(public_path('img/' . $producto->imagen))) {
            unlink(public_path('img/' . $producto->imagen));
        }

        $producto->delete();

        return redirect()->route('producto.index')
            ->with('success', 'Producto eliminado con éxito');
    }

}
