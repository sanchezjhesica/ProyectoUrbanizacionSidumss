<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;   // <-- IMPORTANTE para consultar la tabla roles
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::where('estado_logico', true)->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create() 
    {
        // 1. Obtenemos todos los roles de la base de datos para enviarlos al select
        $roles = DB::table('roles')->get();
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // 1. Validaciones avanzadas y estrictas
        $request->validate([
            // REGLA PARA EL ROL: Debe ser obligatorio y existir en la tabla 'roles'
            'id_rol' => ['required', 'integer', 'exists:roles,id_rol'],

            'nombre' => ['required', 'string', 'max:100', 'regex:/^(?!\s*$).+$/', 'not_in:null,NULL,Null,N/A,n/a', 'regex:/^[\pL\s\-]+$/u'],
            'apellido_paterno' => ['nullable', 'string', 'max:100', 'not_in:null,NULL,Null,N/A,n/a', 'regex:/^[\pL\s\-]+$/u'],
            'apellido_materno' => ['nullable', 'string', 'max:100', 'not_in:null,NULL,Null,N/A,n/a', 'regex:/^[\pL\s\-]+$/u'],
            
            'ci' => ['required', 'numeric', 'digits_between:5,15', 'unique:usuarios,ci', 'regex:/^\S*$/'],
            'telefono' => ['nullable', 'numeric', 'digits_between:7,12', 'regex:/^[67]\d{7}$/'],
            'email' => ['required', 'email:rfc,dns', 'max:100', 'unique:usuarios,email'],
            
            'password' => [
                'required', 
                'string', 
                'min:8', 
                'confirmed', 
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#.\-_]).+$/'
            ],
            
            'codigo_verificacion' => 'required|string|size:6'
        ], [
            // MENSAJES PERSONALIZADOS
            'id_rol.required' => 'Debe seleccionar un rol para el usuario.',
            'id_rol.exists' => 'El rol seleccionado no es válido en el sistema.',

            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo debe contener letras y no puede estar vacío.',
            'nombre.not_in' => 'El nombre no puede ser una palabra inválida como null o N/A.',
            
            'apellido_paterno.regex' => 'El apellido paterno solo debe contener letras.',
            'apellido_paterno.not_in' => 'El apellido paterno no puede ser una palabra inválida.',
            
            'apellido_materno.regex' => 'El apellido materno solo debe contener letras.',
            'apellido_materno.not_in' => 'El apellido materno no puede ser una palabra inválida.',

            'ci.required' => 'La cédula de identidad es obligatoria.',
            'ci.numeric' => 'La cédula de identidad debe contener solo números.',
            'ci.digits_between' => 'La cédula de identidad debe tener entre 5 y 15 dígitos.',
            'ci.unique' => 'Este número de CI ya está registrado en el sistema.',
            'ci.regex' => 'El CI no debe contener espacios en blanco.',

            'telefono.numeric' => 'El teléfono debe contener solo números.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 12 dígitos.',
            'telefono.regex' => 'Número de celular no válido (debe empezar con 6 o 7).',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.dns' => 'El dominio del correo (ej. @gmail.com) no existe o no es real.',
            'email.unique' => 'Este correo electrónico ya está registrado por otro usuario.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.regex' => 'La contraseña debe tener al menos una letra mayúscula, una minúscula, un número y un carácter especial (ej: @, $, !, %, *, ?, &, #, .).',

            'codigo_verificacion.required' => 'Debe ingresar el código de verificación enviado al correo.',
            'codigo_verificacion.size' => 'El código de verificación debe ser de 6 dígitos exactos.'
        ]);

        // 2. VALIDACIÓN DEL CÓDIGO DE VERIFICACIÓN EN LA SESIÓN
        $sessionKey = 'codigo_verificacion_' . $request->email;
        if ($request->codigo_verificacion != Session::get($sessionKey)) {
            return back()->withInput()->withErrors([
                'codigo_verificacion' => 'El código de verificación es incorrecto o no corresponde al correo ingresado.'
            ]);
        }

        // 3. Crear el usuario con el ROL seleccionado dinámicamente
        User::create([
            'id_rol' => $request->id_rol, // <-- Asignación dinámica del rol
            'nombre' => trim($request->nombre),
            'apellido_paterno' => trim($request->apellido_paterno),
            'apellido_materno' => trim($request->apellido_materno),
            'ci' => trim($request->ci),
            'telefono' => trim($request->telefono),
            'email' => trim($request->email),
            'password' => Hash::make($request->password),
            'estado_logico' => true
        ]);

        // 4. Limpiar el código de la sesión
        Session::forget([$sessionKey, 'email_a_verificar']);

        return redirect()->route('admin.usuarios.index')
            ->with('success', '¡Usuario registrado y correo verificado con éxito!');
    }

    public function edit($id) {
        $usuario = User::findOrFail($id);
        $roles = DB::table('roles')->get(); // Enviamos roles por si se edita el rol
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id) {
        $usuario = User::findOrFail($id);
        $usuario->update($request->all());
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado con éxito.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->estado_logico = false; 
        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function resetPassword($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->password = Hash::make('sidumss123');
        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', "La contraseña de {$usuario->nombre} ha sido restablecida a: sidumss123");
    }

    public function enviarCodigoVerificacion(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        $codigo = rand(100000, 999999);

        Session::put('codigo_verificacion_' . $email, $codigo);
        Session::put('email_a_verificar', $email);

        try {
            Mail::raw("Su código de verificación para SIDUMSS es: {$codigo}", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Código de Verificación de Correo - SIDUMSS');
            });

            return response()->json(['success' => true, 'message' => '¡Código enviado con éxito!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al enviar correo: ' . $e->getMessage()], 500);
        }
    }
}