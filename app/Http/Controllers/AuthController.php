<?php

namespace App\Http\Controllers;
/**
 * @OA\Info(
 *    title="APIs For Thrift Store",
 *    version="1.0.0",
 * ),
 *   @OA\SecurityScheme(
 *       securityScheme="bearerAuth",
 *       in="header",
 *       name="bearerAuth",
 *       type="http",
 *       scheme="bearer",
 *       bearerFormat="JWT",
 *    ),
 */
use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\personas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CreateUserRequest",
 *     required={"name", "email", "password", "nombre", "cedula", "direccion", "fecha_nacimiento", "id_rol"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string"),
 *     @OA\Property(property="nombre", type="string"),
 *     @OA\Property(property="cedula", type="string"),
 *     @OA\Property(property="direccion", type="string"),
 *     @OA\Property(property="fecha_nacimiento", type="date"),
 *     @OA\Property(property="id_rol", type="integer"),
 * )
 */
/**
 * @OA\Schema(
 *     schema="LoginUserRequest",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string"),
 * )
 */
/**
 * @OA\Schema(
 *     schema="UpdateNameRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string"),
 * )
 */
/**
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email")
 * )
 */
/**
 * @OA\Schema(
 *     schema="UserWithRole",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="role", type="string")
 * )
 */
/**
 * 
 * @OA\Schema(
 *     schema="PersonaWithUserAndRole",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="role", type="string")
 * )
 */
/**
 * @OA\Schema(
 *     schema="UpdatePasswordRequest",
 *     required={"old_password", "new_password"},
 *     @OA\Property(property="old_password", type="string"),
 *     @OA\Property(property="new_password", type="string", minLength=6),
 * )
 */
class AuthController extends Controller
{
    /**
 *  @OA\Post(
 *     path="/api/auth/register",
 *     summary="Crear un nuevo usuario",
 *     description="Este endpoint se utiliza para crear un nuevo usuario junto con su información de persona asociada en la aplicación.",
 *     operationId="createUser",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuario creado exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User Created Successfully"),
 *             @OA\Property(property="token", type="string", example="API TOKEN")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Campos vacíos o inválidos",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Existen campos vacios"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */
    public function createUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'nombre' => 'required',
                'cedula' => 'required',
                'direccion' => 'required',
                'fecha_nacimiento' => 'required',
                'id_rol' => 'required',
                
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Existen campos vacios',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $persona = personas::create([
                'nombre' => $request->nombre,
                'cedula' => $request->cedula,
                'direccion' => $request->direccion,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                
            ]);
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'id_persona' => $persona->id,
                'id_rol' => $request->id_rol,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

/**
 * @OA\Post(
 *     path="/api/auth/login",
 *     summary="Iniciar sesión de usuario",
 *     description="Este endpoint se utiliza para permitir a un usuario iniciar sesión en la aplicación.",
 *     operationId="loginUser",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/LoginUserRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuario ha iniciado sesión exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User Logged In Successfully"),
 *             @OA\Property(property="token", type="string", example="API TOKEN")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Error de validación o Email y contraseña no coinciden con nuestros registros",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
 /**
 * @OA\Get(
 *     path="/api/auth/users",
 *     summary="Obtener lista de usuarios",
 *     description="Este endpoint se utiliza para obtener una lista de todos los usuarios registrados en la aplicación.",
 *     operationId="listuser",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de usuarios obtenida exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="usuarios", type="array", @OA\Items(ref="#/components/schemas/User"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */
    public function listuser(){
        $user = User::all();
        return response()->json(['usuarios'=>$user],200);
      }
    
/**
 * @OA\Put(
 *     path="/api/auth/update/{id}",
 *     summary="Actualizar nombre de usuario",
 *     description="Este endpoint se utiliza para permitir a un usuario autenticado actualizar su propio nombre de usuario.",
 *     operationId="updatename",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del usuario",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UpdateNameRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Nombre de usuario actualizado exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Nombre de usuario actualizado con éxito")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No autorizado para editar el usuario",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="No estás autorizado para editar este usuario")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuario no encontrado",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Usuario no encontrado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */

   public function updatename(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Verificar si el usuario autenticado tiene el mismo ID que el usuario que intenta editar
        if (Auth::user()->id !== $user->id) {
            return response()->json(['message' => 'No estás autorizado para editar este usuario'], 403);
        }

        $user->update([
            'name' => $request->input('name')
        ]);

        return response()->json(['message' => 'Nombre de usuario actualizado con éxito'], 200);
    }

    /**
 * @OA\Put(
 *     path="/api/auth/update-password/{id}",
 *     summary="Actualizar contraseña de usuario",
 *     description="Este endpoint se utiliza para permitir a un usuario autenticado actualizar su propia contraseña.",
 *     operationId="updatepassword",
 * security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del usuario",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UpdatePasswordRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Contraseña de usuario actualizada exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Contraseña de usuario actualizada con éxito")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No autorizado para editar el usuario",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="No estás autorizado para editar este usuario")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuario no encontrado",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Usuario no encontrado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Contraseña antigua incorrecta",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="La contraseña antigua no coincide con la registrada")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */

    public function updatepassword(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Verificar si el usuario autenticado tiene el mismo ID que el usuario que intenta editar
        if (Auth::user()->id !== $user->id) {
            return response()->json(['message' => 'No estás autorizado para editar este usuario'], 403);
        }

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6', // Asegurarse de que el nuevo password tenga al menos 6 caracteres
        ]);

        // Verificar que el password antiguo proporcionado sea correcto
        if (!Hash::check($request->input('old_password'), $user->password)) {
            return response()->json(['message' => 'la contaseña antigua no coincide con el registrado'], 422);
        }

        $user->update([
            'password' => Hash::make($request->input('new_password'))
        ]);

        return response()->json(['message' => 'Contraseña de usuario actualizada con éxito'], 200);
    }

    public function update(Request $request, $id)
    {
        User::find($id)->update($request->all());
        return response()->json([
            "smg"=>"Se actualizo Correctamente"
        ]);
    }

    /**
 * @OA\Delete(
 *     path="/api/auth/delete/users/{id}",
 *     summary="Eliminar usuario",
 *     description="Este endpoint se utiliza para eliminar un usuario existente de la aplicación.",
 *     operationId="destroyusuario",
 * security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del usuario",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuario eliminado exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Usuario eliminado con éxito")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuario no encontrado",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Usuario no encontrado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */

    public function destroyusuario($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado con éxito'], 200);

      
      
        }
    }