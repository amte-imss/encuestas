<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="panel panel-amarillo">
            <div class="panel-body">
<?php 
$this->config->load('general');
$tipo_msg = $this->config->item('alert_msg');

if (isset($mensaje))
{ 
	echo html_message($mensaje, $tipo_msg['WARNING']['class']);
	echo form_open('encuestausuario/lista_encuesta_usuario?iduser='.$idusuario.'&idcurso='.$idcurso);                
?> 
	<input type="submit" name="submit" value="Terminar" class="btn btn-success">
					

    <?php 	echo form_close();
	
     }
?>


</div>
</div>
</div>
</div>