<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $roles = DB::table('roles')->get();
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_rol' => ['required', 'integer', 'exists:roles,id_rol'],
            'nombre' => ['required', 'string', 'max:100', 'regex:/^(?!\s*$).+$/', 'not_in:null,NULL,Null,N/A,n/a', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
            'apellido_paterno' => ['nullable', 'string', 'max:100', 'not_in:null,NULL,Null,N/A,n/a', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
            'apellido_materno' => ['nullable', 'string', 'max:100', 'not_in:null,NULL,Null,N/A,n/a', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
            
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
            'id_rol.required' => 'Debe seleccionar un rol para el usuario.',
            'id_rol.exists' => 'El rol seleccionado no es válido en el sistema.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo debe contener letras, sin símbolos ni caracteres extraños.',
            'apellido_paterno.regex' => 'El apellido paterno solo debe contener letras.',
            'apellido_materno.regex' => 'El apellido materno solo debe contener letras.',
            'ci.required' => 'La cédula de identidad es obligatoria.',
            'ci.numeric' => 'La cédula de identidad debe contener solo números.',
            'ci.digits_between' => 'La cédula de identidad debe tener entre 5 y 15 dígitos.',
            'ci.unique' => 'Este número de CI ya está registrado en el sistema.',
            'telefono.numeric' => 'El teléfono debe contener solo números.',
            'telefono.regex' => 'Número de celular no válido (debe empezar con 6 o 7).',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado por otro usuario.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.regex' => 'La contraseña debe tener al menos una letra mayúscula, una minúscula, un número y un carácter especial.',
            'codigo_verificacion.required' => 'Debe ingresar el código de verificación.',
            'codigo_verificacion.size' => 'El código de verificación debe ser de 6 dígitos exactos.'
        ]);

        $sessionKey = 'codigo_verificacion_' . $request->email;
        if ($request->codigo_verificacion != Session::get($sessionKey)) {
            return back()->withInput()->withErrors([
                'codigo_verificacion' => 'El código de verificación es incorrecto o no corresponde al correo ingresado.'
            ]);
        }

        User::create([
            'id_rol' => $request->id_rol,
            'nombre' => trim($request->nombre),
            'apellido_paterno' => trim($request->apellido_paterno),
            'apellido_materno' => trim($request->apellido_materno),
            'ci' => trim($request->ci),
            'telefono' => trim($request->telefono),
            'email' => trim($request->email),
            'password' => Hash::make($request->password),
            'estado_logico' => true
        ]);

        Session::forget([$sessionKey, 'email_a_verificar']);

        return redirect()->route('admin.usuarios.index')
            ->with('success', '¡Usuario registrado y correo verificado con éxito!');
    }

    public function edit($id) {
        $usuario = User::findOrFail($id);
        $roles = DB::table('roles')->get();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id) 
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            // Si en tu vista de edición no tienes un select para cambiar el rol, 
            // cambia 'required' por 'nullable' o asegúrate de incluir el rol en el formulario.
            'id_rol' => ['required', 'integer', 'exists:roles,id_rol'],
            
            'nombre' => ['required', 'string', 'max:100', 'regex:/^(?!\s*$).+$/', 'not_in:null,NULL,Null,N/A,n/a', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
            'apellido_paterno' => ['nullable', 'string', 'max:100', 'not_in:null,NULL,Null,N/A,n/a', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
            'apellido_materno' => ['nullable', 'string', 'max:100', 'not_in:null,NULL,Null,N/A,n/a', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
            
            // 🛠️ CORREGIDO AQUÍ: Indicamos explícitamente 'id_usuario' que es tu llave primaria
            'ci' => ['required', 'numeric', 'digits_between:5,15', 'unique:usuarios,ci,' . $id . ',id_usuario', 'regex:/^\S*$/'],
            'telefono' => ['nullable', 'numeric', 'digits_between:7,12', 'regex:/^[67]\d{7}$/'],
            'email' => ['required', 'email:rfc,dns', 'max:100', 'unique:usuarios,email,' . $id . ',id_usuario'],
        ], [
            'id_rol.required' => 'Debe seleccionar un rol para el usuario.',
            'id_rol.exists' => 'El rol seleccionado no es válido en el sistema.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo debe contener letras, sin símbolos ni caracteres extraños.',
            'apellido_paterno.regex' => 'El apellido paterno solo debe contener letras.',
            'apellido_materno.regex' => 'El apellido materno solo debe contener letras.',
            'ci.required' => 'La cédula de identidad es obligatoria.',
            'ci.numeric' => 'La cédula de identidad debe contener solo números.',
            'ci.digits_between' => 'La cédula de identidad debe tener entre 5 y 15 dígitos.',
            'ci.unique' => 'Este número de CI ya está registrado en el sistema.',
            'telefono.numeric' => 'El teléfono debe contener solo números.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 12 dígitos.',
            'telefono.regex' => 'Número de celular no válido (debe empezar con 6 o 7).',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado por otro usuario.',
        ]);

        // Actualización segura
        $usuario->update([
            'id_rol' => $request->id_rol,
            'nombre' => trim($request->nombre),
            'apellido_paterno' => trim($request->apellido_paterno),
            'apellido_materno' => trim($request->apellido_materno),
            'ci' => trim($request->ci),
            'telefono' => trim($request->telefono),
            'email' => trim($request->email),
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado con éxito.');
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
            Mail::raw("Hola,\n\nSu código de verificación para registrarse en el sistema SIDUMSS es:\n\n{$codigo}\n\nEste código es personal y válido para este registro.", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Código de Verificación - SIDUMSS');
            });

            return response()->json([
                'success' => true, 
                'message' => '¡Código enviado con éxito! Revise su bandeja de entrada o spam.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al enviar correo: ' . $e->getMessage()
            ], 500);
        }
    }
}