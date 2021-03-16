<?php

class Dao_Operario extends Dao_General implements IDao_Operario {
    
  public function obtenerAcceso($documento, $clave)
  {  
    $operario = new Operario(); 
    $vecresultado = array();
    try
    { 
        $cn = Conexion::obtenerConexion(); 
        $rs = oci_parse($cn, "call SPR_R_ObtenerAcceso('" . $documento . "', '" . $clave . "', :rc)");
        $refcur = oci_new_cursor($cn);
        oci_bind_by_name($rs, ':rc', $refcur, -1, OCI_B_CURSOR);
        oci_execute($rs);
        oci_execute($refcur); 
        while($row = oci_fetch_array($refcur, OCI_NUM)) {
              array_push($vecresultado, $row);
        }
        oci_free_statement($refcur); 
        oci_close($cn);
     if ($vecresultado!= NULL)
     {  
        $operario->setOperario_id($vecresultado[0][0]); 
        $operario->setNombres($vecresultado[0][1]);
        $operario->setApellidos($vecresultado[0][2]);     
        $operario->setPerfil($vecresultado[0][3]);           
        unset($vecresultado);
     }
     else
     {
        $operario = NULL;
     }
    }
    catch (Exception $ex)
    {
      echo $ex;
    }
    return $operario;
  }
  
  public function obtenerOperario($datoBuscar)
  {  
    $operario = new Operario();
    try
    { 
     $vecr = parent::buscarRegistro('TBL_OPERARIOS', $datoBuscar);
     if ($vecr!= NULL)
     {
        $operario->setOperario_id($vecr[0][0]);
        $operario->setDocumento($vecr[0][1]);
        $operario->setNombres($vecr[0][2]);
        $operario->setApellidos($vecr[0][3]);
        $operario->setTelefono($vecr[0][4]);
        $operario->setCorreo($vecr[0][5]);   
        $operario->setFoto(!empty($vecr[0][6])?($vecr[0][6]):NULL);   
        unset($vecr);
     }
     else
     {
         $operario = NULL;
     }
    }
    catch (Exception $ex)
    {
      echo $ex;
    }
    return $operario;
  }

  public function guardarOperario($operario, $usuario)
  {    
      $result = -1;
      try 
      {           
        $cn = Conexion::obtenerConexion(); 
        $stid = oci_parse($cn, "CALL SPR_IU_Operarios( '" . $operario->getOperario_id() . "',
                                                       '" . $operario->getDocumento() . "',
                                                       '" . $operario->getNombres() . "', 
                                                       '" . $operario->getApellidos() . "', 
                                                       '" . $operario->getTelefono() . "', 
                                                       '" . $operario->getCorreo() . "',
                                                       '" . $operario->getFoto() . "', 
                                                       '" . $usuario . "',                                                                          
                                                       :result)");

       oci_bind_by_name($stid, ':result', $result, 2);
       oci_execute($stid);
       oci_free_statement($stid);       
       oci_close($cn);  
      }
      catch (Exception $ex)
      {
        echo $ex;
      }         
     return $result; 
  }
  
  public function guardarCambioClave($claveanterior, $clavenueva, $usuario)
  {    
    $result = -1;  
    try
    {        
        $cn = Conexion::obtenerConexion(); 
        $stid = oci_parse($cn, "CALL SPR_U_CambioClave( '" . $usuario . "', '" . $claveanterior . "', '" . $clavenueva . "', :result)");
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

    public function cargarListado($opcion) {
        $listaElementos = array();        
        $listaElementos = parent::cargarListas("TBL_OPERARIOS", $opcion);
        return $listaElementos;
    }
    
    public function eliminarRegistro($datoEliminar) {
      $result = parent::borrarRegistro("TBL_OPERARIOS", $datoEliminar);
      return $result;
    }
}
