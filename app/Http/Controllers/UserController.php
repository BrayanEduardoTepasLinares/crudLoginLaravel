<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $users = User::paginate();

        return view('user.index', compact('users'))
            ->with('i', ($request->input('page', 1) - 1) * $users->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = \App\Models\Rol::all();
        $user = new User();

        return view('user.create', compact('user', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();

    // 1. Hashear la contraseña antes de crear el usuario
    $data['password'] = Hash::make($data['password']);

    // 3. Crear el usuario con los datos procesados (contraseña hasheada)
    User::create($data);

        return Redirect::route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::find($id);

        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $roles = \App\Models\Rol::all();
        $user = User::find($id);

        return view('user.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        // 2. Lógica para la contraseña:
        // Solo hashear y actualizar la contraseña si se proporcionó una nueva
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Si el campo de contraseña está vacío, quítalo de los datos
            // para que no intente sobrescribir la contraseña existente.
            unset($data['password']);
        }

        $user->update($data);

        return Redirect::route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();

        return Redirect::route('users.index')
            ->with('success', 'User deleted successfully');
    }
    /**
     * Muestra el formulario de login.
     * Es el equivalente a tu método Login() [AllowAnonymous] sin [HttpPost].
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('user.login');
    }

    /**
     * Procesa las credenciales del usuario y lo autentica.
     * Es el equivalente a tu método Login() con [HttpPost].
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. Validar los datos del formulario (email y password)
        // Puedes crear una clase Request más robusta si lo prefieres
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Intentar autenticar al usuario
        // Auth::attempt() intentará encontrar el usuario por 'email' y verificará
        // el hash de la 'password' automáticamente.
        if (Auth::attempt($credentials)) {
            // Regenera la sesión para evitar ataques de fijación de sesión
            $request->session()->regenerate();
            
            // Redirige al usuario a la página de inicio o a la página que intentaba visitar
            return redirect()->intended('/');
        }

        // Si la autenticación falla, redirige de nuevo al formulario de login con un error
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión del usuario.
     * Es el equivalente a tu método CerrarSession().
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Invalida la sesión actual
        $request->session()->invalidate();
        
        // Regenera el token CSRF para la próxima solicitud
        $request->session()->regenerateToken();
        
        // Redirige al usuario a la página de inicio (o a donde desees)
        return redirect('/');
    }
}
