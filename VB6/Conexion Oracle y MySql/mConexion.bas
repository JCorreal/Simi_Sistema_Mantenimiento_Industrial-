Attribute VB_Name = "mConexion"
Option Explicit

Public Cn As New ADODB.Connection 'Alias para la conexión
Public Const Conexion As String = ("DRIVER={MySQL ODBC 5.3 UNICODE Driver}; SERVER=localhost; DATABASE=SIMIDB; Password=; UID=root;PORT=3306")

'======================================================================================================================================================
 ' Si se desea conectar con Oracle o MySql, reemplazar todo el modulo mAccesoDatos por el adjunto en la carpeta: Conexion Oracle o MySql
 
 ' Y la siguiente línea si es con Oracle:
 ' Public Const Conexion As String = ("Provider=MSDAORA.1;Password=tiger;User ID=CONTROLMANTENIMIENTODB;Data Source=XE;Persist Security Info=True")
 
 ' Y la siguiente línea si es con MySql:
 ' Public Const Conexion As String = ("DRIVER={MySQL ODBC 5.3 UNICODE Driver}; SERVER=localhost; DATABASE=CONTROLMANTENIMIENTODB; UID=root;PORT=3306")
 
 ' Si elige conectar con MySql requiere descargar el conector: mysql-connector-odbc-5.3.6-win32
 
 ' El modulo mAccesoDatos no cambia entre Oracle y MySQl, es transparente para ambos, pero si tiene unas sutiles diferencias con SQL Server
'======================================================================================================================================================


Public Sub AbrirConexion()
 On Error GoTo ControlarErrores
 Cn.Open (Conexion)
ControlarErrores: 'Controlar error en caso de fallas en la conexión con servidor
 If (Err.Number <> 0) Then '-2147467259
     Err.Clear
     MsgBox MensajeErrorConexion, 16, MensajeAplicacion
     End
 End If
 
 
End Sub

