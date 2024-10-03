<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contingencies_summary extends MY_Controller {

	private $id_modulo_cliente;
	private $id_submodulo_cliente;

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");

		$this->id_modulo_cliente = 12;
		$this->id_submodulo_cliente = 24;

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);

		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
    }

    function index(){

        $id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

        $view_data = array();
        $view_data["id_cliente"] = $id_cliente;
        $view_data["id_proyecto"] = $id_proyecto;

        $cliente = $this->Clients_model->get_one($id_cliente);
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["project_info"] = $proyecto;
		$view_data["nombre_proyecto"] = $proyecto->title;

		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

        // SECCIÓN TIPOS DE EVENTO
        $array_tipo_evento = array(
            array("nombre" => "near_incident", "color" => "#F7BD00"),
            array("nombre" => "minor_incident", "color" => "#F75431"),
            array("nombre" => "significant_incident", "color" => "#C10037"),
            array("nombre" => "environmental_damage", "color" => "#8B0C3C"),
            array("nombre" => "environmental_emergency", "color" => "#541743"),
        );
        $view_data['array_tipo_evento'] = $array_tipo_evento;

        $array_cant_tipo_evento = array();
        foreach($array_tipo_evento as $tipo_evento){
            $array_cant_tipo_evento[$tipo_evento['nombre']]['cant'] = 0;
            $array_cant_tipo_evento[$tipo_evento['nombre']]['color'] = $tipo_evento['color'];
        }

        $result_count_event_type = $this->Contingencies_event_record_model->count_event_types(array('id_proyecto' => $id_proyecto))->result();
        
        foreach($result_count_event_type as $result){
            $array_cant_tipo_evento[$result->tipo_evento]['cant'] = $result->cant; 
        }
        $view_data['array_cant_tipo_evento'] = $array_cant_tipo_evento;
        //FIN SECCIÓN TIPOS DE EVENTO

        // SECCIÓN GRÁFICO EVENTOS POR RESPONSABLE
        $array_gerencia = array(
            lang('agricultural'),
            lang('human_management'),
            lang('administration_and_finance'),
            lang('general'),
            lang('packing_plant_and_projects'),
            lang('commercial'),
            lang('cerro_prieto_irrigator')
        );
        
        $view_data['array_gerencia'] = $array_gerencia;

        $cant_eventos_por_gerencia = array();
        foreach($array_gerencia as $gerencia){
            foreach($array_tipo_evento as $tipo_evento){
                $cant_eventos_por_gerencia[$gerencia][$tipo_evento['nombre']] = 0;
            }
        }

        $contingencias = $this->Contingencies_event_record_model->get_details(array('id_proyecto' => $id_proyecto))->result();


        foreach($contingencias as $contingencia){
            $cant_eventos_por_gerencia[lang($contingencia->gerencia)][$contingencia->tipo_evento] += 1;
        }

        $array_data_eventos_por_gerencia = array();
			foreach($array_tipo_evento as $tipo_evento){
				$data_por_tipo_evento = array('name' => lang($tipo_evento['nombre']), 'color' => $tipo_evento['color'], 'data' => array());
				foreach($array_gerencia as $gerencia){
					$data_por_tipo_evento['data'][] = $cant_eventos_por_gerencia[$gerencia][$tipo_evento['nombre']];
				}
				$array_data_eventos_por_gerencia[] = $data_por_tipo_evento;
			}
        // echo '<pre>'; var_dump($array_data_eventos_por_gerencia);exit;

        $view_data['grafico_eventos_por_responsable'] = $array_data_eventos_por_gerencia;
        // FIN SECCIÓN GRÁFICO EVENTOS POR RESPONSABLE

        // SECCIÓN GRÁFICO EVENTOS POR TIPO DE AFECTACIÓN
        $array_tipo_afectacion = array(
            lang("health"),
            lang("water"),
            lang("ground"),
            lang("air"),
            lang("biodiversity"),
            lang("social"),
            lang("heritage"),
            lang("environmental_commitment")
        );
        
        $view_data['array_tipo_afectacion'] = $array_tipo_afectacion;

        $cant_eventos_por_gerencia = array();
        foreach($array_tipo_afectacion as $tipo_afectacion){
            foreach($array_tipo_evento as $tipo_evento){
                $cant_eventos_por_tipo_afectacion[$tipo_afectacion][$tipo_evento['nombre']] = 0;
            }
        }

        $contingencias = $this->Contingencies_event_record_model->get_details(array('id_proyecto' => $id_proyecto))->result();


        foreach($contingencias as $contingencia){
            $cant_eventos_por_tipo_afectacion[lang($contingencia->tipo_afectacion)][$contingencia->tipo_evento] += 1;
        }

        $array_data_eventos_por_tipo_afectacion = array();
			foreach($array_tipo_evento as $tipo_evento){
				$data_por_tipo_evento = array('name' => lang($tipo_evento['nombre']), 'color' => $tipo_evento['color'], 'data' => array());
				foreach($array_tipo_afectacion as $tipo_afectacion){
					$data_por_tipo_evento['data'][] = $cant_eventos_por_tipo_afectacion[$tipo_afectacion][$tipo_evento['nombre']];
				}
				$array_data_eventos_por_tipo_afectacion[] = $data_por_tipo_evento;
			}
        // echo '<pre>'; var_dump($array_data_eventos_por_tipo_afectacion);exit;

        $view_data['grafico_eventos_por_tipo_afectacion'] = $array_data_eventos_por_tipo_afectacion;
        // FIN SECCIÓN GRÁFICO EVENTOS POR TIPO DE AFECTACIÓN
        
        $view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));

        // PARA NOMBRE DE ARCHIVOS EXPORTABLES
		$view_data['sigla_cliente'] = $cliente->sigla;
		$view_data['sigla_proyecto'] = $proyecto->sigla;

        $this->template->rander("contingencies_summary/index", $view_data);

    }

    function get_pdf(){

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
        $start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");

		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		
        /* $id_compromiso_rca = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $info_proyecto->id, 'deleted' => 0))->id;
		$id_compromiso_reportables = $this->Compromises_reportables_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id; */

		$view_data["info_cliente"] = $info_cliente;
		$view_data["info_proyecto"] = $info_proyecto;
		$view_data["Contingencies_summary_controller"] = $this;

		$imagenes_graficos = $this->input->post("imagenes_graficos");

        $view_data["grafico_totales_tipo_evento"] = $imagenes_graficos["grafico_totales_tipo_evento"];
        $view_data["grafico_eventos_por_responsable"] = $imagenes_graficos["grafico_eventos_por_responsable"];
        $view_data["grafico_eventos_por_tipo_afectacion"] = $imagenes_graficos["grafico_eventos_por_tipo_afectacion"];

        $view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

        // SECCIÓN TIPOS DE EVENTO
        $array_tipo_evento = array(
            array("nombre" => "near_incident", "color" => "#F7BD00"),
            array("nombre" => "minor_incident", "color" => "#F75431"),
            array("nombre" => "significant_incident", "color" => "#C10037"),
            array("nombre" => "environmental_damage", "color" => "#8B0C3C"),
            array("nombre" => "environmental_emergency", "color" => "#541743"),
        );
        $view_data['array_tipo_evento'] = $array_tipo_evento;

        $array_cant_tipo_evento = array();
        foreach($array_tipo_evento as $tipo_evento){
            $array_cant_tipo_evento[$tipo_evento['nombre']]['cant'] = 0;
            $array_cant_tipo_evento[$tipo_evento['nombre']]['color'] = $tipo_evento['color'];
        }

        $result_count_event_type = $this->Contingencies_event_record_model->count_event_types(array(
            'id_proyecto' => $id_proyecto,
            'start_date' => $start_date,
            'end_date' => $end_date
        ))->result();
        
        foreach($result_count_event_type as $result){
            $array_cant_tipo_evento[$result->tipo_evento]['cant'] = $result->cant; 
        }
        $view_data['array_cant_tipo_evento'] = $array_cant_tipo_evento;
        //FIN SECCIÓN TIPOS DE EVENTO

        
		// create new PDF document
        $this->load->library('Pdf');

		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("compromises")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("compromises")."_".date('Y-m-d'));
        $this->pdf->SetKeywords('TCPDF, PDF');

		//$this->pdf->SetPrintHeader(false);
		//$this->pdf->SetPrintFooter(false);
		// set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', '', array(0, 64, 255), array(0, 64, 128));
        $this->pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
		// set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE,PDF_MARGIN_BOTTOM);
		//relación utilizada para ajustar la conversión de los píxeles
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		// ---------------------------------------------------------
		// set default font subsetting mode
        $this->pdf->setFontSubsetting(true);
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		//$this->SetFont('freemono', '', 14, '', true);
		$fontawesome = TCPDF_FONTS::addTTFfont('assets/js/font-awesome/fonts/fontawesome-webfont.ttf', 'TrueTypeUnicode', '', 96);

		$this->pdf->AddPage();

		$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
		$this->pdf->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$view_data["fontawesome"] = $fontawesome;
		$view_data["pdf"] = $this->pdf;
		$html = $this->load->view('contingencies_summary/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');

		$pdf_file_name = $info_cliente->sigla."_".$info_proyecto->sigla."_".lang("contingencies")."_".date('Y-m-d').".pdf";

		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;

    }

    function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}

    function get_contingencies_summary_details(){

        $id_cliente = $this->input->post("id_cliente") ? $this->input->post("id_cliente") : $this->login_user->client_id;
		$id_proyecto = $this->input->post("id_proyecto") ? $this->input->post("id_proyecto") : $this->session->project_context;
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");

        $view_data = array();
        $view_data["id_cliente"] = $id_cliente;
        $view_data["id_proyecto"] = $id_proyecto;
        $view_data["start_date"] = $start_date;
		$view_data["end_date"] = $end_date;

        $cliente = $this->Clients_model->get_one($id_cliente);
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["project_info"] = $proyecto;
		$view_data["nombre_proyecto"] = $proyecto->title;

		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

        // SECCIÓN TIPOS DE EVENTO
        $array_tipo_evento = array(
            array("nombre" => "near_incident", "color" => "#F7BD00"),
            array("nombre" => "minor_incident", "color" => "#F75431"),
            array("nombre" => "significant_incident", "color" => "#C10037"),
            array("nombre" => "environmental_damage", "color" => "#8B0C3C"),
            array("nombre" => "environmental_emergency", "color" => "#541743"),
        );
        $view_data['array_tipo_evento'] = $array_tipo_evento;

        $array_cant_tipo_evento = array();
        foreach($array_tipo_evento as $tipo_evento){
            $array_cant_tipo_evento[$tipo_evento['nombre']]['cant'] = 0;
            $array_cant_tipo_evento[$tipo_evento['nombre']]['color'] = $tipo_evento['color'];
        }

        $result_count_event_type = $this->Contingencies_event_record_model->count_event_types(array(
            'id_proyecto' => $id_proyecto,
            'start_date' => $start_date,
            'end_date' => $end_date
        ))->result();
        
        foreach($result_count_event_type as $result){
            $array_cant_tipo_evento[$result->tipo_evento]['cant'] = $result->cant; 
        }
        $view_data['array_cant_tipo_evento'] = $array_cant_tipo_evento;
        //FIN SECCIÓN TIPOS DE EVENTO

        // SECCIÓN GRÁFICO EVENTOS POR RESPONSABLE
        $array_gerencia = array(
            lang('agricultural'),
            lang('human_management'),
            lang('administration_and_finance'),
            lang('general'),
            lang('packing_plant_and_projects'),
            lang('commercial'),
            lang('cerro_prieto_irrigator')
        );
        
        $view_data['array_gerencia'] = $array_gerencia;

        $cant_eventos_por_gerencia = array();
        foreach($array_gerencia as $gerencia){
            foreach($array_tipo_evento as $tipo_evento){
                $cant_eventos_por_gerencia[$gerencia][$tipo_evento['nombre']] = 0;
            }
        }

        $contingencias = $this->Contingencies_event_record_model->get_details(array(
            'id_proyecto' => $id_proyecto,
            'start_date' => $start_date,
            'end_date' => $end_date
        ))->result();


        foreach($contingencias as $contingencia){
            $cant_eventos_por_gerencia[lang($contingencia->gerencia)][$contingencia->tipo_evento] += 1;
        }

        $array_data_eventos_por_gerencia = array();
			foreach($array_tipo_evento as $tipo_evento){
				$data_por_tipo_evento = array('name' => lang($tipo_evento['nombre']), 'color' => $tipo_evento['color'], 'data' => array());
				foreach($array_gerencia as $gerencia){
					$data_por_tipo_evento['data'][] = $cant_eventos_por_gerencia[$gerencia][$tipo_evento['nombre']];
				}
				$array_data_eventos_por_gerencia[] = $data_por_tipo_evento;
			}
        // echo '<pre>'; var_dump($array_data_eventos_por_gerencia);exit;

        $view_data['grafico_eventos_por_responsable'] = $array_data_eventos_por_gerencia;
        // FIN SECCIÓN GRÁFICO EVENTOS POR RESPONSABLE

        // SECCIÓN GRÁFICO EVENTOS POR TIPO DE AFECTACIÓN
        $array_tipo_afectacion = array(
            lang("health"),
            lang("water"),
            lang("ground"),
            lang("air"),
            lang("biodiversity"),
            lang("social"),
            lang("heritage"),
            lang("environmental_commitment")
        );
        
        $view_data['array_tipo_afectacion'] = $array_tipo_afectacion;

        $cant_eventos_por_gerencia = array();
        foreach($array_tipo_afectacion as $tipo_afectacion){
            foreach($array_tipo_evento as $tipo_evento){
                $cant_eventos_por_tipo_afectacion[$tipo_afectacion][$tipo_evento['nombre']] = 0;
            }
        }

        $contingencias = $this->Contingencies_event_record_model->get_details(array(
            'id_proyecto' => $id_proyecto,
            'start_date' => $start_date,
            'end_date' => $end_date
        ))->result();


        foreach($contingencias as $contingencia){
            $cant_eventos_por_tipo_afectacion[lang($contingencia->tipo_afectacion)][$contingencia->tipo_evento] += 1;
        }

        $array_data_eventos_por_tipo_afectacion = array();
			foreach($array_tipo_evento as $tipo_evento){
				$data_por_tipo_evento = array('name' => lang($tipo_evento['nombre']), 'color' => $tipo_evento['color'], 'data' => array());
				foreach($array_tipo_afectacion as $tipo_afectacion){
					$data_por_tipo_evento['data'][] = $cant_eventos_por_tipo_afectacion[$tipo_afectacion][$tipo_evento['nombre']];
				}
				$array_data_eventos_por_tipo_afectacion[] = $data_por_tipo_evento;
			}
        // echo '<pre>'; var_dump($array_data_eventos_por_tipo_afectacion);exit;

        $view_data['grafico_eventos_por_tipo_afectacion'] = $array_data_eventos_por_tipo_afectacion;
        // FIN SECCIÓN GRÁFICO EVENTOS POR TIPO DE AFECTACIÓN
        
        $view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));

        // PARA NOMBRE DE ARCHIVOS EXPORTABLES
		$view_data['sigla_cliente'] = $cliente->sigla;
		$view_data['sigla_proyecto'] = $proyecto->sigla;

        echo $this->load->view("contingencies_summary/contingencies_summary_details_by_date", $view_data);

    }


}