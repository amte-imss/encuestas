<?php
$this->config->load('general');
$tipo_msg = $this->config->item('alert_msg');
//pr($grupos_ids_text);
if ($this->session->flashdata('success') == TRUE) {
    echo html_message($this->session->flashdata('success'), $tipo_msg['SUCCESS']['class']);
}
?>
     <div class="list-group-item">
     <div style="text-align:justify">
     
     
    <?php 
   
       echo "<b>Instrucciones</b> <br><br>";
       echo  $instrumento[0]['guia_descripcion_encuesta'];
         

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



<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="panel panel-amarillo">
            <div class="panel-body">

<?php
//pr($preguntas);

$check_ok = '<span class="glyphicon glyphicon-ok" aria-hidden="true" style="color:green;"> </span>';
$check_no = '<span class="glyphicon glyphicon-remove" aria-hidden="true" style="color:red;"> </span>';

if (isset($preguntas) && !empty($preguntas) && isset($instrumento) && !empty($instrumento)) {
    //pr($instrumento);
    /*
      preguntas_cve,
      seccion_cve,
      encuesta_cve,
      tipo_pregunta_cve,
      pregunta,
      -- pregunta_abierta_cerrada,
      obligada,
      orden,
      is_bono,
      -- has_children,
      -- obligatoria,
      val_ref,
      pregunta_padre,
      -- encuesta_padre,
      reactivos_cve,
      -- preguntas_cve,
      -- ponderacion,
      texto,
      -- orden,
      encuesta_cve
     */
    ?>
                    <div id="detalle_instrumento">                
                        <div class="table-responsive">
                            <table class="table table-bordered">

                             <!--   <tr>
                                    <td>
                                        <label>Nombre de la implementación:</label>
                                    </td>-->
                                    <!--<td colspan="5">
                                        <h3><?php //echo $instrumento[0]['descripcion_encuestas'];  ?></h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>Rol a evaluar:</label>
                                    </td>
                                    <td>
                                        <h4><?php //echo $instrumento[0]['name'];  ?></h4>
                                    </td>
                                    <td>
                                        <label>Aplica para bonos:</label>
                                    </td>
                                    <td>
                                        <h4><a><?php //echo ($instrumento[0]['is_bono'] != 0) ? $check_ok : $check_no;   ?></a></h4>
                                    </td>
                                    <td>
                                        <label>Curso tutorizado:</label>
                                    </td>
                                    <td>
                                        <h4><a><?php //echo ($instrumento[0]['tutorizado'] != 0) ? $check_ok : $check_no;  ?></a></h4>
                                    </td>
                                </tr>-->
                                 <!--   <td colspan="4"><h3><?php echo $curso['data'][0]['cur_nom_completo']; ?></h3></td>
                                </tr>-->
                                <tr>
                                    <td>
                                        <label>Nombre del instrumento:</label>
                                    </td>
                                    <td colspan="4"><h3><?php echo $instrumento[0]['descripcion_encuestas']; ?></h3></td>
                                </tr>
                                <tr>
                                    <td><label>Rol a evaluar:</label></td>
                                    <td><label>Rol evaluador:</label></td>
                                    <td><label>Aplica para bonos:</label></td>
                                    <td><label>Curso tutorizado:</label></td>
                                    <td><label>Activo:</label></td>
                                </tr>
                                <tr>
                                    <td><h4><?php echo $instrumento[0]['name']; ?></h4></td>
                                    <td><h4><?php echo $instrumento[0]['evaluador']; ?></h4></td>
                                    <td><h4><a><?php echo ($instrumento[0]['is_bono'] != 0) ? $check_ok : $check_no; ?></a></h4></td>
                                    <td><h4><a><?php echo ($instrumento[0]['tutorizado'] != 0) ? $check_ok : $check_no; ?></a></h4></td>
                                    <td><h4><a><?php echo ($instrumento[0]['status'] != 0) ? $check_ok : $check_no; ?></a></h4></td>
                                </tr>
                            </table>
                        </div>
                    </div>
    <?php
    if (isset($mensaje)) {
        echo "<font color='red'>" . $mensaje . "</font>";
    }
    //echo "<h2>Instrumento: ".$instrumento[0]['descripcion_encuestas']."</h2><br>";
    //echo "<h3>Rol a evaluar: ".$instrumento[0]['name']."</h3><br><br>";


    $seccion = 0;
    $pregunta = 0;
    $no_pregunta = 1;

    echo form_open('encuestausuario/guardar_encuesta_usuario', array('id' => 'form_encuesta_usuario'));
    //pr($preguntas);               

    foreach ($preguntas as $key => $val) {

        if ($seccion !== $val['seccion_cve']) {
            if ($val['descripcion'] != 'NA') {
                echo "<br><h4># " . $val['descripcion'] . "</h4><br>";
            }
        }

        if ($pregunta !== $val['preguntas_cve']) {

            echo "<br><b><h5> " . $val['orden'] . " - ¿" . $val['pregunta'] . "?</h5></b><br>";
            echo form_error_format('p_r[' . $val['preguntas_cve'] . ']');
        }

        echo '<label><input type="radio" name="p_r[' . $val['preguntas_cve'] . ']"   
                    value="' . $val['reactivos_cve'] . '"' . set_radio('p_r[' . $val['preguntas_cve'] . ']', '' . $val['reactivos_cve'] . '') . '; >' . $val['texto'] . '</label><br>';




        $seccion = $val['seccion_cve'];
        $pregunta = $val['preguntas_cve'];
    }
    if (isset($encuesta_cve) || isset($user_id_evaluado) || isset($user_id_evaluador) || isset($curso_cve) || isset($grupo_cve)) {
        echo '<input type="hidden" id="idencuesta" name="idencuesta" value="' . $encuesta_cve . '">';
        echo '<input type="hidden" id="iduevaluado" name="iduevaluado" value="' . $evaluado_user_cve . '">';
        echo '<input type="hidden" id="iduevaluador" name="iduevaluador" value="' . $evaluador_user_cve . '">';
        echo '<input type="hidden" id="idcurso" name="idcurso" value="' . $curso_cve . '">';
        echo '<input type="hidden" id="is_bono" name="is_bono" value="' . $instrumento[0]['is_bono'] . '">';
        echo '<input type="hidden" id="idgrupo" name="idgrupo" value="' . $grupo_cve . '">';
        if (isset($grupos_ids_text)) {
            echo '<input type="hidden" id="grupos_ids_text" name="grupos_ids_text" value="' . $grupos_ids_text . '">';
        }
        if (isset($bloque)) {
            echo '<input type="hidden" id="bloque" name="bloque" value="' . $bloque . '">';
        }
        echo "<br>";
    }
    if (isset($boton) && !empty($boton)) {

        echo $this->form_complete->create_element(array('id' => 'btn_submit', 'name' => 'btn_submit',
            'type' => 'submit', 'value' => 'Terminar encuesta'
        ));
    }



    echo form_close();
    if (isset($mensaje)) {
        echo "<font color='red'>" . $mensaje . "</font>";
    }
} else {
    ?>
                    <br><br>
                    <div class="row">
                        <div class="jumbotron"><div class="container"> <p class="text_center">No se encontraron datos registrados en esta busqueda</p> </div></div>
                    </div>

                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>