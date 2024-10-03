<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contingencies_record extends MY_Controller {
    
    private $id_modulo_cliente;
	private $id_submodulo_cliente;

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 12;
		$this->id_submodulo_cliente = 25;

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

        $cliente = $this->Clients_model->get_one($id_cliente);
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["project_info"] = $proyecto;
		$view_data["nombre_proyecto"] = $proyecto->title;

		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

        $this->template->rander("contingencies_record/index", $view_data);
    }

    function summary(){
        $view_data = array();
        $this->load->view('contingencies_record/summary/index', $view_data);
    }

    function event(){

        $view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
        
        $view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");

        $this->load->view('contingencies_record/events/index', $view_data);
    }

    function correction(){
        
        $view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
        
        $view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");

        $this->load->view('contingencies_record/corrections/index', $view_data);
    }

    function verification(){

        $view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
        
        $view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");
        $this->load->view('contingencies_record/verifications/index', $view_data);
    }

    function list_data_summary(){
        $id_proyecto = $this->session->project_context;

        $options = array(
            "id_proyecto" => $id_proyecto
        );
        
        $list_data = $this->Contingencies_event_record_model->get_summary($options)->result();

        $result = array();
        foreach($list_data as $data){
            $result[] = $this->_make_row_summary($data);
        }

        echo json_encode(array("data" => $result));
    }

    function _make_row_summary($data){

        $html_tiene_correccion = "";
        $correction_count = $this->Contingencies_correction_record_model->get_all_where(
            array('id_contingencia_evento' => $data->id, 'deleted' => 0)
        )->num_rows();

        if($correction_count > 0){
            $html_tiene_correccion = '<i class="fa fa-check" aria-hidden="true"></i>';
        }else{
            $html_tiene_correccion = '<i class="fa fa-times" aria-hidden="true"></i>';
        }

        $html_tiene_verificacion = "";
        $verification_count = $this->Contingencies_verification_record_model->get_all_where(
            array('id_contingencia_evento' => $data->id, 'deleted' => 0)
        )->num_rows();

        if($verification_count > 0){
            $html_tiene_verificacion = '<i class="fa fa-check" aria-hidden="true"></i>';
        }else{
            $html_tiene_verificacion = '<i class="fa fa-times" aria-hidden="true"></i>';
        }

        $row_data = array(
			$data->n_sacpa,
			$data->fecha_identificacion,
			lang($data->gerencia),
            lang($data->instrumento_gestion_ambiental),
			lang($data->tipo_evento),
            lang($data->tipo_afectacion),
			$html_tiene_correccion,
            $html_tiene_verificacion
		);
		 
		$row_data[] = modal_anchor(get_uri("contingencies_record/view_summary/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_event'),"data-post-id" => $data->id));
					 
        return $row_data;
    }

    public function view_summary(){
        $id_contingency = $this->input->post('id');
        if($id_contingency){
            $contingencia_info = $this->Contingencies_event_record_model->get_one($id_contingency);
            if($contingencia_info){
                $id_proyecto = $this->session->project_context;
                $id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
                $view_data['id_cliente'] = $id_cliente;
                
                // DATOS DE EVENTO
                $view_data['model_info'] = $contingencia_info;
                $view_data["label_column"] = "col-md-3";
                $view_data["field_column"] = "col-md-9";

                $view_data['fecha_identificacion'] = $contingencia_info->fecha_identificacion;
                $view_data['n_sacpa'] = $contingencia_info->n_sacpa;
                $view_data['gerencia'] = $contingencia_info->gerencia;
                $view_data['instrumento_gestion_ambiental'] = $contingencia_info->instrumento_gestion_ambiental;
                $view_data['clausula_incumplimiento'] = $contingencia_info->clausula_incumplimiento;
                $view_data['tipo_evento'] = $contingencia_info->tipo_evento;
                $view_data['tipo_afectacion'] = $contingencia_info->tipo_afectacion;
                $view_data['descripcion_no_conformidad'] = $contingencia_info->descripcion_no_conformidad;
                
                $archivos_evidencia_evento = json_decode($contingencia_info->evidencia_evento);
                $view_data['archivos_evidencia_evento'] = $archivos_evidencia_evento;

                // DATOS DE CORRECCIÓN
                $correccion_info = $this->Contingencies_correction_record_model->get_one_where(array('id_contingencia_evento' => $contingencia_info->id));
                // var_dump($correccion_info);exit;
                $view_data['descripcion_accion_correctiva'] = $correccion_info->descripcion_accion_correctiva;
                $view_data['responsable_correccion'] = $correccion_info->responsable_correccion;
                $view_data['fecha_correccion'] = $correccion_info->fecha_correccion;
                
                $archivo_evidencias_accion_correctiva = json_decode($correccion_info->evidencia_accion_correctiva);
                $view_data['archivo_evidencias_accion_correctiva'] = $archivo_evidencias_accion_correctiva;

                // DATOS DE VERIFICACIÓN
                $verificacion_info = $this->Contingencies_verification_record_model->get_one_where(array('id_contingencia_evento' => $contingencia_info->id));

                $view_data['descripcion_verificacion'] = $verificacion_info->descripcion_verificacion;
                $view_data['responsable_verificacion'] = $verificacion_info->responsable_verificacion;
                $view_data['fecha_verificacion'] = $verificacion_info->fecha_verificacion;

                $this->load->view('contingencies_record/summary/view', $view_data);
            
            }else{
                show_404();
            }
        }else{
            show_404();
        }
    }

    function get_excel(){

		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;

		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);

        // Definición de columnas
        $nombre_columnas_summary[] = array("nombre_columna" => lang("n_sacpa"), "id_tipo_campo" => "n_sacpa");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("management_2"), "id_tipo_campo" => "management_2");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("identification_date"), "id_tipo_campo" => "identification_date");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("environmental_management_instrument"), "id_tipo_campo" => "environmental_management_instrument");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("event_type"), "id_tipo_campo" => "event_type");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("affectation_type"), "id_tipo_campo" => "affectation_type");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("description_of_non_conformity_and_or_finding"), "id_tipo_campo" => "description_of_non_conformity_and_or_finding");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("responsible_for_correction"), "id_tipo_campo" => "responsible_for_correction");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("correction_date"), "id_tipo_campo" => "correction_date");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("responsible_for_verification"), "id_tipo_campo" => "responsible_for_verification");
        $nombre_columnas_summary[] = array("nombre_columna" => lang("verification_date"), "id_tipo_campo" => "verification_date");

        $list_data = $this->Contingencies_event_record_model->get_summary(array('id_proyecto' => $id_proyecto))->result();

        foreach($list_data as $data){
            if($puede_ver != 3){
                $result_summary[] = $this->_make_row_summary_excel($data);
            }
        }


        $this->load->library('excel');

		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle("")
							 ->setSubject("")
							 ->setDescription("")
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");

		if($client_info->color_sitio){
			$color_sitio = str_replace('#', '', $client_info->color_sitio);
		} else {
			$color_sitio = "00b393";
		}

		// ESTILOS
		$styleArray = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
			'fill' => array(
				'rotation' => 90,
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => $color_sitio)
			),
		);

		// LOGO
		if($client_info->id){
			if($client_info->logo){
				$url_logo = "files/mimasoft_files/client_".$client_info->id."/".$client_info->logo.".png";
			} else {
				$url_logo = "files/system/default-site-logo.png";
			}
		} else {
			$url_logo = "files/system/default-site-logo.png";
		}

		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath('./'.$url_logo);
		$objDrawing->setHeight(35);
		$objDrawing->setOffsetY(6);
		$objDrawing->setOffsetX(20);
		$objDrawing->setWorksheet($doc->getActiveSheet());
		$doc->getActiveSheet()->mergeCells('A1:B3');
		$doc->getActiveSheet()->getStyle('A1:B3')->applyFromArray($styleArray);

        // HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));

		$letra = $this->getNameFromNumber(count($nombre_columnas_summary)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("contingencies"))
			->setCellValue('C2', $project_info->title)
			->setCellValue('C3', lang("date").': '.$fecha.' '.lang("at").' '.$hora);

		$doc->setActiveSheetIndex(0);


        // SETEO DE CABECERAS DE CONTENIDO A LA HOJA DE EXCEL
        $col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		foreach($nombre_columnas_summary as $index => $columna){
			$valor = (!is_array($columna)) ? $columna : $columna["nombre_columna"];
			$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row = 5, $valor);
			$col++;
		}


        // CARGA DE CONTENIDO A LA HOJA DE EXCEL
		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		$row = 6; // EMPEZANDO DE LA FILA 6
		foreach($result_summary as $res){

			foreach($nombre_columnas_summary as $index_columnas => $columna){

				$name_col = PHPExcel_Cell::stringFromColumnIndex($col);
				$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(true);
				$valor = $res[$index_columnas];

				if(!is_array($columna)){

					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);

				} else {

					if($columna["id_tipo_campo"] == "n_sacpa"){

						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == "management_2"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } elseif($columna["id_tipo_campo"] == "identification_date"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } elseif($columna["id_tipo_campo"] == "environmental_management_instrument"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } elseif($columna["id_tipo_campo"] == "event_type"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } elseif($columna["id_tipo_campo"] == "affectation_type"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } elseif($columna["id_tipo_campo"] == "description_of_non_conformity_and_or_finding"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } elseif($columna["id_tipo_campo"] == "responsible_for_correction"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } elseif($columna["id_tipo_campo"] == "correction_date"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } elseif($columna["id_tipo_campo"] == "responsible_for_verification"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } elseif($columna["id_tipo_campo"] == "verification_date"){
                        
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

                    } else {
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);

					}

				}

				//if($columna["id_tipo_campo"] != "unity"){
					$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
				//}
				$col++;
			}

			$col = 0;
			$row++;

		}
		//$doc->getActiveSheet()->fromArray($result, NULL,"A6");


		// FILTROS
		$doc->getActiveSheet()->setAutoFilter('A5:'.$letra.'5');

		// ANCHO COLUMNAS
		/*$lastColumn = $doc->getActiveSheet()->getHighestColumn();
		$lastColumn++;
		$cells = array();
		for($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;
		}
		foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/


        $nombre_hoja = strlen(lang("contingencies_excel")) > 31 ? substr(lang("contingencies_excel"), 0, 28).'...' : lang("contingencies_excel");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);

		$filename = $client_info->sigla."_".$project_info->sigla."_".lang("contingencies_excel")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');
		$objWriter->save('php://output');
		exit;

    }

    function _make_row_summary_excel($data){
        $id_proyecto = $this->session->project_context;
        $id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;

        $row_data[] = $data->n_sacpa;
        $row_data[] = lang($data->gerencia);
        $row_data[] = date(get_setting_client_mimasoft($id_cliente, "date_format"), strtotime($data->fecha_identificacion));
        $row_data[] = lang($data->instrumento_gestion_ambiental);
        $row_data[] = lang($data->tipo_evento);
        $row_data[] = lang($data->tipo_afectacion);
        $row_data[] = $data->descripcion_no_conformidad;
        $row_data[] = $data->responsable_correccion ? lang($data->responsable_correccion) : '-';
        $row_data[] = $data->fecha_correccion ? date(get_setting_client_mimasoft($id_cliente, "date_format"), strtotime($data->fecha_correccion)) : '-';
        $row_data[] = $data->responsable_verificacion ? $data->responsable_verificacion : '-';
        $row_data[] = $data->fecha_verificacion ? date(get_setting_client_mimasoft($id_cliente, "date_format"), strtotime($data->fecha_verificacion)) : '-';
        
        return $row_data;
    }

    function modal_form_event(){
        $id_contingencia = $this->input->post('id');

        if($id_contingencia){
            $model_info = $this->Contingencies_event_record_model->get_one($id_contingencia);

            $view_data['model_info'] = $model_info;
            $view_data['gerencia'] = $model_info->gerencia;
            $view_data['instrumento_gestion_ambiental'] = $model_info->instrumento_gestion_ambiental;
            $view_data['tipo_evento'] = $model_info->tipo_evento;
            $view_data['tipo_afectacion'] = $model_info->tipo_afectacion;

            $archivo_evidencias_evento = json_decode($model_info->evidencia_evento);
            $view_data['archivo_evidencias_evento'] = $archivo_evidencias_evento;
        }


        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $gerencia_dropdown = array(
            '' => '-',
            'agricultural' => lang('agricultural'),
            'human_management' => lang('human_management'),
            'administration_and_finance' => lang('administration_and_finance'),
            'general' => lang('general'),
            'packing_plant_and_projects' => lang('packing_plant_and_projects'),
            'commercial' => lang('commercial'),
            'cerro_prieto_irrigator' => lang('cerro_prieto_irrigator'),
            'sustainability_and_external_communications' => lang('sustainability_and_external_communications')
        );
        
        $view_data['gerencia_dropdown'] = $gerencia_dropdown;

        $instrumento_gestion_ambiental_dropdown = array(
            "" => "-",
            "mpama" => lang("mpama"),
            "pama" => lang("pama"),
            "pama_and_mpama" => lang("pama_and_mpama"),
            "mdia" => lang("mdia"),
            "dia_and_mdia" => lang("dia_and_mdia"),
            "n/a" => lang("n/a")
        );
        
        $view_data['instrumento_gestion_ambiental_dropdown'] = $instrumento_gestion_ambiental_dropdown;

        
        $tipo_evento_dropdown = array(
            "" => "-",
            "near_incident" => lang("near_incident"),
            "minor_incident" => lang("minor_incident"),
            "significant_incident" => lang("significant_incident"),
            "environmental_damage" => lang("environmental_damage"),
            "environmental_emergency" => lang("environmental_emergency"),
        );
        $view_data['tipo_evento_dropdown'] = $tipo_evento_dropdown;

        $tipo_afectacion_dropdown = array(
            "" => "-",
            "health" => lang("health"),
            "water" => lang("water"),
            "ground" => lang("ground"),
            "air" => lang("air"),
            "biodiversity" => lang("biodiversity"),
            "social" => lang("social"),
            "heritage" => lang("heritage"),
            "environmental_commitment" => lang("environmental_commitment")
        );
        $view_data['tipo_afectacion_dropdown'] = $tipo_afectacion_dropdown;

        $this->load->view('contingencies_record/events/modal_form', $view_data);
    }

    function save_event(){
        // var_dump($this->input->post());exit;

        $id_proyecto = $this->session->project_context;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
        $id_contingency = $this->input->post('id_contingencia');
        
        $fecha_identificacion = $this->input->post('fecha_identificacion');
        $n_sacpa = $this->input->post('n_sacpa');
        $gerencia = $this->input->post('gerencia');
        $instrumento_gestion_ambiental = $this->input->post('instrumento_gestion_ambiental');
        $clausula_incumplimiento = $this->input->post('clausula_incumplimiento');
        $tipo_evento = $this->input->post('tipo_evento');
        $tipo_afectacion = $this->input->post('tipo_afectacion');
        $descripcion_no_conformidad = $this->input->post('descripcion_no_conformidad');

        validate_submitted_data(array(
            "fecha_identificacion" => "required",
            "n_sacpa" => "required",
            "gerencia" => "required",
            "instrumento_gestion_ambiental" => "required",
            "clausula_incumplimiento" => "required",
            "tipo_evento" => "required",
            "tipo_afectacion" => "required",
            "descripcion_no_conformidad" => "required"
        ));

        
        $data_contingencies_record = array(
            "id_proyecto" => $id_proyecto,
            "fecha_identificacion" => $fecha_identificacion,
            "n_sacpa" => $n_sacpa,
            "gerencia" => $gerencia,
            "instrumento_gestion_ambiental" => $instrumento_gestion_ambiental,
            "clausula_incumplimiento" => $clausula_incumplimiento,
            "tipo_evento" => $tipo_evento,
            "tipo_afectacion" => $tipo_afectacion,
            "descripcion_no_conformidad" => $descripcion_no_conformidad
        );

        if($id_contingency){ //edit
		
			$data_contingencies_record["modified_by"] = $this->login_user->id;
			$data_contingencies_record["modified"] = get_current_utc_time();
			$save_id = $this->Contingencies_event_record_model->save($data_contingencies_record, $id_contingency);
		
		} else { //insert
			
			$data_contingencies_record["created_by"] = $this->login_user->id;
			$data_contingencies_record["created"] = get_current_utc_time();
			$save_id = $this->Contingencies_event_record_model->save($data_contingencies_record);

		}
        
        if($save_id){
            $model_info = $this->Contingencies_event_record_model->get_one($save_id);

            // Borrar archivos eliminados (Se vuelven a guardar en el json sólo los archivos que no estan en la lista de archivos a eliminar)
            $nombre_archivos_evidencia_eliminar = $this->input->post('nombre_archivos_evidencia_eliminar');
            $data_archivos = array();
            if($nombre_archivos_evidencia_eliminar){
                $archivos = json_decode($model_info->evidencia_accion_correctiva);
                $data_archivos = array_diff($archivos, $nombre_archivos_evidencia_eliminar);
                
                $data_evidencia = array('evidencia_evento' => json_encode($data_archivos));
                $this->Contingencies_event_record_model->save($data_evidencia, $save_id);

                // Borrar archivos de carpeta
                foreach($nombre_archivos_evidencia_eliminar as $nombre_archivo_eliminar){
                    $filename = $nombre_archivo_eliminar;
                    $file_path = "files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/contingencia_".$id_contingency."/event".$filename;
                    delete_file_from_directory($file_path);
                }
            }

            // Guardar archivos existentes y/o nuevos
            $model_info = $this->Contingencies_event_record_model->get_one($save_id);
            $evidencias_evento = $this->input->post('evidencias_evento');

            if($evidencias_evento){
                $this->create_contingencies_event_folder($save_id);
                
                $archivos_subidos = array();
                foreach($evidencias_evento as $evidencia){
                    $nombre_archivo = $this->input->post("file_name_".$evidencia); 
                    $archivos_subidos[] = move_temp_file("evidencias_evento_".$nombre_archivo, "files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/contingencia_".$save_id."/event/", "", "", "");
                }
                $data_archivos = array();
                if($model_info->evidencia_evento){
                    $archivos_existentes = json_decode($model_info->evidencia_evento);
                    $data_archivos = array_merge($archivos_existentes, $archivos_subidos);
                    $data_evidencia = array('evidencia_evento' => json_encode($data_archivos));
                    $this->Contingencies_event_record_model->save($data_evidencia, $save_id);
                }else{
                    $data_evidencia = array('evidencia_evento' => json_encode($archivos_subidos));
                    $this->Contingencies_event_record_model->save($data_evidencia, $save_id);
                }
            }

            echo json_encode(array("success" => true, "data" => $this->_row_data_event($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        }else{
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function list_data_event(){
        $id_proyecto = $this->session->project_context;

        $options = array(
            "id_proyecto" => $id_proyecto
        );
        
        $list_data = $this->Contingencies_event_record_model->get_details($options)->result();

        $result = array();
        foreach($list_data as $data){
            $result[] = $this->_make_row_event($data);
        }

        echo json_encode(array("data" => $result));
    }

    function _row_data_event($id){
        $data = $this->Contingencies_event_record_model->get_one($id);
        return $this->_make_row_event($data);
    }

    function _make_row_event($data){

        $puede_editar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"editar"
		);
        $puede_eliminar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"eliminar"
		);

        $tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.htmlspecialchars($data->descripcion_no_conformidad, ENT_QUOTES).'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$descripcion_no_conformidad = ((!$data->descripcion_no_conformidad) || $data->descripcion_no_conformidad == "") ? "-" : $tooltip_descripcion; 

        $row_data = array(
			$data->fecha_identificacion,
			$data->n_sacpa,
			lang($data->gerencia),
			lang($data->instrumento_gestion_ambiental),
			lang($data->tipo_evento),
			$descripcion_no_conformidad
		);
					
		if($puede_editar == 3){
			$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_event'), "data-post-id" => $data->id, "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
		} else {
			$boton_editar = modal_anchor(get_uri("contingencies_record/modal_form_event"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_event'), "data-post-id" => $data->id));
		}

        $correction_count = $this->Contingencies_correction_record_model->get_all_where(
            array('id_contingencia_evento' => $data->id, 'deleted' => 0)
        )->num_rows();
        $verification_count = $this->Contingencies_verification_record_model->get_all_where(
            array('id_contingencia_evento' => $data->id, 'deleted' => 0)
        )->num_rows();
        
        //if($puede_eliminar == 3 && $existen_otros_formularios){
        if($puede_eliminar == 3 || $correction_count > 0 || $verification_count > 0){
            $boton_eliminar = '<span style="cursor: not-allowed;">'. modal_anchor(get_uri(), "<i class='fa fa-times'></i>", array("class" => "edit", "title" => lang('delete_event'), "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
        }else{
            $boton_eliminar = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_event'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("contingencies_record/delete_event"), "data-action" => "delete-confirmation"));
        }
		 
		$row_data[] = modal_anchor(get_uri("contingencies_record/view_event/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_event'),"data-post-id_contingencia" => $data->id)). $boton_editar. $boton_eliminar;
					 
        return $row_data;
    }

    public function view_event(){
        $id_contingency = $this->input->post('id_contingencia');
        if($id_contingency){
            $contingencia_info = $this->Contingencies_event_record_model->get_one($id_contingency);
            if($contingencia_info){
                $id_proyecto = $this->session->project_context;
                $id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
                $view_data['id_cliente'] = $id_cliente;
                
                $view_data['model_info'] = $contingencia_info;
                $view_data["label_column"] = "col-md-3";
                $view_data["field_column"] = "col-md-9";

                $view_data['fecha_identificacion'] = $contingencia_info->fecha_identificacion;
                $view_data['n_sacpa'] = $contingencia_info->n_sacpa;
                $view_data['gerencia'] = $contingencia_info->gerencia;
                $view_data['instrumento_gestion_ambiental'] = $contingencia_info->instrumento_gestion_ambiental;
                $view_data['clausula_incumplimiento'] = $contingencia_info->clausula_incumplimiento;
                $view_data['tipo_evento'] = $contingencia_info->tipo_evento;
                $view_data['tipo_afectacion'] = $contingencia_info->tipo_afectacion;
                $view_data['descripcion_no_conformidad'] = $contingencia_info->descripcion_no_conformidad;
                
                $archivos_evidencia_evento = json_decode($contingencia_info->evidencia_evento);
                // var_dump($archivos_evidencia_evento);exit;
                $view_data['archivos_evidencia_evento'] = $archivos_evidencia_evento;

                $this->load->view('contingencies_record/events/view', $view_data);
            
            }else{
                show_404();
            }
        }else{
            show_404();
        }
    }

    public function delete_event(){
        $id = $this->input->post('id');

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));


        // Validación: No se puede eliminar un evento/contingencia si éste tiene Correcciones o Verificaciones asociadas
		$correction_count = $this->Contingencies_correction_record_model->get_all_where(
            array(
                'id_contingencia_evento' => $id,
                'deleted' => 0
            ))->num_rows();
        
        $verification_count = $this->Contingencies_verification_record_model->get_all_where(
            array(
                'id_contingencia_evento' => $id,
                'deleted' => 0
        ))->num_rows();
        
        
		
		if($correction_count > 0 || $verification_count > 0){
			echo json_encode(array("success" => false, 'message' => lang("delete_event_validation_message")));
			exit();
		}

        if ($this->Contingencies_event_record_model->delete($id)) {
	
            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    public function modal_form_correction(){
        $id_correccion = $this->input->post('id');
        $id_proyecto = $this->session->project_context;
        
        if($id_correccion){
            $model_info = $this->Contingencies_correction_record_model->get_one($id_correccion);
            
            $view_data['model_info'] = $model_info;
            $view_data['evento'] = $model_info->id_contingencia_evento;
            $view_data['descripcion_accion_correctiva'] = $model_info->descripcion_accion_correctiva;
            $view_data['responsable_correccion'] = $model_info->responsable_correccion;
            $view_data['fecha_correccion'] = $model_info->fecha_correccion;

            $archivo_evidencias_accion_correctiva = json_decode($model_info->evidencia_accion_correctiva);
            // var_dump($model_info);exit;
            $view_data['archivo_evidencias_accion_correctiva'] = $archivo_evidencias_accion_correctiva;
        }


        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        // Eventos que deben ser deshabilitados en dropdown list
        $eventos_en_correcciones = array();
        $correcciones = $this->Contingencies_correction_record_model->get_all()->result();
        foreach($correcciones as $correccion){
            $eventos_en_correcciones[] = $correccion->id_contingencia_evento;
        }
        $view_data['eventos_en_correcciones'] = $eventos_en_correcciones;

        $eventos_contingencia = $this->Contingencies_event_record_model->get_all_where(array('id_proyecto' => $id_proyecto))->result();
        $evento_dropdown = array('' => '-' );
        foreach($eventos_contingencia as $evento){
            $evento_dropdown[$evento->id] = $evento->n_sacpa." (".$evento->fecha_identificacion.")";
        }
        $view_data['evento_dropdown'] = $evento_dropdown;
        
        $responsable_correccion_dropdown = array(
            '' => '-',
            "management_2" => lang("management_2"),
            "superintendence" => lang("superintendence"),
            "leadership" => lang("leadership"),
            "other" => lang("other")
        );
        $view_data['responsable_correccion_dropdown'] = $responsable_correccion_dropdown;

        $this->load->view('contingencies_record/corrections/modal_form', $view_data);
    }

    function save_correction(){
        // var_dump($this->input->post());exit;

        $id_proyecto = $this->session->project_context;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
        $id_correction = $this->input->post('id_correccion');

        $id_evento = $this->input->post('evento');
        $descripcion_accion_correctiva = $this->input->post('descripcion_accion_correctiva');
        $responsable_correccion = $this->input->post('responsable_correccion');
        $fecha_correccion = $this->input->post('fecha_correccion');

        validate_submitted_data(array(
            "evento" => "numeric|required"
        ));

        
        $data_correction_record = array(
            "id_contingencia_evento" => $id_evento,
            "descripcion_accion_correctiva" => $descripcion_accion_correctiva,
            "responsable_correccion" => $responsable_correccion,
            "fecha_correccion" => $fecha_correccion
        );

        if($id_correction){ //edit
		
			$data_correction_record["modified_by"] = $this->login_user->id;
			$data_correction_record["modified"] = get_current_utc_time();
			$save_id = $this->Contingencies_correction_record_model->save($data_correction_record, $id_correction);
		
		} else { //insert
			
			$data_correction_record["created_by"] = $this->login_user->id;
			$data_correction_record["created"] = get_current_utc_time();
			$save_id = $this->Contingencies_correction_record_model->save($data_correction_record);

		}
        
        if($save_id){
            $model_info = $this->Contingencies_correction_record_model->get_one($save_id);

            // Borrar archivos eliminados (Se vuelven a guardar en el json sólo los archivos que no estan en la lista de archivos a eliminar)
            $nombre_archivos_evidencia_eliminar = array_unique($this->input->post('nombre_archivos_evidencia_eliminar'));
            $data_archivos = array();
            if($nombre_archivos_evidencia_eliminar){
                $archivos = json_decode($model_info->evidencia_accion_correctiva);
                $data_archivos = array_diff($archivos, $nombre_archivos_evidencia_eliminar);
                
                $data_evidencia = array('evidencia_accion_correctiva' => json_encode($data_archivos));
                $this->Contingencies_correction_record_model->save($data_evidencia, $save_id);

                // Borrar archivos de carpeta
                foreach($nombre_archivos_evidencia_eliminar as $nombre_archivo_eliminar){
                    $filename = $nombre_archivo_eliminar;
                    $file_path = "files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/contingencia_".$model_info->id_contingencia_evento."/correction/".$filename;
                    delete_file_from_directory($file_path);
                }
            }

            // Guardar archivos existentes y/o nuevos
            $model_info = $this->Contingencies_correction_record_model->get_one($save_id);
            $evidencias_correccion = $this->input->post('evidencias_accion_correctiva');

            if($evidencias_correccion){
                $this->create_contingencies_correction_folder($model_info->id_contingencia_evento);
                
                $archivos_subidos = array();
                foreach($evidencias_correccion as $evidencia){
                    $nombre_archivo = $this->input->post("file_name_".$evidencia); 
                    $archivos_subidos[] = move_temp_file("evidencias_accion_correctiva_".$nombre_archivo, "files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/contingencia_".$model_info->id_contingencia_evento."/correction/", "", "", "");
                }

                $data_archivos = array();
                if($model_info->evidencia_accion_correctiva){
                    $archivos_existentes = json_decode($model_info->evidencia_accion_correctiva);
                    $data_archivos = array_merge($archivos_existentes, $archivos_subidos);
                    $data_evidencia = array('evidencia_accion_correctiva' => json_encode($data_archivos));
                    $this->Contingencies_correction_record_model->save($data_evidencia, $save_id);
                }else{
                    $data_evidencia = array('evidencia_accion_correctiva' => json_encode($archivos_subidos));
                    $this->Contingencies_correction_record_model->save($data_evidencia, $save_id);
                }
            }

            echo json_encode(array("success" => true, "data" => $this->_row_data_correction($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        }else{
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function list_data_correction(){
        $id_proyecto = $this->session->project_context;

        $options = array(
            "id_proyecto" => $id_proyecto
        );
        
        $list_data = $this->Contingencies_correction_record_model->get_details($options)->result();

        $result = array();
        foreach($list_data as $data){
            $result[] = $this->_make_row_correction($data);
        }

        echo json_encode(array("data" => $result));
    }

    function _row_data_correction($id){
        $data = $this->Contingencies_correction_record_model->get_one($id);
        return $this->_make_row_correction($data);
    }

    function _make_row_correction($data){

        $puede_editar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"editar"
		);
        $puede_eliminar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"eliminar"
		);

        $tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.htmlspecialchars($data->descripcion_accion_correctiva, ENT_QUOTES).'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$descripcion_accion_correctiva = ((!$data->descripcion_accion_correctiva) || $data->descripcion_accion_correctiva == "") ? "-" : $tooltip_descripcion; 
        
        $evento_contingencia = $this->Contingencies_event_record_model->get_one($data->id_contingencia_evento);
        $evento = $evento_contingencia->n_sacpa." (".$evento_contingencia->fecha_identificacion.")";
        
        $row_data = array(
            $data->id,
            $evento,
            $descripcion_accion_correctiva,
			lang($data->responsable_correccion),
			$data->fecha_correccion
		);
					
		if($puede_editar == 3){
			$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_event'), "data-post-id" => $data->id, "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
		} else {
			$boton_editar = modal_anchor(get_uri("contingencies_record/modal_form_correction"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_event'), "data-post-id" => $data->id));
		}

        /* $correction_count = $this->Contingencies_correction_record_model->get_all_where(
            array('id_contingencia_evento' => $data->id)
        )->num_rows();
        $verification_count = $this->Contingencies_verification_record_model->get_all_where(
            array('id_contingencia_evento' => $data->id)
        )->num_rows(); */

        if($puede_eliminar == 3 ){
        // if($puede_eliminar == 3 || $correction_count > 0 || $verification_count > 0){
            $boton_eliminar = '<span style="cursor: not-allowed;">'. modal_anchor(get_uri(), "<i class='fa fa-times'></i>", array("class" => "edit", "title" => lang('delete_correction'), "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
        }else{
            $boton_eliminar = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_correction'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("contingencies_record/delete_correction"), "data-action" => "delete-confirmation"));
        }
		 
		$row_data[] = modal_anchor(get_uri("contingencies_record/view_correction/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_correction'),"data-post-id_correccion" => $data->id)). $boton_editar. $boton_eliminar;
					 
        return $row_data;
    }

    public function view_correction(){
        $id_correction = $this->input->post('id_correccion');
        if($id_correction){
            $contingencia_info = $this->Contingencies_correction_record_model->get_one($id_correction);
            if($contingencia_info){
                $id_proyecto = $this->session->project_context;
                $id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
                $view_data['id_cliente'] = $id_cliente;
                
                $view_data['model_info'] = $contingencia_info;
                $view_data["label_column"] = "col-md-3";
                $view_data["field_column"] = "col-md-9";

                $evento_contingencia = $this->Contingencies_event_record_model->get_one($contingencia_info->id_contingencia_evento);

                $view_data['evento'] = $evento_contingencia->n_sacpa." (".$evento_contingencia->fecha_identificacion.")";
                $view_data['descripcion_accion_correctiva'] = $contingencia_info->descripcion_accion_correctiva;
                $view_data['responsable_correccion'] = $contingencia_info->responsable_correccion;
                $view_data['fecha_correccion'] = $contingencia_info->fecha_correccion;

                $archivo_evidencias_accion_correctiva = json_decode($contingencia_info->evidencia_accion_correctiva);
                // var_dump($contingencia_info);exit;
                $view_data['archivo_evidencias_accion_correctiva'] = $archivo_evidencias_accion_correctiva;

                $this->load->view('contingencies_record/corrections/view', $view_data);
            
            }else{
                show_404();
            }
        }else{
            show_404();
        }
    }

    public function delete_correction(){
        $id = $this->input->post('id');

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        if ($this->Contingencies_correction_record_model->delete($id)) {
	
            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    public function modal_form_verification(){
        $id_verificacion = $this->input->post('id');
        $id_proyecto = $this->session->project_context;
        
        if($id_verificacion){
            $model_info = $this->Contingencies_verification_record_model->get_one($id_verificacion);
            
            $view_data['model_info'] = $model_info;
            $view_data['evento'] = $model_info->id_contingencia_evento;
            $view_data['descripcion_verificacion'] = $model_info->descripcion_verificacion;
            $view_data['responsable_verificacion'] = $model_info->responsable_verificacion;
            $view_data['fecha_verificacion'] = $model_info->fecha_verificacion;

        }


        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        // Eventos que deben ser deshabilitados en dropdown list
        $eventos_en_verificaciones = array();
        $verificaciones = $this->Contingencies_verification_record_model->get_all()->result();
        foreach($verificaciones as $verificacion){
            $eventos_en_verificaciones[] = $verificacion->id_contingencia_evento;
        }
        $view_data['eventos_en_verificaciones'] = $eventos_en_verificaciones;

        $eventos_contingencia = $this->Contingencies_event_record_model->get_all_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->result();

        $evento_dropdown = array('' => '-' );
        foreach($eventos_contingencia as $evento){
            $evento_dropdown[$evento->id] = $evento->n_sacpa." (".$evento->fecha_identificacion.")";
        }
        $view_data['evento_dropdown'] = $evento_dropdown;

        $responsable_verificacion_dropdown = array(
            '' => '-',
            "Carlos Cabrera" => "Carlos Cabrera",
            "Luis Martínez" => "Luis Martínez",
            "Maricielo Rojas" => "Maricielo Rojas",
            "Claudia Lescano" => "Claudia Lescano",
            "Betty Ysla" => "Betty Ysla",
            "Yanina Flores" => "Yanina Flores",
            "Sara Mendoza" => "Sara Mendoza",
            "Otro" => "Otro"
        );
        $view_data['responsable_verificacion_dropdown'] = $responsable_verificacion_dropdown;

        $this->load->view('contingencies_record/verifications/modal_form', $view_data);
    }

    function save_verification(){
        // var_dump($this->input->post());exit;

        $id_proyecto = $this->session->project_context;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
        $id_verification = $this->input->post('id_verificacion');

        $id_evento = $this->input->post('evento');
        $descripcion_verificacion = $this->input->post('descripcion_verificacion');
        $responsable_verificacion = $this->input->post('responsable_verificacion');
        $fecha_verificacion = $this->input->post('fecha_verificacion');

        validate_submitted_data(array(
            "evento" => "numeric|required"
        ));

        
        $data_verification_record = array(
            "id_contingencia_evento" => $id_evento,
            "descripcion_verificacion" => $descripcion_verificacion,
            "responsable_verificacion" => $responsable_verificacion,
            "fecha_verificacion" => $fecha_verificacion
        );

        if($id_verification){ //edit
		
			$data_verification_record["modified_by"] = $this->login_user->id;
			$data_verification_record["modified"] = get_current_utc_time();
			$save_id = $this->Contingencies_verification_record_model->save($data_verification_record, $id_verification);
		
		} else { //insert
			
			$data_verification_record["created_by"] = $this->login_user->id;
			$data_verification_record["created"] = get_current_utc_time();
			$save_id = $this->Contingencies_verification_record_model->save($data_verification_record);

		}
        
        if($save_id){
            $model_info = $this->Contingencies_verification_record_model->get_one($save_id);

            // Borrar archivos eliminados (Se vuelven a guardar en el json sólo los archivos que no estan en la lista de archivos a eliminar)
            /* $nombre_archivos_evidencia_eliminar = array_unique($this->input->post('nombre_archivos_evidencia_eliminar'));
            $data_archivos = array();
            if($nombre_archivos_evidencia_eliminar){
                $archivos = json_decode($model_info->evidencia_accion_correctiva);
                $data_archivos = array_diff($archivos, $nombre_archivos_evidencia_eliminar);
                
                $data_evidencia = array('evidencia_accion_correctiva' => json_encode($data_archivos));
                $this->Contingencies_verification_record_model->save($data_evidencia, $save_id);

                // Borrar archivos de carpeta
                foreach($nombre_archivos_evidencia_eliminar as $nombre_archivo_eliminar){
                    $filename = $nombre_archivo_eliminar;
                    $file_path = "files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/contingencia_".$model_info->id_contingencia_evento."/verification/".$filename;
                    delete_file_from_directory($file_path);
                }
            }

            // Guardar archivos existentes y/o nuevos
            $model_info = $this->Contingencies_verification_record_model->get_one($save_id);
            $evidencias_correccion = $this->input->post('evidencias_accion_correctiva');

            if($evidencias_correccion){
                $this->create_contingencies_verification_folder($model_info->id_contingencia_evento);
                
                $archivos_subidos = array();
                foreach($evidencias_correccion as $evidencia){
                    $nombre_archivo = $this->input->post("file_name_".$evidencia); 
                    $archivos_subidos[] = move_temp_file("evidencias_accion_correctiva_".$nombre_archivo, "files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/contingencia_".$model_info->id_contingencia_evento."/verification/", "", "", "");
                }

                $data_archivos = array();
                if($model_info->evidencia_accion_correctiva){
                    $archivos_existentes = json_decode($model_info->evidencia_accion_correctiva);
                    $data_archivos = array_merge($archivos_existentes, $archivos_subidos);
                    $data_evidencia = array('evidencia_accion_correctiva' => json_encode($data_archivos));
                    $this->Contingencies_verification_record_model->save($data_evidencia, $save_id);
                }else{
                    $data_evidencia = array('evidencia_accion_correctiva' => json_encode($archivos_subidos));
                    $this->Contingencies_verification_record_model->save($data_evidencia, $save_id);
                }
            } */

            echo json_encode(array("success" => true, "data" => $this->_row_data_verification($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        }else{
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function list_data_verification(){
        $id_proyecto = $this->session->project_context;

        $options = array(
            "id_proyecto" => $id_proyecto
        );
        
        $list_data = $this->Contingencies_verification_record_model->get_details($options)->result();

        $result = array();
        foreach($list_data as $data){
            $result[] = $this->_make_row_verification($data);
        }

        echo json_encode(array("data" => $result));
    }


    function _row_data_verification($id){
        $data = $this->Contingencies_verification_record_model->get_one($id);
        return $this->_make_row_verification($data);
    }

    function _make_row_verification($data){

        $puede_editar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"editar"
		);
        $puede_eliminar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"eliminar"
		);

        $tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.htmlspecialchars($data->descripcion_verificacion, ENT_QUOTES).'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$descripcion_verificacion = ((!$data->descripcion_verificacion) || $data->descripcion_verificacion == "") ? "-" : $tooltip_descripcion; 
        
        $evento_contingencia = $this->Contingencies_event_record_model->get_one($data->id_contingencia_evento);
        $evento = $evento_contingencia->n_sacpa." (".$evento_contingencia->fecha_identificacion.")";
        
        $row_data = array(
            $data->id,
            $evento,
            $descripcion_verificacion,
			$data->responsable_verificacion,
			$data->fecha_verificacion
		);
					
		if($puede_editar == 3){
			$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_verification'), "data-post-id" => $data->id, "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
		} else {
			$boton_editar = modal_anchor(get_uri("contingencies_record/modal_form_verification"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_verification'), "data-post-id" => $data->id));
		}

        /* $verification_count = $this->Contingencies_verification_record_model->get_all_where(
            array('id_contingencia_evento' => $data->id)
        )->num_rows();
        $verification_count = $this->Contingencies_verification_record_model->get_all_where(
            array('id_contingencia_evento' => $data->id)
        )->num_rows(); */

        if($puede_eliminar == 3 ){
        // if($puede_eliminar == 3 || $verification_count > 0 || $verification_count > 0){
            $boton_eliminar = '<span style="cursor: not-allowed;">'. modal_anchor(get_uri(), "<i class='fa fa-times'></i>", array("class" => "edit", "title" => lang('delete_verification'), "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
        }else{
            $boton_eliminar = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_verification'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("contingencies_record/delete_verification"), "data-action" => "delete-confirmation"));
        }
		 
		$row_data[] = modal_anchor(get_uri("contingencies_record/view_verification/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_verification'),"data-post-id_verificacion" => $data->id)). $boton_editar. $boton_eliminar;
					 
        return $row_data;
    }

    public function view_verification(){
        $id_verification = $this->input->post('id_verificacion');
        if($id_verification){
            $contingencia_info = $this->Contingencies_verification_record_model->get_one($id_verification);
            if($contingencia_info){
                $id_proyecto = $this->session->project_context;
                $id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
                $view_data['id_cliente'] = $id_cliente;
                
                $view_data['model_info'] = $contingencia_info;
                $view_data["label_column"] = "col-md-3";
                $view_data["field_column"] = "col-md-9";

                $evento_contingencia = $this->Contingencies_event_record_model->get_one($contingencia_info->id_contingencia_evento);

                $view_data['evento'] = $evento_contingencia->n_sacpa." (".$evento_contingencia->fecha_identificacion.")";
                $view_data['descripcion_verificacion'] = $contingencia_info->descripcion_verificacion;
                $view_data['responsable_verificacion'] = $contingencia_info->responsable_verificacion;
                $view_data['fecha_verificacion'] = $contingencia_info->fecha_verificacion;
                /* 
                $archivo_evidencias_accion_correctiva = json_decode($contingencia_info->evidencia_accion_correctiva);
                // var_dump($contingencia_info);exit;
                $view_data['archivo_evidencias_accion_correctiva'] = $archivo_evidencias_accion_correctiva; */

                $this->load->view('contingencies_record/verifications/view', $view_data);
            
            }else{
                show_404();
            }
        }else{
            show_404();
        }
    }

    public function delete_verification(){
        $id = $this->input->post('id');

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        if ($this->Contingencies_verification_record_model->delete($id)) {
	
            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }
    

    /* check valid file for client */

    function validate_file() {
    
        $file_name = $this->input->post("file_name");
        
        if (!$file_name){
            echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
        }
        
        echo json_encode(array("success" => true));
        
        /*
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($file_ext == 'xlsx') {
            echo json_encode(array("success" => true));
        }else{
            echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
        }
        */
        
    }

    function upload_multiple_file($file_type = "") {

		$id_campo = $this->input->post("cid");
		//$number = uniqid();
		
		if($id_campo){
			upload_file_to_temp("file", array("id_campo" => $id_campo));
		}else {
			upload_file_to_temp();
		}
		/*
		if($id_campo){
			upload_file_to_temp("file", array("id_campo" => $id_campo. "_" . $number));
		}else {
			upload_file_to_temp();
		}
		*/
		
	}

    function create_contingencies_event_folder($id_contingency) {
		
		$contingencia = $this->Contingencies_event_record_model->get_one($id_contingency);
		$id_proyecto = $contingencia->id_proyecto;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		if(!file_exists(__DIR__.'/../../files/contingencias/client_'.$id_cliente.'/project_'.$id_proyecto.'/contingencia_'.$id_contingency.'/event')) {
			if(mkdir(__DIR__.'/../../files/contingencias/client_'.$id_cliente.'/project_'.$id_proyecto.'/contingencia_'.$id_contingency.'/event', 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}

    function download_event_evidence_file($id_contingency, $filename){
		
        //serilize the path
		$file_data = serialize(array(array("file_name" => $filename)));
		
        $id_proyecto = $this->session->project_context;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
        // var_dump($file_data);exit;
        // echo "files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/";exit;
		download_app_files("files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/contingencia_".$id_contingency."/event/", $file_data);
		
	}

    function delete_event_evidence_file(){
		
		$filename = $this->input->post('filename');
        $id_contingencia = $this->input->post('id_contingencia');
        $key_contingencia = $this->input->post('key_contingencia');

		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => "", 'nombre_campo' => "evidencia_evento_".$id_contingencia."_".$key_contingencia, "nombre_archivo" => $filename));

	}

    function create_contingencies_correction_folder($id_contingency) {
		$contingencia = $this->Contingencies_event_record_model->get_one($id_contingency);
		$id_proyecto = $contingencia->id_proyecto;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		if(!file_exists(__DIR__.'/../../files/contingencias/client_'.$id_cliente.'/project_'.$id_proyecto.'/contingencia_'.$id_contingency.'/correction')) {
			if(mkdir(__DIR__.'/../../files/contingencias/client_'.$id_cliente.'/project_'.$id_proyecto.'/contingencia_'.$id_contingency.'/correction', 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}

    function download_correction_evidence_file($id_contingency, $filename){
		
        //serilize the path
		$file_data = serialize(array(array("file_name" => $filename)));
		
        $id_proyecto = $this->session->project_context;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
        // var_dump($file_data);exit;
        // echo "files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/";exit;
		download_app_files("files/contingencias/client_".$id_cliente."/project_".$id_proyecto."/contingencia_".$id_contingency."/correction/", $file_data);
		
	}

    function delete_correction_evidence_file(){
		
		$filename = $this->input->post('filename');
        $id_correccion = $this->input->post('id_correccion');
        $key_correccion = $this->input->post('key_correccion');

		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => "", 'nombre_campo' => "evidencia_accion_correctiva_".$id_correccion."_".$key_correccion, "nombre_archivo" => $filename));

	}

	private function getNameFromNumber($num){
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return (string)$letter;
		}
	}
}