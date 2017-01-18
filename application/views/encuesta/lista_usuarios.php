<?php
if (isset($datos_user_aeva) && !empty($datos_user_aeva)) {
//    pr($datos_user_aeva);
    ?>
    <div class="list-group-item">
        <div style="text-align:right"><?php echo $nombreevaluador ?>
        </div>
        <?php
        if (isset($error) AND ! is_null($error) AND ! empty($error)) {
            echo '<div class="row">
                                <div class="col-md-1 col-sm-1 col-xs-1"></div>
                                <div class="col-md-10 col-sm-10 col-xs-10 alert alert-danger">
                                    ' . $error . '
                                </div>
                                <div class="col-md-1 col-sm-1 col-xs-1"></div>
                            </div>';
        }
        ?>
        
    </div>
     <div class="list-group-item">
     <div style="text-align:justify">
     
    <?php 
    $datos_curso['data'][0]['tutorizado']=1;
    if (isset($datos_curso['data'][0]['tutorizado']) && $datos_curso['data'][0]['tutorizado'] == 1) 
    { 
       echo "<b>Instrucciones</b> <br><br>
       A fin de conocer su punto de vista sobre aspectos importantes relativos al presente curso, 
       enseguida se presentan algunas aseveraciones para conocer su satisfacción en general. 
       Sus respuestas son de gran relevancia para mejorar los cursos a distancia implementados por la 
       División de Innovación Educativa por lo que le solicitamos responda con la mayor veracidad posible.
       <br><br> 
       Deberá seleccionar “SI” o “No” según se haya presentado o no la situación descrita. En los casos en que no le sea posible dar una opinión, deberá seleccionar No aplica:
       ";
    }
    else
    {
       echo "<b>Instrucciones</b> <br><br>
       Enseguida se presenta una serie de enunciados, todos se refieren a las actividades que desempeña un Coordinador de curso no Tutorizado.
       <br><br>
       Coloque una X en el espacio que corresponda, ya sea que el Coordinador de curso llevó a cabo o no la actividad que se describe.
       <br><br>
       En los casos en que no es posible evaluar la actividad del Coordinador de curso, por no haberse presentado la situación descrita, deberá colocar una X en el espacio que corresponde  a No aplica";
    }    
    

    ?>
    
    </div>

     </div>   
     <div class="col-md-4"></div>
         <div class="col-md-4">
           <table id="rayitas" class="text-center">
             <tr class="border-bottom">
               <td class="border-lr"></td>
               <td></td>
               <td class="border-lr">X</td>
             </tr>
             <tr>
               <td class="no-border">Si</td>
               <td class="no-border">No</td>
               <td class="no-border">No aplica</td>
             </tr>
           </table>
         </div>
         <div class="col-md-4"></div>


    <div class="panel-heading">  
        
        <table>
            <tr>
                <th>
                    NOMBRE DE IMPLEMENTACIÓN:
                </th>
            </tr>
            <tr>                
                <td>
                    <h3> <?php echo $datos_curso['data'][0]['cur_nom_completo']; ?>
                        (<?php echo $datos_curso['data'][0]['cur_clave']; ?>)</h3> 
                </td>
            </tr>
        </table>
    </div>
    </div>



    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>Regla</th>
                    <th>Rol evaluador</th>

                    <th>Rol a evaluar</th>
                    <th>Nombre docente a evaluar</th>
                    <th>Grupo</th>
                   <!--<th>Encuesta</th>-->
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //pr($datos_user_aeva);
                foreach ($datos_user_aeva as $val) {
                    //pr($val);
                    foreach ($val as $keyl => $valuel) {

//                        pr($valuel);# code...
                        //}
                        $is_bloques_grupos = 0;
                        if (isset($valuel)) {
                            if (isset($valuel['ngpo']) && $valuel['ngpo'] != '0') {
                                //$grupo = $val[0]['ngpo'];
                                $grupo = (!empty($valuel['ngpo'])) ? implode(str_getcsv(trim($valuel['ngpo'], '{}')), ', ') : '';
                                $is_bloques_grupos = 1;
                            } else {
                                $grupo = '--';
                            }

                            echo '<tr>
                        <td >' . $valuel['regla'] . '</td >
                        <td >' . $valuel['evaluador'] . '</td >
                        <td >' . $valuel['role'] . '</td > 
                         <td >' . $valuel['firstname'] . ' ' . $valuel['lastname'] . '</td>
                        <td > ' . $grupo . '</td >';
                            //<td >' . $val[0]['regla'] . '</td>

                            echo '<td>';
                            echo form_open('encuestausuario/instrumento_asignado', array('id' => 'form_curso'));
                            ?>
                        <input type="hidden" id="idencuesta" name="idencuesta" value="<?php echo $valuel['regla'] ?>">
                        <?php if ($is_bloques_grupos) { ?>
                            <input type = "hidden" id = "grupos_ids_text" name = "grupos_ids_text" value = "<?php echo $valuel['grupos_ids_text'] ?>">
                        <?php } ?>
                        <?php if (isset($valuel['bloque'])) { ?>
                            <input type = "hidden" id = "bloque" name = "bloque" value = "<?php echo $valuel['bloque'] ?>">
                        <?php } ?>

                        <input type="hidden" id="iduevaluado" name="iduevaluado" value="<?php echo $valuel['userid'] ?>">
                        <input type="hidden" id="idcurso" name="idcurso" value="<?php echo $valuel['cursoid'] ?>">
                        <input type="hidden" id="idgrupo" name="idgrupo" value="<?php
                        if (isset($valuel['gpoid']) && $valuel['gpoid'] > 0) {
                            echo $valuel['gpoid'];
                        } else {
                            echo '0';
                        }
                        ?>">
                        <input type="hidden" id="iduevaluador" name="iduevaluador" value="<?php echo $iduevaluador ?>">


                        <?php
                        if (isset($valuel['realizado']) || !empty($valuel['realizado'])) {
                            echo "Realizada";
                        } else {
                            ?>
                            <input type="submit"  class="btn btn-info btn-block" value="Evaluar usuario">
                            <?php
                        }
                        ?>

                        <!--
                          <a href="'.site_url('encuestausuario/instrumento_asignado/'.$val[0]['regla']).'" class="btn btn-info btn-block">
                              <span class="glyphicon glyphicon-search"></span>
                          </a>-->
                        <?php
                        echo form_close();

                        echo '</td>
                        ';

                        echo '</tr>';
                        # code...
                    }
                }
            }
            ?>
            </tbody>
        </table>

    </div>
<?php } else if (isset($coment_general)) { ?>
    <div class="row">
        <div class="jumbotron"><div class="container"><p class="text_center"><?php echo $coment_general; ?></p> </div></div>
    </div>
<?php } else { ?>
    <br><br>
    <div class="row">
        <div class="jumbotron"><div class="container"> <p class="text_center">No se encontraron datos registrados con esta busqueda</p> </div></div>
    </div>

<?php } ?>
