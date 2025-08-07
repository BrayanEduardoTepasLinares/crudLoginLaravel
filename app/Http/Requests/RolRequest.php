<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
			'name' => 'required|string',
        ];
        if ($this->route('rol')) {
            $rules['name'] .= '|' . Rule::unique('rols', 'name')->ignore($this->route('rol')->id);
        } else {
            // Si se está creando un nuevo usuario, el email debe ser único
            $rules['name'] .= '|unique:rols,name';
        }

        
        return $rules;
    }
}
