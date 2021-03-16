'===================================================================================================================
 ' Si bien el lenguaje VB 6.0, fue bien popular por allá en la década del 90, porque a decir verdad satisfacía las
 ' necesidades del momento, éste se quedó corto con la orientación a objetos, apenas alcanzaba a simular algo de ello,
 ' pero sin la madurez de un lenguaje típicamente OO.
 ' Esa simulación de OO en VB 6 es lo que he intentado realizar acá, pero nótese que no es posible establecer un
 ' método constructor, por lo cual se simula con un Init, y desde luego tampoco se puede construir una interface y un
 ' controlador, que implemente dicha interface, tan útiles y necesarios hoy cuando aplicamos MVC.
 ' No es lo mismo pero bueno, ante las carencias del lenguaje tocaba hacerlo así.
'===================================================================================================================

' Bien como se puede apreciar no se disparan Querys desde la aplicación, todo se realiza en el lado del servidor,
' lo que se traduce en mayor eficiencia y velocidad en tiempo de respuesta.
' Todo lo que tiene que ver con acceso a datos está centralizado en este módulo, que bien podría separarse aún más
' si se quisiera, diseñando un módulo DAL para cada una de las estructuras de la BD, como lo recomiendan las buenas
' prácticas de los expertos, pero para este caso se puede dejar así y funciona bastante bien

Option Explicit

Dim Cmd               As New Command         'Objeto de tipo Command para acceder a Procedimientos Almacenados
Dim Rst1              As New ADODB.Recordset 'Cursor - Recordset
Public COPerario      As Operario            'Clase Operario
Public CEquipo        As Equipo              'Clase Equipo
Public CListaValores  As ListaValores        'Clase ListaValores
Public CMantenimiento As Mantenimiento       'Clase Mantenimiento

Private Sub BuscarRegistro(Tabla As String, DatoBuscar As Integer) 
 AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_R_BuscarRegistro"
      .Parameters("p_TABLA") = Tabla
      .Parameters("p_DATOBUSCAR") = DatoBuscar      
 End With
 AbrirRecordset
End Sub

Private Sub AbrirRecordset()         
 Set Rst1 = Nothing		     		 'Este es el tipico cursor "Manguera" denominado asi por algunos autores dada su rapidez
 Rst1.CursorLocation = adUseServer   'Declarado en el servidor, para que se gestione alla y evitar asi sobrecarga en el front
 Rst1.CursorType = adOpenForwardOnly 'De recorrido solo hacia adelante ya que es mas liviano, ideal para cargar listas y combos
 Rst1.LockType = adLockReadOnly      'Este cursor siempre sera de solo lectura, sin bloqueos
 Rst1.Open Cmd.Execute
End Sub

Public Sub CargarCombosListas(mCombo As Control, Tabla As String) 'Procedimiento para carga de Combos y Listas
 On Error GoTo ControlarErrores
 AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_R_CargarCombosListas"
      .Parameters("p_TABLA") = Tabla
 End With
 AbrirRecordset
 mCombo.Clear
 While Not Rst1.EOF
       mCombo.AddItem Rst1.Fields(0).Value & " " & Rst1.Fields(1).Value
       mCombo.ItemData(mCombo.NewIndex) = Rst1.Fields(0).Value
       Rst1.MoveNext
 Wend
 If (TypeOf mCombo Is ComboBox) Then mCombo.ListIndex = 0
 LiberarRecursos
 Exit Sub
ControlarErrores:
 Err.Clear
 LiberarRecursos
End Sub

Public Sub ControlProgramacion(mCombo1 As Control, mCombo2 As Control, mCombo3 As Control, Tabla As String) 'Procedimiento para carga de Combos en Mantenimiento
 On Error GoTo ControlarErrores
 AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_R_CargarCombosListas"
      .Parameters("p_TABLA") = Tabla
 End With
 AbrirRecordset
 mCombo1.Clear
 mCombo2.Clear
 mCombo3.Clear
 While Not Rst1.EOF
       If (Rst1.Fields(2).Value = "EQUIPOS") Then
           mCombo1.AddItem Rst1.Fields(1).Value
           mCombo1.ItemData(mCombo1.NewIndex) = Rst1.Fields(0).Value
       ElseIf (Rst1.Fields(2).Value = "OPERARIOS") Then
               mCombo2.AddItem Rst1.Fields(1).Value
               mCombo2.ItemData(mCombo2.NewIndex) = Rst1.Fields(0).Value
       ElseIf (Rst1.Fields(2).Value = "LINEAS") Then
               mCombo2.AddItem Rst1.Fields(1).Value
               mCombo2.ItemData(mCombo2.NewIndex) = Rst1.Fields(0).Value
       ElseIf (Rst1.Fields(2).Value = "MARCAS") Then
               mCombo3.AddItem Rst1.Fields(1).Value
               mCombo3.ItemData(mCombo3.NewIndex) = Rst1.Fields(0).Value
       End If
       Rst1.MoveNext
 Wend
 mCombo1.ListIndex = 0
 mCombo2.ListIndex = 0
 mCombo3.ListIndex = 0
 LiberarRecursos
 Exit Sub
ControlarErrores:
 Err.Clear
 LiberarRecursos
End Sub

Public Sub EstablecerCombo(mCombo As ComboBox, mClave As Double) 'Procedimiento para posicionar el Combo en un dato especifico
 On Error GoTo ControlarErrores
 Dim i As Double
 For i = 0 To mCombo.ListCount
     If (mCombo.ItemData(i)) = mClave Then
         mCombo.ListIndex = i
         Exit For
     End If
 Next i
 Exit Sub
ControlarErrores:
 Err.Clear
End Sub

'=======================================================================================================================================================
'Inicio Operaciones sobre estructura Operarios
'=======================================================================================================================================================

Public Sub ObtenerAcceso(Documento As String, Clave As Integer) 'Buscar Operario
 On Error GoTo ControlarErrores
  AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_R_ObtenerAcceso"
      .Parameters("p_DOCUMENTO") = Documento
      .Parameters("p_CLAVE") = Clave
 End With
 AbrirRecordset
 If (Not Rst1.EOF) Then
     Set COPerario = mFactory.Nuevo_Operario(Rst1!OPerario_id, Rst1!Documento, Rst1!Nombres, Rst1!Apellidos, "", "", "", Rst1!Perfil)  'Llamar Clase Operario
 End If
 LiberarRecursos
 Exit Sub
ControlarErrores:
 Err.Clear
 LiberarRecursos
End Sub

Public Sub ObtenerOperario(DatoBuscar As Integer) 'Buscar Operario
 On Error GoTo ControlarErrores
 BuscarRegistro "TBL_OPERARIOS", DatoBuscar
 If (Not Rst1.EOF) Then
     Set COPerario = mFactory.Nuevo_Operario(Rst1!Operario_id, Rst1!Documento, Rst1!Nombres, Rst1!Apellidos, IIf(IsNull(Rst1!Correo), "", Rst1!Correo), Rst1!Telefono, IIf(IsNull(Rst1!Foto), "", Rst1!Foto), 3) 'Llamar Clase Operario
 End If
 LiberarRecursos
 Exit Sub
ControlarErrores:
 Err.Clear
 LiberarRecursos
End Sub

Public Function GuardarCambioClave(Usuario As Integer, ClaveAnterior As Integer, ClaveNueva As Integer) As Integer  'Modificar Clave de Acceso
 On Error GoTo ControlarErrores
 AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_U_CambioClave"
      .Parameters("p_OPERARIO_ID") = Usuario
      .Parameters("p_CLAVE_ANTERIOR") = ClaveAnterior
      .Parameters("p_CLAVE_NUEVA") = ClaveNueva
      .Parameters("p_RESULTADO").Direction = adParamOutput
      .Execute
 End With
 GuardarCambioClave = Cmd.Parameters("p_RESULTADO").Value
 LiberarRecursos
 Exit Function
ControlarErrores:
 Err.Clear
 GuardarCambioClave = False
 LiberarRecursos
End Function

Public Function GuardarOPerario(COPerario As Operario, Usuario As Integer) As Integer 'Guardar Operario
 On Error GoTo ControlarErrores
 AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_IU_Operarios"
      .Parameters("P_OPERARIO_ID") = COPerario.Operario_id
      .Parameters("p_DOCUMENTO") = COPerario.Documento
      .Parameters("p_NOMBRES") = COPerario.Nombres
      .Parameters("p_APELLIDOS") = COPerario.Apellidos
      .Parameters("p_TELEFONO") = COPerario.Telefono
      .Parameters("p_CORREO") = COPerario.Correo
      .Parameters("p_FOTO") = COPerario.Foto
      .Parameters("p_USUARIOCONECTADO") = UsuarioConectado
      .Parameters("p_RESULTADO").Direction = adParamOutput
      .Execute
 End With
 GuardarOPerario = (Cmd.Parameters("p_RESULTADO").Value)
 LiberarRecursos
 Exit Function
ControlarErrores:
 GuardarOPerario = 2
 Err.Clear
 LiberarRecursos
End Function

'=======================================================================================================================================================
' Fin Operaciones sobre estructura Operarios
'=======================================================================================================================================================

'=======================================================================================================================================================
'Inicio Operaciones sobre estructura Equipos
'=======================================================================================================================================================

Public Sub ObtenerEquipo(Codigo As Integer) 'Buscar Equipo
 On Error GoTo ControlarErrores
 BuscarRegistro "TBL_EQUIPOS", Codigo
 Set CEquipo = mFactory.Nuevo_Equipo(Rst1.Fields(0), Rst1.Fields(1), Rst1.Fields(2), Rst1.Fields(3), Rst1.Fields(4), IIf(Rst1.Fields(5) = 1, 1, 0))       'Llamar Clase
 LiberarRecursos
 Exit Sub
ControlarErrores:
 Err.Clear
 LiberarRecursos
End Sub

Public Function GuardarEquipo(CEquipo As Equipo, Usuario As Integer) As Integer 'Guardar Equipo - Maquinaria
 On Error GoTo ControlarErrores
 AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_IU_Equipos"
      .Parameters("p_EQUIPO_ID") = CEquipo.Equipo_id
      .Parameters("p_NOMBRE_EQUIPO") = CEquipo.Nombre_equipo
      .Parameters("p_MARCA") = CEquipo.Marca
      .Parameters("p_SERIE") = CEquipo.Serie
      .Parameters("p_LINEA") = CEquipo.Linea
      .Parameters("p_LUBRICACION") = CEquipo.Lubricacion
      .Parameters("p_USUARIOCONECTADO") = UsuarioConectado
      .Parameters("p_RESULTADO").Direction = adParamOutput
      .Execute
 End With
 GuardarEquipo = (Cmd.Parameters("p_RESULTADO").Value)
 LiberarRecursos
 Exit Function
ControlarErrores:
 Err.Clear
 GuardarEquipo = 2
 LiberarRecursos
End Function

'=======================================================================================================================================================
' Fin Operaciones sobre estructura Equipos
'=======================================================================================================================================================

'=======================================================================================================================================================
' Inicio Operaciones sobre estructura ListaValores
'=======================================================================================================================================================

Public Sub ObtenerListaValores(Codigo As Integer) 'Obtener Marca o Linea
 On Error GoTo ControlarErrores
 BuscarRegistro "TBL_LISTAVALORES", Codigo
 Set CListaValores = mFactory.Nuevo_ListaValores(Rst1.Fields(0), Rst1.Fields(1), IIf(IsNull(Rst1.Fields(2)), "", Rst1.Fields(2)), ValorTipo)    'Llamar Clase
 LiberarRecursos
 Exit Sub
ControlarErrores:
 Err.Clear
 LiberarRecursos
End Sub

Public Function GuardarListaValores(CListaValores As ListaValores, Usuario As Integer) As Integer 'Guardar Marca y/o Linea
 On Error GoTo ControlarErrores
 AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_IU_ListaValores"
      .Parameters("p_LISTAVALORES_ID") = CListaValores.ListaValores_id
      .Parameters("p_NOMBRE") = CListaValores.Nombre
      .Parameters("p_DESCRIPCION") = CListaValores.Descripcion
      .Parameters("p_TIPO") = CListaValores.Tipo
      .Parameters("p_USUARIOCONECTADO") = UsuarioConectado
      .Parameters("p_RESULTADO").Direction = adParamOutput
      .Execute
 End With
 GuardarListaValores = (Cmd.Parameters("p_RESULTADO").Value)
 LiberarRecursos
 Exit Function
ControlarErrores:
 Err.Clear
 GuardarListaValores = 2
 LiberarRecursos
End Function

'=======================================================================================================================================================
' Fin Operaciones sobre estructura ListaValores
'=======================================================================================================================================================

'=======================================================================================================================================================
' Inicio Operaciones sobre estructura Mantenimiento
'=======================================================================================================================================================

Public Sub ObtenerMantenimiento(Codigo As Integer) 'Obtener un Mantenimiento programado
 On Error GoTo ControlarErrores
 BuscarRegistro "TBL_MANTENIMIENTO", Codigo
 If (Not Rst1.EOF) Then
     Set CMantenimiento = mFactory.Nuevo_Mantenimiento(Rst1.Fields(0), Rst1.Fields(1), Rst1.Fields(2), Rst1.Fields(3), IIf(IsNull(Rst1!Observaciones), "", Rst1!Observaciones))
 End If
 LiberarRecursos
 Exit Sub
ControlarErrores:
 Err.Clear
 LiberarRecursos
End Sub

Public Function GuardarMantenimiento(CMantenimiento As Mantenimiento, Usuario As Integer) As Integer 'Guardar una Programacion de Mantenimiento
 On Error GoTo ControlarErrores
 AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_IU_Mantenimiento"
      .Parameters("p_MANTENIMIENTO_ID") = CMantenimiento.Mantenimiento_id
      .Parameters("p_EQUIPO_ID") = CMantenimiento.Equipo_id
      .Parameters("p_OPERARIO_ID") = CMantenimiento.Operario_id
      .Parameters("p_FECHA") = CMantenimiento.Fecha
      .Parameters("p_OBSERVACIONES") = CMantenimiento.Observaciones
      .Parameters("p_USUARIOCONECTADO") = UsuarioConectado
      .Parameters("p_RESULTADO").Direction = adParamOutput
      .Execute
 End With
 GuardarMantenimiento = (Cmd.Parameters("p_RESULTADO").Value)
 LiberarRecursos
 Exit Function
ControlarErrores:
 Err.Clear
 GuardarMantenimiento = 2
 LiberarRecursos
End Function

'=======================================================================================================================================================
' Fin Operaciones sobre estructura Mantenimiento
'=======================================================================================================================================================

Public Function EliminarRegistro(DatoEliminar As Integer, Tabla As String) As Integer 'Procedimiento de borrado de Registros
 On Error GoTo ControlarErrores
 AbrirConexion
 With Cmd
      .ActiveConnection = Cn
      .CommandType = adCmdStoredProc
      .CommandText = "SPR_D_Registro"
      .Parameters("p_TABLA") = Tabla
      .Parameters("p_CONDICION") = DatoEliminar
      .Parameters("p_RESULTADO").Direction = adParamOutput
      .Execute
 End With
 EliminarRegistro = (Cmd.Parameters("p_RESULTADO").Value)
 LiberarRecursos
 Exit Function
ControlarErrores:
 Err.Clear
 EliminarRegistro = 2
 LiberarRecursos
End Function

Private Sub LiberarRecursos() 'Cerrar Cursor, Command y Conexion
 Set Rst1 = Nothing
 Set Cmd = Nothing
 If (Cn.State = 1) Then
     Cn.Close
 End If
 Set Cn = Nothing
End Sub



