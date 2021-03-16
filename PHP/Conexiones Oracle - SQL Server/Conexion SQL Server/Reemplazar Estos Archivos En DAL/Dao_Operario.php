<?php

class Dao_Operario extends Dao_General implements IDao_Operario {
    
  public function obtenerAcceso($documento, $clave)
  {  
    $operario = new Operario();      
    try
    { 
        $cn = Conexion::obtenerConexion();    
        $params = array(                 
                  array($documento, SQLSRV_PARAM_IN),  
                  array($clave, SQLSRV_PARAM_IN)
                );         
        $callSP = '{CALL SPR_R_obtenerAcceso(?,?)}';
        $stmt=sqlsrv_query($cn, $callSP, $params);        
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )
        {
           array_push($vecresultado, $row);
        }
        sqlsrv_free_stmt( $stmt);
        sqlsrv_close( $cn );
        if ($vecr!= NULL)
        {
           $operario->setOperario_id($vecresultado[0][0]);          
           $operario->setNombres($vecresultado[0][1]);
           $operario->setApellidos($vecresultado[0][2]);     
           $operario->setPerfil($vecresultado[0][3]);       
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

  public function obtenerOperario($datoBuscar)
  {  
    $operario = new Operario();
    try
    { 
        $vecr = AccesoDatos::buscarRegistro('TBL_OPERARIOS', $datoBuscar);
        if ($vecr!= NULL)
        {
           $operario->setOperario_id($vecr[0][0]); 
           $operario->setDocumento($vecr[0][1]);
           $operario->setNombres($vecr[0][2]);
           $operario->setApellidos($vecr[0][3]);
           $operario->setTelefono($vecr[0][4]);
           $operario->setCorreo($vecr[0][5]);   
           $operario->setFoto($vecr[0][6]);
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
      $resultado = -1;
      try 
      {             
        $cn = Conexion::obtenerConexion();       
        $params = array(                   
                        array($operario->getOperario_id(), SQLSRV_PARAM_IN),  
                        array($operario->getDocumento(), SQLSRV_PARAM_IN),                  
                        array($operario->getNombres(), SQLSRV_PARAM_IN),    
                        array($operario->getApellidos(), SQLSRV_PARAM_IN),   
                        array($operario->getTelefono(), SQLSRV_PARAM_IN),   
                        array($operario->getCorreo(), SQLSRV_PARAM_IN),  
                        array($operario->getFoto(), SQLSRV_PARAM_IN),   
                        array($usuario, SQLSRV_PARAM_IN),   
                        array(&$resultado, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_INT),
                        );  
          $callSP = "{CALL SPR_IU_Operarios(?,?,?,?,?,?,?,?,?)}";
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
      return $resultado;
  }
  
  public function guardarCambioClave($claveanterior, $clavenueva, $usuario)
  {    
    $resultado = -1;  
    try
    {        
        $cn = Conexion::obtenerConexion();         
        $params = array(
                        array($usuario, SQLSRV_PARAM_IN),            
                        array($claveanterior, SQLSRV_PARAM_IN),
                        array($clavenueva, SQLSRV_PARAM_IN),                      
                        array(&$resultado, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_INT),
                        );                  
        $callSP = "{CALL SPR_U_CambioClave(?,?,? ?)}";
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
    return $resultado;
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
