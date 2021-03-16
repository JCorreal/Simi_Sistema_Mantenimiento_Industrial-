<?php
    
class Dao_ListaValores extends Dao_General implements IDao_ListaValores {
  
  public function obtenerListaValores($datoBuscar)
  {  
    $listavalores = new ListaValores();
    try
    {
      $vecr = parent::buscarRegistro('TBL_LISTAVALORES', $datoBuscar);
      if ($vecr!= NULL)
      {
        $listavalores->setListaValores_id($vecr[0][0]);
        $listavalores->setNombre($vecr[0][1]);
        $listavalores->setDescripcion(!empty($vecr[0][2])?($vecr[0][2]):NULL);   
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
     $result = -1; 
     try
     {          
        $cn = Conexion::obtenerConexion();  
        $stid = oci_parse($cn, "call SPR_IU_ListaValores( '" . $listavalores->getListaValores_id() . "', 
                                                          '" . $listavalores->getNombre() . "', 
                                                          '" . $listavalores->getDescripcion() . "', 
                                                          '" . $listavalores->getTipo() . "', 
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
