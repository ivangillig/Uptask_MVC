<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

    public static function login(Router $router) {

        $alertas = [];

        

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if (empty($alertas)){

                //Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                }else{

                    //El usuario existe
                    if( password_verify($_POST['password'], $usuario->password)) {
                        
                        //Iniciar la sesión del usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        //Redireccionar
                        header('Location: /dashboard');

                    }else{
                        Usuario::setAlerta('error', 'Contraseña incorrecta');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();

        //render a la vista
        $router->render('/auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }


    public static function logout() {
        
        session_start();
        $_SESSION = [];
        header('Location: /');        
    }

    public static function crearCuenta(Router $router) {

        //instancia del modelo usuario
        $usuario = new Usuario; 
        $alertas = [];

        
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();


            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {

                    //hash de password
                    $usuario->hashPassword();

                    //eliminar password2
                    unset($usuario->password2);

                    //generar el Token
                    $usuario->crearToken();

                    //Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    //Envío mail de verificación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
         }
        

         //render a la vista
         $router->render('/auth/crear', [
            'titulo' => 'Crear cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {

        $usuario = new Usuario; 
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            
            $alertas = $usuario -> validarEmail();


            if(empty($alertas)){
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if ($usuario && $usuario->confirmado) {
                    
                    //Generar un nuevo token
                    $usuario -> crearToken();

                    //elimino la segunda contraseña que está demás
                    unset($usuario->password2);

                    //Actualizar el usuario
                    $usuario->guardar();

                    //Enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    //Imprimir una alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu e-mail.');

                }else{
                    Usuario::setAlerta('error', 'El usuario ingresado no existe o no está confirmado');
                }

            }
        }

         $alertas = Usuario::getAlertas();

         //render a la vista
         $router->render('/auth/olvide', [
            'titulo' => 'Olvide mi contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function restablecer(Router $router) {

        $token = s($_GET['token']);
        $mostrar = true;

        //debuguear($token);
        if(!$token) header('Location: /');

        //identificar el usuario con el token del GET
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error','Token no válido');
            $mostrar = false;
        }
        
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            //Añadir el nuevo pass
            $usuario -> sincronizar($_POST);

            //Validar el password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                //Hashear el nuevo password
                $usuario -> hashPassword();

                //Eliminar el token
                $usuario -> token = null;

                //Guardar el usuario en la BD
                $resultado = $usuario->guardar();

                //Redireccionar
                if($resultado){
                    header('Location: /');
                }
                

            }
            
        }
        
        $alertas = Usuario::getAlertas();
        
        //render a la vista
        $router->render('/auth/restablecer', [
            'titulo' => 'Restablecer contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }
    
    public static function mensaje(Router $router) {
         //render a la vista
         $router->render('/auth/mensaje', [
            'titulo' => 'Cuenta creada exitosamente'
        ]);
    }

    public static function confirmar(Router $router) {

        $token = s($_GET['token']);

        if(!$token) header('Location: /');

        //Encontrar al usuario con el token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            //No se encontró un usuario con ese token
            Usuario::setAlerta('error', 'Token No Válido');
        }else{
            //Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);

            //Guardar el usuario confirmado en la DB
            $usuario -> guardar();
            
            Usuario::setAlerta('exito', 'Usuario confirmado correctamente, inicia sesión para continuar');
        }

        $alertas = Usuario::getAlertas();


         //render a la vista
         $router->render('auth/confirmar', [
            'titulo' => 'Cuenta confirmada correctamente',
            'alertas' => $alertas
        ]);
    }
}