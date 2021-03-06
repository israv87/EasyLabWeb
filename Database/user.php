<?php
//llamamos a la coneccxiona la base de datos
include 'config.php';

class User extends DB
{
    //definimos variables para almacenar datos que obtengamos de la base
    private $p_nombre;
    private $s_nombre;
    private $p_apellido;
    private $m_apellido;
    private $username;
    private $u_rol;
    //funcion para comparar los datos que el usuario ingresa con la base de datos
    public function userExists($user, $pass)
    {
        $md5pass = md5($pass);//Convertimos la contraseña que ingresó el usuario en el formulario a un cifrado md5 para mayor seguridad
        $query = $this->connect()->prepare('SELECT * FROM usuarios WHERE usuario = :user AND  password = :pass'); //preparamos consulta sql
        $query->execute(['user' => $user, 'pass' => $md5pass]); //Ejecutamos la consulta buscando en la base de datos el usuario y la contraseña que se ingresó
        if ($query->rowCount()) {//validamos si existe un usuario
            return true;//Existe
        } else {
            return false;//No Existe
        }
    }
    //funcion para guardar los datos de los estudiantes una vez encontrados en la base de datos
    public function setUser($user)
    {
        $query = $this->connect()->prepare('SELECT * FROM usuarios where usuario =:user');//preparamos consulta sql
        $query->execute(['user' => $user]);//Ejecutamos la consulta buscando en la base de datos si los datos de ingreso pertenencen a un estudiante
        foreach ($query as $currentUser) {//Si el usuario es un estudiante obtenemos sus datos
            //cada dato de la base lo guardamos en una variable
            $this->u_rol = $currentUser['rol']; 
        }
    }

    public function getRol()//obtener el rol de un docete o un estudiante
    {
        return $this->u_rol;//Se guarda la variable obtenida
    }

    public function ListaFuncionalidades()
    {
        $query1 = $this->connect()->prepare('SELECT * FROM funcionalidades'); //preparamos consulta sql
        $query1->execute(); //Ejecutamos la consulta buscando en la base de datos siempre comparando con el usuario que ingresó en el login
        echo ' <table class="table table-striped table-responsive ">
                    <thead>
                        <tr>
                        <th>Fecha</th>
                        <th>Funcionalidad</th>
                        <th>Descripcion</th>
                        <th>Herramienta</th>
                        <th>Archivo</th>
                        <th>Editar</th>
                        <th>Borrar</th>
                        </tr>
                    </thead>
                    <tbody>';
        foreach ($query1 as $currentUser) {
            $idfunc=$currentUser['id_Funcionalidad'];
            $idHerr =$currentUser['FK_Herramientas_F'];
             
      
            echo '
                <tr>
                    <td>' . $currentUser['Fecha'] . '</td>
                    <td>' . $currentUser['Nombre_Funcionalidad'] . '</td>
                    <td>' . $currentUser['Descripcion'] . '</td>';
                    $query2 = $this->connect()->prepare('SELECT nombre FROM herramientas WHERE id_Herramientas='.$idHerr.''); //preparamos consulta sql
                    $query2->execute(); //Ejecutamos la consulta buscando en la base de datos siempre comparando con el usuario que ingresó en el login
                    foreach ($query2 as $currentUser2) {        
                    echo'<td>' . $currentUser2['nombre'] . '</td>';
                    }
                    echo'
                    <td> <a href="Database/SQL/DownloadRecFuncionalidades.php?idFunc='.$idfunc.'"target="_blank"><img src="assets/img/pdf.png"></a> </td>
                    <td>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalF'.$idfunc.'"><i class="fa fa-pencil-square-o" aria-hidden="true" ></i></button>
                        


                    <div id="modalF'.$idfunc.'" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">' . $currentUser['Nombre_Funcionalidad'] . ' </h4>
                        </div>
                        <form class="form-horizontal" method="post" action="Database/SQL/UpdateFuncionalidad.php" enctype="multipart/form-data" >

                        <div class="modal-body">
                        <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                            <input type="hidden" id="idfuncUPDATE" name="idfuncUPDATE" value="'.$idfunc.'">     
                                <label for="Herramienta" class="control-label col-sm-2">Herramienta:</label>
                                <div class="col-sm-12" style="display: block;">';
                                    
                                $query3 = $this->connect()->prepare('SELECT * FROM herramientas');
                                $query3->execute();
                                echo '
                                <select class="form-select"  name="herramientaUP" id="herramientaUP">';
                                foreach ($query3 as $currentUser3) {
                                    $id= $currentUser3['id_Herramientas']; 
                                    echo '
                                    <option name="herramientaUP" id="herramientaU´P" value="'.$id.'">'.$currentUser3['nombre'].'</option>
                                     ';   
                                }
                                echo '</select>
                                <p class="help-block">Selecione la herramienta...</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Titulo" class="control-label col-sm-2">Nombre:</label>
                                <div class="col-sm-12" style="display: block;">
                                    <input type="text" class="form-control" name="nombreUP" id="nombreUP" value="' . $currentUser['Nombre_Funcionalidad'] . '">
                                    <p class="help-block">Nombre de la funcionalidad...</p>
                                </div>
                            </div>
                            </div>
                            <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="descripcion">Descripción</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="descripcionUP" name="descripcionUP" rows="7" >' . $currentUser['Descripcion'] . '</textarea>
                                    <p class="help-block">Descripción de la funcionalidad ....
                                    </p>
                                </div>
                            </div>
                        </div>
                        </div>
                        
                    </div>
                        </div>
                        <div class="modal-footer">
                            
                        <button type="button" id="Limpiar" name="Limpiar" class="btn btn-secondary" aria-label="limpiar" data-dismiss="modal">Salir</button>
                        <button type="subir"  name="subir" class="btn btn-primary" aria-label="subir">enviar</button>
                        </div>
                        </div>
                        </form>
                    </div>
                    </div>
                    </td>
                    <td>
                        <form method="post" action="Database/SQL/BorrarFuncionalidad.php" >
                            <input type="hidden" id="idfunc" name="idfunc" value="'.$idfunc.'">       
                            <button type="submit" name="dlteBtn" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </form>
                    </td>
                    
                </tr> 
                ';
        }
        echo '</tbody>
            </table>';
    }
    
    public function ListaPracticas()
    {
        $query1 = $this->connect()->prepare('SELECT * FROM practicas'); //preparamos consulta sql
        $query1->execute(); //Ejecutamos la consulta buscando en la base de datos siempre comparando con el usuario que ingresó en el login
        echo ' <table class="table table-striped table-responsive ">
                    <thead>
                        <tr>
                        <th>Fecha</th>
                        <th>Practica</th>
                        <th>Descripcion</th>
                        <th>Herramienta</th>
                        <th>Archivo</th>
                        <th>Editar</th>
                        <th>Borrar</th>
                        </tr>
                    </thead>
                    <tbody>';
        foreach ($query1 as $currentUser) {
            $idpractica=$currentUser['id_Practica'];  
            $idHerrP =$currentUser['FK_Herramientas_P']; 
            echo '
                <tr>
                    <td>' . $currentUser['Fecha'] . '</td>
                    <td>' . $currentUser['Nombre_Practica'] . '</td>
                    <td>' . $currentUser['Descripcion'] . '</td>';
                    $query2 = $this->connect()->prepare('SELECT nombre FROM herramientas WHERE id_Herramientas='.$idHerrP.''); //preparamos consulta sql
                    $query2->execute(); //Ejecutamos la consulta buscando en la base de datos siempre comparando con el usuario que ingresó en el login
                    foreach ($query2 as $currentUser2) {        
                    echo'<td>' . $currentUser2['nombre'] . '</td>';
                    }
                    echo'
                    <td> <a href="Database/SQL/DownloadRecPracticas.php?idPractica='.$idpractica.'"target="_blank"><img src="assets/img/pdf.png"></a> </td>
                    <td>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalP'.$idpractica.'"><i class="fa fa-pencil-square-o" aria-hidden="true" ></i></button>
                        


                    <div id="modalP'.$idpractica.'" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">' . $currentUser['Nombre_Practica'] . ' </h4>
                        </div>
                        <form class="form-horizontal" method="post" action="Database/SQL/UpdatePracticas.php" enctype="multipart/form-data" >

                        <div class="modal-body">
                        <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Herramienta" class="control-label col-sm-2">Herramienta:</label>
                                <div class="col-sm-12" style="display: block;">';
                                    
                                $query3 = $this->connect()->prepare('SELECT * FROM herramientas');
                                $query3->execute();
                                echo '
                                <select class="form-select"  name="herramientaUP" id="herramientaUP">';
                                foreach ($query3 as $currentUser3) {
                                    $id= $currentUser3['id_Herramientas']; 
                                    echo '
                                    <option name="herramientaUP" id="herramientaUP" value="'.$id.'">'.$currentUser3['nombre'].'</option>
                                     ';   
                                }
                                echo '</select>
                                <p class="help-block">Selecione ka herramienta...</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Titulo" class="control-label col-sm-2">Nombre:</label>
                                <div class="col-sm-12" style="display: block;">
                                <input type="hidden" id="idpracUPDATE" name="idpracUPDATE" value="'.$idpractica.'">    
                                    <input type="text" class="form-control" name="nombreUP" id="nombreUP" value="' . $currentUser['Nombre_Practica'] . '">
                                    <p class="help-block">Nombre de la práctica...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="descripcion">Descripción</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="descripcionUP" name="descripcionUP" rows="7" >' . $currentUser['Descripcion'] . '</textarea>
                                    <p class="help-block">Descripción de la práctica ....
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                        <div class="modal-footer">
                      
                        <button type="button" id="Limpiar" name="Limpiar" class="btn btn-secondary" aria-label="limpiar" data-dismiss="modal">Salir</button>
                        <button type="subir"  name="subir" class="btn btn-primary" aria-label="subir">enviar</button>
                        </div>
                        </div>
                        </form>
                    </div>
                    </div>
                    </td>
                    <td>
                        <form method="post" action="Database/SQL/BorrarPractica.php" >
                            <input type="hidden" id="idprac" name="idprac" value="'.$idpractica.'">       
                            <button type="submit" name="dlteBtn" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </form>
                    </td>
                    
                </tr> 
                ';
        }
        echo '</tbody>
            </table>';
    }
   

    public function SelectHerramientas()
    {
      
        $query = $this->connect()->prepare('SELECT * FROM herramientas');
        $query->execute();
        echo '
        <select class="form-select"  name="herramienta" id="herramienta">';
        foreach ($query as $currentUser) {
            $id= $currentUser['id_Herramientas']; 
            echo '
            <option name="herramienta" id="herramienta" value="'.$id.'">'.$currentUser['nombre'].'</option>
             ';   
        }
        echo '</select>
        '; 

    }

   
       



}
