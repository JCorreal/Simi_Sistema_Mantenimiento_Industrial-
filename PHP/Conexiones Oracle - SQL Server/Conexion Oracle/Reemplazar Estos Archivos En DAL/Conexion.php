<?php // Clase que nos devuelve la conexion con el proveedor que se desee

class Conexion {
 
 public static function obtenerConexion()
 {
    
    try
    {    
        $conexion = oci_connect('CONTROLMANTENIMIENTODB', 'tiger', 'localhost/XE');
        if (!$conexion) {
            die("No se puede conectar a la base de datos:");
        }
        else 
        {
           return($conexion);
        }         
    }
    catch (Exception $ex)
    { 
       echo $ex;     
    }
 }

}


