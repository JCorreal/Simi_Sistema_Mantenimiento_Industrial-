<?php
   
class Dao_ListaValores extends Dao_General implements IDao_ListaValores {
  
   public function obtenerListaValores($datoBuscar)
  {  
    $listavalores = new ListaValores();
    try
    {
      $vecr = AccesoDatos::buscarRegistro('TBL_LISTAVALORES', $datoBuscar);
      if ($vecr!= NULL)
      {
        $listavalores->setListaValores_id($vecr[0][0]);
        $listavalores->setNombre($vecr[0][1]);
        $listavalores->setDescripcion($vecr[0][2]);
        $listavalores->setTipo($vecr[0][3]);   
        unset($vecr);
      }
      else
      {
          $listavalores = NULL;
      }
   }
   catch (Exception $ex)
   {
       echo $ex;
   }
   return $listavalores;   
  }

  public function guardarListaValores($listavalores, $usuario)
  {      
    $resultado = -1;
    try
     {
        $cn = Conexion::obtenerConexion();        
        $params = array(                   
                        array($listavalores->getListaValores_id(), SQLSRV_PARAM_IN),                  
                        array($listavalores->getNombre(), SQLSRV_PARAM_IN),    
                        array($listavalores->getDescripcion(), SQLSRV_PARAM_IN),   
                        array($listavalores->getTipo(), SQLSRV_PARAM_IN),
                        array($usuario, SQLSRV_PARAM_IN),   
                        array(&$resultado, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_INT),
                        );  
          $callSP = "{CALL SPR_IU_ListaValores(?,?,?,?,?,?)}";
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
 
   
    public function cargarListado($tabla, $opcion) {
        $listaElementos = array();        
        $listaElementos = parent::cargarListas($tabla, $opcion);
        return $listaElementos;
    }
    
    public function eliminarRegistro($datoEliminar) {
      $result = parent::borrarRegistro("TBL_LISTAVALORES", $datoEliminar);
      return $result;
    }
}
