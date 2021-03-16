<?php
     
class Dao_Mantenimiento extends Dao_General implements IDao_Mantenimiento{
     
  public function obtenerMantenimiento($datoBuscar)
  {  
    $mantenimiento = new Mantenimiento();
    try
    {      
      $vecr = AccesoDatos::buscarRegistro('TBL_MANTENIMIENTO', $datoBuscar);
      if ($vecr!= NULL)
      {
        $mantenimiento->setMantenimiento_id($vecr[0][0]);        
        $mantenimiento->setEquipo_id($vecr[0][1]);
        $mantenimiento->setOperario_id($vecr[0][2]);        
        $mantenimiento->setFecha($vecr[0][3]);
        $mantenimiento->setObservaciones($vecr[0][4]);
        unset($vecr);
      }
      else
      {
          $mantenimiento = NULL;
      }
    }
   catch (Exception $ex)
   {
       echo $ex;
   }
   return $mantenimiento;
  }
  
  public function guardarMantenimiento($mantenimiento, $usuario)
  {  
    $resultado = -1;  
    try 
     {     
        $cn = Conexion::obtenerConexion();         
        $params = array(                   
                        array($mantenimiento->getMantenimiento_id(), SQLSRV_PARAM_IN),  
                        array($mantenimiento->getEquipo_id(), SQLSRV_PARAM_IN),                  
                        array($mantenimiento->getOperario_id(), SQLSRV_PARAM_IN),    
                        array($mantenimiento->getFecha(), SQLSRV_PARAM_IN),   
                        array($mantenimiento->getObservaciones(), SQLSRV_PARAM_IN),   
                        array($usuario, SQLSRV_PARAM_IN),   
                        array(&$resultado, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_INT),
                        );  
          $callSP = "{CALL SPR_IU_Mantenimiento(?,?,?,?,?,?,?)}";
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
  
 
    public function controlarProgramacion($tabla) {
        $listaElementos = array();        
        $listaElementos = parent::controlProgramacion($tabla);
        return $listaElementos;
    }
    
    public function cargarListado($opcion) {
        $listaElementos = array();        
        $listaElementos = parent::cargarListas("TBL_MANTENIMIENTO", $opcion);
        return $listaElementos;
    }
    
    public function eliminarRegistro($datoEliminar) {
      $result = parent::borrarRegistro("TBL_MANTENIMIENTO", $datoEliminar);
      return $result;
    }
}
