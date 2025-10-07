<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('categoria')?->id; 
        return [
            'nombre'      => 'required|string|max:100|unique:categorias,nombre,'.($id ?? 'NULL').',id',
            'descripcion' => 'nullable|string|max:255',
            'estado'      => 'required|boolean',
        ];
    }
}
