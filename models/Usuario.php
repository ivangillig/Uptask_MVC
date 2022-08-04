<?php

namespace Model;

class Usuario extends ActiveRecord {

    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = [])
    {
        
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;

    }

    //Validar inicio de sesión
    public function validarLogin(){
        if(!$this->email) {
            self::$alertas['error'][] = 'El email del Usuario es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no válido';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'El password del Usuario es obligatorio';
        }

        return self::$alertas;
    }

    //validación para cuentas nuevas
    public function validarNuevaCuenta(){
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del Usuario es obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El email del Usuario es obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'El password del Usuario es obligatorio';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe tener al menos 6 caracteres';
        }

        if($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los passwords son diferentes';
        }

        return self::$alertas;
    }

    //Valida un email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
            return self::$alertas;
        }
        
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no válido';
        }
        

        return self::$alertas;
    }

    //valida el password
    public function validarPassword(){

        if(!$this->password) {
            self::$alertas['error'][] = 'El password del Usuario es obligatorio';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe tener al menos 6 caracteres';
        }

        return self::$alertas;

    }

    public function validar_perfil(){
        if(!$this -> nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if(!$this -> email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        return self::$alertas;
    }

    public function nuevo_password() : array {
        if(!$this->password_actual){
            self::$alertas['error'][] = 'El password actual no puede ir vacío';
        }

        if(!$this->password_nuevo){
            self::$alertas['error'][] = 'El nuevo password no puede ir vacío';
        }else{
            if(strlen($this->password_nuevo) < 6){
                self::$alertas['error'][] = 'El nuevo password debe tener al menos 6 caracteres';
            }
        }

        return self::$alertas;
    }

    public function comprobar_password() : bool {
        return password_verify($this->password_actual, $this->password);
    }

    //hashea los pass
    public function hashPassword() : void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

    }

    //Generar un token
    public function crearToken() : void {
        $this->token = uniqid();

    }

}