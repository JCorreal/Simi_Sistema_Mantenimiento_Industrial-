<?php // Clase que nos devuelve la conexion con el proveedor que se desee

class Conexion {
 
 public static function obtenerConexion()
 {
    $serverName = "DESKTOP-2ONK997VFGT\SQLEXPRESS"; 
    
    try
    {    
           $connectionInfo=array( "Database"=>"controlmantenimientodb");                
           $conexion = sqlsrv_connect( $serverName, $connectionInfo);
           if( !$conexion ) {
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


