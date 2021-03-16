<?php

class Dao_Equipo extends Dao_General implements IDao_Equipo{
    
   public function obtenerEquipo($datoBuscar)
  {     
    $equipo = new Equipo();  
    try 
    {
      $vecr = AccesoDatos::buscarRegistro('TBL_EQUIPOS', $datoBuscar);
      if ($vecr!= NULL)
      {
        $equipo->setEquipo_id($vecr[0][0]);
        $equipo->setNombre_equipo($vecr[0][1]);
        $equipo->setMarca($vecr[0][2]);
        $equipo->setSerie($vecr[0][3]);
        $equipo->setLinea($vecr[0][4]);
        $equipo->setLubricacion($vecr[0][5]);  
        unset($vecr);
      }
      else
      {
          $equipo = NULL;
      }
    }
    catch (Exception $ex)
    {
       echo $ex;
    }
    return $equipo;
  }

  public function guardarEquipo($equipo, $usuario)
  {  
    $resultado = -1;  
    try
    {          
        $cn = Conexion::obtenerConexion();       
        $params = array(                   
                        array($equipo->getEquipo_id(), SQLSRV_PARAM_IN),                  
                        array($equipo->getNombre_equipo(), SQLSRV_PARAM_IN),    
                        array($equipo->getMarca(), SQLSRV_PARAM_IN),   
                        array($equipo->getSerie(), SQLSRV_PARAM_IN),
                        array($equipo->getLinea(), SQLSRV_PARAM_IN),
                        array($equipo->getLubricacion(), SQLSRV_PARAM_IN),
                        array($usuario, SQLSRV_PARAM_IN),   
                        array(&$resultado, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_INT),
                        );  
          $callSP = "{CALL SPR_IU_Equipos(?,?,?,?,?,?,?,?)}";
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
 
    public function controlarProgramacion() {
        $listaElementos = array();        
        $listaElementos = parent::controlProgramacion("CONTROLEQUIPOS");
        return $listaElementos;
    }
    
    public function cargarListado($opcion) {
        $listaElementos = array();        
        $listaElementos = parent::cargarListas("TBL_EQUIPOS", $opcion);
        return $listaElementos;
    }
    
    public function eliminarRegistro($datoEliminar) {
      $result = parent::borrarRegistro("TBL_EQUIPOS", $datoEliminar);
      return $result;
    }

}
