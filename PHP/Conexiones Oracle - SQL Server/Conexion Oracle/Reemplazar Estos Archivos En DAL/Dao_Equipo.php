<?php
     
class Dao_Equipo extends Dao_General implements IDao_Equipo{
    
 public function obtenerEquipo($datoBuscar)
  { 
    $equipo = new Equipo();      
    try 
    {
      $vecr = parent::buscarRegistro('TBL_EQUIPOS', $datoBuscar);
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
    $result = -1;   
    try
    {              
        $cn = Conexion::obtenerConexion(); 
        $stid = oci_parse($cn, "CALL SPR_IU_Equipos( '" . $equipo->getEquipo_id() . "', 
                                                     '" . $equipo->getNombre_equipo() . "',
                                                     '" . $equipo->getMarca() . "', 
                                                     '" . $equipo->getSerie() . "', 
                                                     '" . $equipo->getLinea() . "',
                                                     '" . $equipo->getLubricacion() . "', 
                                                     '" . $usuario . "',
                                                     :result)");        
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
