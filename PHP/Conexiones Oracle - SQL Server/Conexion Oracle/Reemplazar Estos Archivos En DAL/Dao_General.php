<?php

class Dao_General {
    
  public function buscarRegistro($tabla, $datoBuscar)
  { // Funcion para buscar un registro especifico             
    $vecresultado = array(); 
     try 
     {          
          $cn = Conexion::obtenerConexion(); 
          $rs = oci_parse($cn, "call SPR_R_BuscarRegistro('" . $tabla . "', '" . $datoBuscar . "', :rc)");
          // Recorremos el resultado de la consulta y lo almacenamos en el array
          $refcur = oci_new_cursor($cn);
          oci_bind_by_name($rs, ':rc', $refcur, -1, OCI_B_CURSOR);
          oci_execute($rs);
          oci_execute($refcur); 
          while($row = oci_fetch_array($refcur, OCI_NUM)) {
                array_push($vecresultado, $row);
          }
          oci_free_statement($refcur); 
          oci_close($cn);
     }
     catch (Exception $ex)
     { 
       oci_close($cn);
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
      $rs = oci_parse($cn, "CALL SPR_R_CargarListado('" . $tabla . "', '" . $opcion . "', :rc)");
      $refcur = oci_new_cursor($cn);
      oci_bind_by_name($rs, ':rc', $refcur, -1, OCI_B_CURSOR);
      oci_execute($rs);
      oci_execute($refcur); 
      while($row = oci_fetch_array($refcur, OCI_NUM)) {
            array_push($listaElementos, $row);        
      }
      oci_free_statement($refcur); 
      oci_close($cn);
    }
    catch (Exception $ex)
    { 
       oci_close($cn); 
       echo $ex;     
    }
    return $listaElementos;
  }
   
  public function controlProgramacion($tabla)
  { 
     $listaElementos = array();       
    try
    {
      $cn = Conexion::obtenerConexion();   
      $rs = oci_parse($cn, "CALL SPR_R_CargarCombosListas('" . $tabla . "', :rc)");
      $refcur = oci_new_cursor($cn);
      oci_bind_by_name($rs, ':rc', $refcur, -1, OCI_B_CURSOR);
      oci_execute($rs);
      oci_execute($refcur);  
       while($row = oci_fetch_array($refcur, OCI_NUM)) {             
             array_push($listaElementos, $row[2]);
             array_push($listaElementos, $row[0]);
             array_push($listaElementos, $row[1]);             
      }
      oci_free_statement($refcur); 
      oci_close($cn); 
    }
    catch (Exception $ex)
    { 
       oci_close($cn); 
       echo $ex;     
    } 
    return $listaElementos;   
  }
  
  public function borrarRegistro($tabla, $datoEliminar)
  {
   $result = -1;  
    try
    {            
       $cn = Conexion::obtenerConexion();   
       $stid = oci_parse($cn, "CALL SPR_D_Registro( '" . $tabla . "', '" . $datoEliminar . "',  :result)");
       oci_bind_by_name($stid, ':result', $result, 2);
       oci_execute($stid);
       oci_free_statement($stid);       
       oci_close($cn);  
    }
    catch (Exception $ex)
    {
        oci_close($cn);
        echo $ex;
    }  
    return $result;  
  }
}
