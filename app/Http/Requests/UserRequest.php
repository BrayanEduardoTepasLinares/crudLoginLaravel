<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
			'email' => 'required|string',
            'password' => 'required|string',
			'rol_id' => 'required',
        ];
        if ($this->route('user')) {
            $rules['email'] .= '|' . Rule::unique('users', 'email')->ignore($this->route('user')->id);
        } else {
            // Si se está creando un nuevo usuario, el email debe ser único
            $rules['email'] .= '|unique:users,email';
        }

        // Regla condicional para la contraseña
        // Si se está actualizando un usuario, la contraseña no es obligatoria
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $rules['password'] = 'nullable|string|min:8';
        }
        return $rules;
    }
}
