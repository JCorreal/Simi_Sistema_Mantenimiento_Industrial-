<?php

class Dao_Mantenimiento extends Dao_General implements IDao_Mantenimiento{
     
  public function obtenerMantenimiento($datoBuscar)
  {  
    $mantenimiento = new Mantenimiento();
    try
    {      
      $vecr = parent::buscarRegistro('TBL_MANTENIMIENTO', $datoBuscar);
      if ($vecr!= NULL)
      {
         $mantenimiento->setMantenimiento_id($vecr[0][0]);
         $mantenimiento->setEquipo_id($vecr[0][1]);
         $mantenimiento->setOperario_id($vecr[0][2]);        
         $mantenimiento->setFecha($vecr[0][3]);
         $mantenimiento->setObservaciones(!empty($vecr[0][4])?($vecr[0][4]):NULL);           
         unset($vecr);
         return $mantenimiento;
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
    $result = -1;  
     try 
     {              
        $cn = Conexion::obtenerConexion();  
        $stid = oci_parse($cn, "CALL SPR_IU_Mantenimiento( '" . $mantenimiento->getMantenimiento_id() . "', 
                                                           '" . $mantenimiento->getEquipo_id() . "', 
                                                           '" . $mantenimiento->getOperario_id() . "',
                                                           '" . date_format(new DateTime ($mantenimiento->getFecha()), 'd/M/Y' ) . "',   
                                                           '" . $mantenimiento->getObservaciones() . "', 
                                                           '" . $usuario . "', 
                                                           :result)");
         
        oci_bind_by_name($stid, ':result', $result);
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
