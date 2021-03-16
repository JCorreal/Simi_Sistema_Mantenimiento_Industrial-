<?php

class Dao_General {
    
  public function buscarRegistro($tabla, $datoBuscar)
  { // Funcion para buscar un registro especifico 
     $vecresultado = array(); 
     try 
     {  
        $cn = Conexion::obtenerConexion();    
        $params = array(                 
                  array($tabla, SQLSRV_PARAM_IN),  
                  array($datoBuscar, SQLSRV_PARAM_IN)
                );         
        $callSP = '{CALL SPR_R_BuscarRegistro(?,?)}';
        $stmt=sqlsrv_query($cn, $callSP, $params);        
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )
        {
           array_push($vecresultado, $row);
        }
        sqlsrv_free_stmt( $stmt);
        sqlsrv_close( $cn );
     }
     catch (Exception $ex)
     { 
       sqlsrv_close( $cn );
       echo $ex;     
     }
     return $vecresultado;
  }
  
  public function cargarListas($tabla, $opcion)
  {   
    $listaElementos = array();  
    try
    {        
      $cn = Conexion::obtenerConexion();     
      $params = array(                 
                array($tabla, SQLSRV_PARAM_IN),  
                array($opcion, SQLSRV_PARAM_IN)  
                );  
      $callSP = '{CALL SPR_R_CargarListado(?,?)}';
      $stmt=sqlsrv_query($cn, $callSP, $params);
       // Recorremos el resultado de la consulta y lo almacenamos en el array
          while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC ) )
          {
            array_push($listaElementos, $row);           
          }
      sqlsrv_free_stmt( $stmt);
      sqlsrv_close( $cn );
    }    
    catch (Exception $ex)
    { 
      sqlsrv_close( $cn );
      echo $ex;     
    }
    return  $listaElementos;
  }
   
  public function controlProgramacion($tabla)
  { 
    $listaElementos = array();   
    try
    {
      $cn = Conexion::obtenerConexion();     
      $params = array(                 
                array($tabla, SQLSRV_PARAM_IN) 
                );  
      $callSP = '{CALL SPR_R_CargarCombosListas(?)}';
      $stmt=sqlsrv_query($cn, $callSP, $params);
      // Recorremos el resultado de la consulta y lo almacenamos en el array
          while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )
          {            
            array_push($listaElementos, $row[2], $row[0], $row[1]);   
          }
      sqlsrv_free_stmt( $stmt);
      sqlsrv_close( $cn );    
    }
    catch (Exception $ex)
    { 
       sqlsrv_close( $cn );
       echo $ex;     
    } 
    return  $listaElementos;
  }
  
    
  public function borrarRegistro($tabla, $datoEliminar)
  {   
   $resultado = -1;     
   try
   {          
        $cn = Conexion::obtenerConexion();        
        $params = array(                   
                        array($tabla, SQLSRV_PARAM_IN),  
                        array($datoEliminar, SQLSRV_PARAM_IN),  
                        array(&$resultado, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_INT),
                        );                  
        $callSP = "{CALL SPR_D_Registro(?,?,?)}";
        $stmt=sqlsrv_query($cn, $callSP, $params);
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt( $stmt);
        sqlsrv_close( $cn );
   }
   catch (Exception $ex)
   {
       sqlsrv_close($cn);
       echo $ex;
   }  
  }
  
}
