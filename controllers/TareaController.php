<?php

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController {

    public static function index(){

        
        $proyectoId=$_GET['id'];
        
        if(!$proyectoId){ header('Location: /dashboard');}
        
        session_start();
        
        $proyecto = Proyecto::where('url', $proyectoId);

        if(!$proyecto || $proyecto -> propietarioId !== $_SESSION['id']) header('Location: /404');

        $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);

        echo json_encode(['tareas' => $tareas]);
    }

    public static function crear(){
        if($_SERVER['REQUEST_METHOD']==='POST'){

            session_start();

            $proyectoId = $_POST['proyectoId'];

            $proyecto = Proyecto::where('url', $proyectoId);

            // Error si el proyecto no existe o no es el dueño del proyecto
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al agregar la tarea'
                ];

                echo json_encode($respuesta);
                return;
            }

            // Si todo sale bien, instanciar y crear la tarea
            $tarea = new Tarea($_POST);

            //cambio la url por el id del proyecto
            $tarea->proyectoId = $proyecto->id;

            $resultado = $tarea -> guardar();    
            
            $respuesta = [
                'tipo' => 'exito',
                'id'  => $resultado['id'],
                'mensaje' => 'Tarea creada correctamente',
                'proyectoId' => $proyecto->id
            ];
            echo json_encode($respuesta);
        }
        
    }

    public static function actualizar(){
        if($_SERVER['REQUEST_METHOD']==='POST'){
            
            //Validar que el proyecto exista
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);

            session_start();

            // Error si el proyecto no existe o no es el dueño del proyecto
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];

                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $tarea -> proyectoId = $proyecto -> id;

            $resultado = $tarea->guardar();

            if($resultado){
                $respuesta = [
                    'tipo' => 'exito',
                    'id'  => $tarea->id,
                    'proyectoId' => $proyecto->id,
                    'mensaje' => 'La tarea se modificó correctamente'
                ];
                echo json_encode(['respuesta' => $respuesta]);
            }
        }
    }

    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD']==='POST'){
            
             //Validar que el proyecto exista
             $proyecto = Proyecto::where('url', $_POST['proyectoId']);

             session_start();
 
             // Error si el proyecto no existe o no es el dueño del proyecto
             if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                 $respuesta = [
                     'tipo' => 'error',
                     'mensaje' => 'Hubo un error al eliminar la tarea'
                 ];
 
                 echo json_encode($respuesta);
                 return;
             }

             $tarea = new Tarea($_POST);
             $resultado = $tarea->eliminar();

             echo json_encode(['resultado' => $resultado]);
        }
    }
}