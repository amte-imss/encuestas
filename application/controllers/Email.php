<?php

class Email extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Tarjeton_model", "m_tarjeton");
        $this->load->model("bono_perfil_empleado_model", "m_empleado");
        $this->load->library('form_complete');
        $this->load->library('Form_validation');
        $this->config->load('general');
//        $estados_bono = $this->config->item('estados_bono');
        $this->load->library('enum_estados_empleado');
    }

    public function index() {
        $usuario = (object) [
                    'usr_nombre' => 'Luis',
                    'usr_paterno' => 'Aguilera',
                    'usr_materno' => 'Soto',
                    'usr_matricula' => 'ERTYSADO',
        ];

        $main_contet = $this->load->view('template/email/enviar_confirmacion.tpl', $usuario, true);
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();
    }

    public function get_ajax_mail($id_em = null, $mail_ = null) {
//        pr('--id_> ' . $id_em);
//        pr('--mail_> ' . $mail_);
//        pr($this->input->post());

        if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
            $estados_bono = $this->config->item('estados_bono');
            if ($this->input->post()) {
                $this->config->load('form_validation');
                $validations = $this->config->item('form_correo_e'); //Obtener validaciones almacenadas en archivo para mail
                $this->form_validation->set_rules($validations);

                $data['id_empleado'] = $id_em; //Se cacha el id del usuario para que no se perda
                $data['mail'] = trim($this->input->post('mail_', true)); //se cacha el correo que viene por el metodo post
                //Si los datos de entrada son validos
                if ($this->form_validation->run() == true) {
                    $arr = $this->m_empleado->getContactoEmpleado($id_em);
                    $datos['em_cve'] = $arr[0]['em_cve'];
                    $datos['contacto_cve'] = $arr[0]['id_contacto'];
                    $datos['emp_nom_completo'] = $arr[0]['emp_nom_completo'];
                    $datos['contacto_val'] = trim($data['mail']);
                    $datos['matricula'] = $arr[0]['matricula'];
//                    pr($datos);
                    $res = $this->m_empleado->save_mail_bonos($datos); //Guarda dirección de email

                    $registros = $this->m_empleado->get_registro_bono($id_em); //se obtienen los registros del bono del empleado
//                    pr($registros);
                    if (isset($registros)) {
                        $datos_reghistorial['emp_can_cve'] = $registros['result'][0]['emp_can_cve']; //Agrega el registro actual de empleado candidato
                        $datos_reghistorial['est_bono_cve'] = $registros['result'][0]['cve_estado_bono']; //Le asigna estado del bono solicitud de tarjetón
                        $now = date('Y-m-d H:i:s'); //Fecha y hora actual
                        $datos_reghistorial['reg_fecha'] = $now; //Agrega la fecha actual.
                        $datos_reghistorial['reg_promedio'] = $registros['result'][0]['reg_promedio'];
                        $datos_reghistorial['tar_cve'] = $registros['result'][0]['tar_cve']; //Agrega el registro actual de empleado candidato
                        $datos_reghistorial['accion_tarjeton'] = 0; //Agrega el registro actual de empleado candidato
//                        $datos_reghistorial['accion_tarjeton'] = $registros['result'][0]['accion_tarjeton']; //Agrega el registro actual de empleado candidato
                    }

                    $this->load->model("bono_perfil_empleado_model", "m_empleado");
//                    $id_reg_historial = $this->m_empleado->save_bitacora_registro_bono($datos_reghistorial);
//                    $datos['reg_bono_cve'] = $id_reg_historial['reg_bono_cve']; //Asigna el id de registro de bono
                    $datos['tar_quincena'] = 0;

//                    exist_and_not_null($valor);
                    //Si todo lo anterior es valido
                    $plantilla = $this->load->view('template/email/enviar_solicitud_tarjeton.bon.php', $datos, true);
                    $sentMail = $this->enviar_solicitud_correo($datos + array('plantilla' => $plantilla)); //Enviar correo
//                    $sentMail = $this->sendMailGmail($datos + array('plantilla' => $plantilla)); //Enviar correo
//                    pr($sentMail);
//                    $sentMail = array('result' => 1);
                    if ($sentMail["result"] == 0) {
//                        pr('algo salio mallll');
                        //$this->session->set_flashdata('error', $sentMail['error']);
                        $error['error'] = 'No se pudo enviar el correo electrónico. <br> Por favor inténtelo más tarde o verifique su conexión a internet';
                        $datos_reghistorial['reg_msg'] = 'La solicitud del tarjetón no pudo ser enviada '; //Mensaje de solicitud de tarjetón no enviado
                        $respuesta = $this->load->view('mensajes_error/mensajes_error_tarjeton.php', $error, true);
                        $res['result'] = 1; //Indica que hubo un error al mandar el mail
                    } else {
                        $error['success'] = 'La solicitud de tarjetón fue enviada correctamente. <br>Por favor espere un momento';
                        $datos_reghistorial['reg_msg'] = 'La solicitud de tarjetón fue enviada correctamente '; //Mensaje de solicitud de tarjetón
                        $respuesta = $this->load->view('mensajes_error/mensajes_error_tarjeton.php', $error, true);
                        $res['result'] = 1; //Indica que hubo un error al mandar el mail
                    }
                    //Muestra mensaje y recarga página
                    if ($res['result'] == 1) {
                        //si el las operaciones se efectuaron exitosamente
                        $this->m_empleado->save_bitacora_registro_bono($datos_reghistorial);
                        echo $respuesta;
                        echo '<script type="text/javascript">
                            window.setTimeout(function(){ document.location.reload(true); }, 3500);
                        </script>';
                    }
//                    redirect('/bono_perfil_empleado/empleado/', 'refresh');
                } else {
//                    pr('HOlAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
                    //Si todo lo anterior es valido
                    $main_contet = $this->load->view('template/email/enviarmail', $data, true);
                    echo $main_contet;
                }
            } else {
                //Carga datos de gmail
                if (is_null($mail_)) {
                    $arr = $this->m_empleado->getContactoEmpleado($id_em);
//                    pr($arr);
                    $data['id_empleado'] = $id_em;
                    if (exist_and_not_null($arr)) {
                        $data['nombre'] = $arr[0]['emp_nom_completo'];
                        $mail_bono = $arr[0]['mail_bono'];
                        $data['mail'] = (is_null($mail_bono)) ? $arr[0]['mail_em'] : $mail_bono; //Si no exixte un correo registrado en la tabla de contactos, toma el de la tabla de empleados
                        $data['matricula'] = $arr[0]['matricula'];
                    } else {
                        $data['nombre'] = '';
                        $mail_bono = '';
                        $data['mail'] = '';
                        $data['matricula'] = '';
                    }
                }
                $main_contet = $this->load->view('template/email/enviarmail', $data, true);
                echo $main_contet;
            }
        }
    }

    private function enviar_solicitud_correo($data) {
        //$this->load->config('email');
        $this->load->library('My_phpmailer');

        $mail = $this->my_phpmailer->phpmailerclass(); //Se cargan datos por default definidos en config/email

        $resultado = array('result' => 1, 'error' => null);

        // $mail->IsSMTP(); // establecemos que utilizaremos SMTP
        // $mail->Host = "172.16.23.18";
        //$mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
        /* $mail->IsSMTP(); // telling the class to use SMTP
          $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
          // 1 = errors and messages
          // 2 = messages only
          $mail->SMTPAuth   = true;                  // enable SMTP authentication
          $mail->Host       = "smtp.gmail.com"; // sets the SMTP server
          $mail->Port       = 587;                    // set the SMTP port for the GMAIL server
          $mail->Username   = "sied.ad.imss@gmail.com"; // SMTP account username
          $mail->Password   = "s13d.4d.1mss"; */
//        pr($datos);
//        $mail->addAddress($datos['mail'], utf8_decode($datos['nombre']));
//        pr($data);
//        pr('->' . $data['contacto_val'] . '<-');
        $mail->addAddress($data['contacto_val'], utf8_decode($data['emp_nom_completo']));
        $mail->Subject = utf8_decode('Petición de tarjetón :: Bonos IMSS');
        $mail->msgHTML(utf8_decode($data['plantilla']));
        //$mail->AltBody = 'This is a plain-text message body';
        // $resultado['result'] = true;
        if (!$mail->send()) { //send the message, check for errors
            $resultado['result'] = 0;
            $resultado['error'] = $mail->ErrorInfo;
        }
        //pr($mail);
        //pr($resultado);
        return $resultado;
    }

    public function sendMailGmail($datos) {
        //cargamos la libreria email de ci
        $resultado = array('result' => false, 'error' => null);
        $this->load->library("email");

        //configuracion para gmail
        $configGmail = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_port' => 465,
//            'smtp_port' => 534,
            'smtp_user' => 'cenitluis.pumas@gmail.com',
            'smtp_pass' => 'el#:(vlaluna2vces',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        );
        //Configuracion hotmail.com
        $configHotmail = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.live.com',
            'smtp_port' => 465,
//            'smtp_port' => 534,
            'smtp_user' => 'cenitluis_pumas@hotmail.com',
            'smtp_pass' => '#%304172670',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        );

        //cargamos la configuración para enviar con gmail
        $this->email->initialize($configGmail);

//      nombre o correo que envía
        $this->email->from($configGmail['smtp_user']);
        $this->email->to($datos['contacto_val']);
        $this->email->subject('Bienvenido/a a uno-de-piera.com');
        $this->email->message($datos['plantilla']);
        $this->email->send();
        //con esto podemos ver el resultado
        var_dump($this->email->print_debugger());
    }

    public function sendMailYahoo() {
        //cargamos la libreria email de ci
        $this->load->library("email");

        //configuracion para yahoo
        $configYahoo = array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.mail.yahoo.com',
            'smtp_port' => 587,
            'smtp_crypto' => 'tls',
            'smtp_user' => 'emaildeyahoo',
            'smtp_pass' => 'password',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        );

        //cargamos la configuración para enviar con yahoo
        $this->email->initialize($configYahoo);

        $this->email->from('correo que envia los emails'); //correo de yahoo o no funcionará
        $this->email->to("correo que recibe el email");
        $this->email->subject('Bienvenido/a a uno-de-piera.com');
        $this->email->message('<h2>Email enviado con codeigniter haciendo uso del smtp de yahoo</h2><hr><br> Bienvenido al blog');
        $this->email->send();
        //con esto podemos ver el resultado
        var_dump($this->email->print_debugger());
    }

}
