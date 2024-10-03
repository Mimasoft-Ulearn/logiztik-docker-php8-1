<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron_job {

    private $today = null;
    private $ci = null;
	
    function run() {
		
		$this->today = get_today_date();
    	$this->ci = get_instance();
		
		// LLAMADA AL HISTÓRICO DE NOTIFICACIONES PARA ENVIAR CORREOS
		$ayn_notif_historical = $this->ci->AYN_Notif_historical_model->get_all_where(array(
			//"module_level" => "general",
			"is_email_sended" => 0,
			"deleted" => 0
		))->result();
		
		foreach($ayn_notif_historical as $notif_historical){
			
			$data = array("is_email_sended" => 1);
			$this->ci->AYN_Notif_historical_model->save($data, $notif_historical->id);
			
			if($notif_historical->module_level == "general"){
				
				$notif_general = $this->ci->AYN_Notif_general_model->get_one($notif_historical->id_notif_general);
				if($notif_general->email_notification){ // Si la configuración está seteada para enviar correo de notificación, se envía el correo a cada usuario
									
					$notif_historical_users = $this->ci->AYN_Notif_historical_users_model->get_all_where(array(
						"id_notif_historical" => $notif_historical->id,
						"deleted" => 0
					))->result();
					
					foreach($notif_historical_users as $historical_user){
						$id_user_action = $notif_historical->id_user;
						$id_user_to_notify = $historical_user->id_user;
						if($id_user_action != $id_user_to_notify){
							$send_email = $this->_send_email_notifications($notif_historical, $historical_user->id_user);
						}
					}
					/*
					if($send_email){
						$data = array("is_email_sended" => 1);
						$notif_historical = $this->ci->AYN_Notif_historical_model->save($data, $notif_historical->id);
					}
					*/
				}
				
			}
			
			if($notif_historical->module_level == "project"){
				
				$notif_general = $this->ci->AYN_Notif_projects_clients_model->get_one($notif_historical->id_notif_projects_clients);
				if($notif_general->email_notification){ // Si la configuración está seteada para enviar correo de notificación, se envía el correo a cada usuario
									
					$notif_historical_users = $this->ci->AYN_Notif_historical_users_model->get_all_where(array(
						"id_notif_historical" => $notif_historical->id,
						"deleted" => 0
					))->result();

					foreach($notif_historical_users as $historical_user){
						$id_user_action = $notif_historical->id_user;
						$id_user_to_notify = $historical_user->id_user;
						if($id_user_action != $id_user_to_notify){
							$send_email = $this->_send_email_notifications($notif_historical, $historical_user->id_user);
						}
					}
					/*
					if($send_email){
						$data = array("is_email_sended" => 1);
						$notif_historical = $this->ci->AYN_Notif_historical_model->save($data, $notif_historical->id);
					}
					*/
				}
			
			}
			
			if($notif_historical->module_level == "admin"){
				
				$notif_general = $this->ci->AYN_Notif_projects_admin_model->get_one($notif_historical->id_notif_projects_admin);
				if($notif_general->email_notification){ // Si la configuración está seteada para enviar correo de notificación, se envía el correo a cada usuario
									
					$notif_historical_users = $this->ci->AYN_Notif_historical_users_model->get_all_where(array(
						"id_notif_historical" => $notif_historical->id,
						"deleted" => 0
					))->result();

					foreach($notif_historical_users as $historical_user){
						$id_user_action = $notif_historical->id_user;
						$id_user_to_notify = $historical_user->id_user;
						if($id_user_action != $id_user_to_notify){
							$send_email = $this->_send_email_notifications($notif_historical, $historical_user->id_user);
						}
					}
					/*
					if($send_email){
						$data = array("is_email_sended" => 1);
						$notif_historical = $this->ci->AYN_Notif_historical_model->save($data, $notif_historical->id);
					}
					*/
				}
			
			}

		}
		
		// LLAMADA AL HISTÓRICO DE ALERTAS PARA ENVIAR CORREOS
		$ayn_alert_historical = $this->ci->AYN_Alert_historical_model->get_all_where(array(
			//"module_level" => "general",
			"is_email_sended" => 0,
			"deleted" => 0
		))->result();
		
		foreach($ayn_alert_historical as $alert_historical){
						
			$alert_config_historical = json_decode($alert_historical->alert_config, TRUE);
			$suma_elementos = $alert_config_historical["suma_elementos"];

			$alert_projects_config = $this->ci->AYN_Alert_projects_model->get_one($alert_historical->id_alert_projects);
			$alert_config_config = json_decode($alert_projects_config->alert_config, TRUE);
			$valor_riesgo = $alert_config_config["risk_value"];
			$valor_umbral = $alert_config_config["threshold_value"];
			
			//if($alert_projects_config->risk_email_alert && ($suma_elementos >= $valor_riesgo) && ($suma_elementos < $valor_umbral) )
			if($alert_projects_config->risk_email_alert || $alert_projects_config->threshold_email_alert){
					
				$alert_historical_users = $this->ci->AYN_Alert_historical_users_model->get_all_where(array(
					"id_alert_historical" => $alert_historical->id,
					"deleted" => 0
				))->result();
				
				if(count($alert_historical_users)){
					
					$data = array("is_email_sended" => 1);
					$this->ci->AYN_Alert_historical_model->save($data, $alert_historical->id);
					
					foreach($alert_historical_users as $alert_user){
						$send_email = $this->_send_email_alerts($alert_historical, $alert_projects_config, $alert_user->id_user);
					}
					
				}
				/*
				if($send_email){ // Si se envía el correo, se setea is_email_sended en 1
					$data = array("is_email_sended" => 1);
					$save_alert_historical = $this->ci->AYN_Alert_historical_model->save($data, $alert_historical->id);
				}
				*/
			}
			
		}

    }

	private function _send_email_notifications($notif_historical, $id_user_to_notify){
		
		// Se envía correo a los usuarios del histórico menos al usuario que realizó la acción
		$send_app_mail = FALSE;
		$module_level = $notif_historical->module_level;
		$id_user_action = $notif_historical->id_user;
		$event = $notif_historical->event;
		$id_element = $notif_historical->id_element;
		$user_action = $this->ci->Users_model->get_one($id_user_action);
		$user_to_notify = $this->ci->Users_model->get_one($id_user_to_notify);
		$notified_date = $notif_historical->notified_date;
		$massive = $notif_historical->massive;
			
		if($module_level == "general" && $id_user_action != $id_user_to_notify){
			
			$email_template = $this->ci->Email_templates_model->get_final_template("ayn_notification_general");
			$id_module = $notif_historical->id_client_context_module;
			$id_submodule = $notif_historical->id_client_context_submodule;
			$module_name = lang($this->ci->Client_context_modules_model->get_one($id_module)->contexto);
			$submodule_name = $this->ci->Client_context_modules_model->get_one($id_module)->name;
			
			$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
			$parser_data["USER_ACTION_NAME"] = $user_action->first_name." ".$user_action->last_name;
			$parser_data["MODULE_NAME"] = $module_name;
			$datetime_format = get_setting_client_mimasoft($notif_historical->id_client, 'date_format')." ".set_time_format_client($notif_historical->id_client);
			$parser_data["NOTIFIED_DATE"] = convert_date_utc_to_local_client_mimasoft($notified_date, $datetime_format, $notif_historical->id_client);
			$parser_data["SITE_URL"] = get_uri();
			$parser_data["CONTACT_URL"] = get_uri("contact");
			$parser_data_signature["SITE_URL"] = get_uri();
			$signature_message = $this->ci->parser->parse_string($email_template->signature, $parser_data_signature, TRUE);
			$parser_data["SIGNATURE"] = $signature_message;
				
			// Acuerdos Territorio - Beneficiarios: 2 | Acuerdos Distribución - Beneficiarios: 6
			if(($id_module == "2" || $id_module == "6") && $id_submodule == "0"){
				
				if($event == "add"){
					$event_message = lang("added_an_item");	
				} elseif($event == "edit"){
					$event_message = lang("edited_an_item");
				} elseif($event == "delete"){
					$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
				}
				
				$parser_data["EVENT"] = strtolower($event_message);
				$parser_data["SUBMODULE_NAME"] = $submodule_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
				
			}
			
			// Acuerdos Territorio - Actividades: 3 | Acuerdos Distribución - Actividades: 7
			if(($id_module == "3" || $id_module == "7")){
				
				if($event == "add"){
					$event_message = lang("added_an_item");
				} elseif($event == "edit"){
					$event_message = lang("edited_an_item");
				} elseif($event == "delete"){
					$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
				}

				$parser_data["EVENT"] = strtolower($event_message);
				$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("activities_record");
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);

				//echo $message;

			}
			
			// Acuerdos Territorio - Convenios y Donaciones: 4 | Acuerdos Distribución - Convenios y Donaciones: 8
			if($id_module == "4" || $id_module == "8"){
								
				// Pestaña Información
				if($event == "information_add" || $event == "information_edit" || $event == "information_delete" || $event == "information_audit"){ 
					
					if($event == "information_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "information_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "information_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					} elseif($event == "information_audit"){
						$event_message = lang("audited_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("agreements_record")." | ".lang("information");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Pestaña Configuración
				if($event == "configuration_add" || $event == "configuration_edit" || $event == "configuration_delete" || $event == "configuration_close"){
					
					if($event == "configuration_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "configuration_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "configuration_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					} elseif($event == "configuration_close"){
						$event_message = lang("closed_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					if($id_module == "4"){
						$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("agreements_record")." | ".lang("configuration");
					} elseif($id_module == "8"){
						$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("configuration");
					}
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
				
				}
				
				// Pestaña Registro de Ejecución
				if($event == "execution_record_add" || $event == "execution_record_edit" || $event == "execution_record_delete"){

					if($event == "execution_record_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "execution_record_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "execution_record_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("execution_record");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Pestaña Registro de Pago
				if($event == "payment_record_edit"){
					
					$event_message = lang("edited_an_item");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("payment_record");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}

			}			
			
			// Acuerdos Territorio - Mantenedoras: 5 |  Acuerdos Distribución - Mantenedoras: 9
			if($id_module == "5" || $id_module == "9"){
								
				// Mantenedoras Sociedades
				if($event == "society_add" || $event == "society_edit" || $event == "society_delete"){
					
					if($event == "society_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "society_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "society_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("societies");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Mantenedoras Centrales
				if($event == "central_add" || $event == "central_edit" || $event == "central_delete"){ 
					
					if($event == "central_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "central_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "central_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("centrals");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Mantenedoras Tipos de Acuerdo
				if($event == "type_of_agreement_add" || $event == "type_of_agreement_edit" || $event == "type_of_agreement_delete"){ 
					
					if($event == "type_of_agreement_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "type_of_agreement_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "type_of_agreement_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("types_of_agreement");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
								
			}
			
			// Ayuda y Soporte
			if($id_module == "1"){
				if($id_submodule == "4" && $event == "send_email"){ 
					
					$event_message = lang("sent_form");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = lang("contact");
					
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
			}
						
		}
		
		if($module_level == "project" && $id_user_action != $id_user_to_notify){
			
			$email_template = $this->ci->Email_templates_model->get_final_template("ayn_notification_projects_clients");
			$id_module = $notif_historical->id_client_module;
			$id_submodule = $notif_historical->id_client_submodule;
			$module_name = $this->ci->Clients_modules_model->get_one($id_module)->name;
			$submodule_name = $this->ci->Clients_submodules_model->get_one($id_submodule)->name;
			$project = $this->ci->Projects_model->get_one($notif_historical->id_project);
			
			$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
			$parser_data["USER_ACTION_NAME"] = $user_action->first_name." ".$user_action->last_name;
			
			if($id_submodule){
				$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
			} else {
				$parser_data["MODULE_NAME"] = $module_name;
			}
			
			$datetime_format = get_setting_mimasoft($notif_historical->id_project, "date_format")." ".set_time_format($notif_historical->id_project);
			$parser_data["NOTIFIED_DATE"] = convert_date_utc_to_local_mimasoft($notified_date, $datetime_format, $notif_historical->id_project);
			$parser_data["SITE_URL"] = get_uri();
			$parser_data["CONTACT_URL"] = get_uri("contact");
			$parser_data_signature["SITE_URL"] = get_uri();
			$signature_message = $this->ci->parser->parse_string($email_template->signature, $parser_data_signature, TRUE);
			$parser_data["SIGNATURE"] = $signature_message;
			
			// Registros Ambientales || Mantenedoras || Otros Registros
			if($id_module == "2" || $id_module == "3" || $id_module == "4"){
				
				
				if($id_module == "4" && ($event == "add_fixed_or" || $event == "edit_fixed_or" || $event == "delete_fixed_or")){
					$element = $this->ci->Fixed_form_values_model->get_one_where(array("id" => $id_element));
					$id_form = $element->id_formulario;
					$form_name = $this->ci->Forms_model->get_one($id_form)->nombre;
					if($event == "add_fixed_or"){
						$event = "add";
					}
					if($event == "edit_fixed_or"){
						$event = "edit";
					}
					if($event == "delete_fixed_or"){
						$event = "delete";
					}
				} else {
					$element = $this->ci->Form_values_model->get_one_where(array("id" => $id_element));
					$form_rel_project = $this->ci->Form_rel_project_model->get_one($element->id_formulario_rel_proyecto);
					$id_form = $form_rel_project->id_formulario;
					$form = $this->ci->Forms_model->get_one($id_form);
					$flujo = ($form->id_tipo_formulario == "1" && $form->flujo != "No Aplica") ? ' ('.$form->flujo.')' : "";
					$form_name = $this->ci->Forms_model->get_one($id_form)->nombre;
				}
				
				if($event == "add"){
					$event_message = lang("added_an_item");
				} elseif($event == "edit"){
					$event_message = lang("edited_an_item");
				} elseif($event == "delete"){
					$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
				}
				
				$parser_data["EVENT"] = strtolower($event_message);
				$parser_data["ELEMENT"] = " <strong>".$form_name."</strong>".$flujo;
				$parser_data["PROJECT_NAME"] = $project->title;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
				
			}
			
			// Compromisos
			if($id_module == "6"){
				
				// Evaluación de Compromisos RCA
				if($id_submodule == "4"){
					
					$element = $this->ci->Compromises_compliance_evaluation_rca_model->get_one_where(array("id" => $id_element));
					$valor_compromiso = $this->ci->Values_compromises_rca_model->get_one($element->id_valor_compromiso);
					
					if($event == "add"){
						$event_message = lang("added_an_evaluation");
					} elseif($event == "edit"){
						$event_message = lang("edited_an_evaluation");

					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $valor_compromiso->nombre_compromiso;
					$parser_data["PROJECT_NAME"] = $project->title;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Evaluación de Compromisos Reportables
				if($id_submodule == "22"){
					
					$element = $this->ci->Compromises_compliance_evaluation_reportables_model->get_one_where(array("id" => $id_element));
					$valor_compromiso = $this->ci->Values_compromises_reportables_model->get_one($element->id_valor_compromiso);
					$event_message = lang("edited_an_evaluation");
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $valor_compromiso->nombre_compromiso;
					$parser_data["PROJECT_NAME"] = $project->title;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
			}
			
			// Permisos
			if($id_module == "7"){
								
				$element = $this->ci->Permitting_procedure_evaluation_model->get_one_where(array("id" => $id_element));
				$valor_permiso = $this->ci->Values_permitting_model->get_one($element->id_valor_permiso);

				if($event == "add"){
					$event_message = lang("added_an_evaluation");
				} elseif($event == "edit"){
					$event_message = lang("edited_an_evaluation");
				}
				
				$parser_data["EVENT"] = strtolower($event_message);
				$parser_data["ELEMENT"] = $valor_permiso->nombre_permiso;
				$parser_data["PROJECT_NAME"] = $project->title;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
					
			}
			
			// Administración Cliente
			if($id_module == "11"){
				
				// Configuración Panel Principal
				if($id_submodule == "20"){
					
					$event_message = lang("edited_an_item");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $submodule_name;
					$parser_data["PROJECT_NAME"] = $project->title;
					$parser_data["MODULE_NAME"] = $module_name;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
				
				}
				
				// Carga Masiva
				if($id_submodule == "21"){
					
					$element = $this->ci->Forms_model->get_one($id_element);
					$form_name = $element->nombre;
					
					$event_message = lang("added_elements_massively");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $form_name;
					$parser_data["PROJECT_NAME"] = $project->title;
					$parser_data["MODULE_NAME"] = $module_name;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
				}
				
			}
			
			// Recordbook
			if($id_module == "12"){
				
				// Registros Recordbook
				if($id_submodule == "23"){
					
					$element = $this->ci->Recordbook_values_model->get_one_where(array("id" => $id_element));

					if($event == "add"){
						$event_message = lang("added_an_item");
					} elseif($event == "edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $submodule_name;
					$parser_data["PROJECT_NAME"] = $project->title;
					$parser_data["MODULE_NAME"] = $module_name;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);

					//echo $message;

				}
				
				// Seguimiento del recordbook
				if($id_submodule == "24"){
					
					$element = $this->ci->Recordbook_monitoring_model->get_one_where(array("id" => $id_element));
					$event_message = lang("edited_an_item");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $submodule_name;
					$parser_data["PROJECT_NAME"] = $project->title;
					$parser_data["MODULE_NAME"] = $module_name;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
			}

		}
		
		// Notificaciones módulos nivel admin
		if($module_level == "admin"){
			
			$email_template = $this->ci->Email_templates_model->get_final_template("ayn_notification_projects_admin");
			$id_module = $notif_historical->id_admin_module;
			$id_submodule = $notif_historical->id_admin_submodule;
			$module_name = $this->ci->AYN_Admin_modules_model->get_one($id_module)->name;
			$submodule_name = $this->ci->AYN_Admin_submodules_model->get_one($id_submodule)->name;
			$project = $this->ci->Projects_model->get_one($notif_historical->id_project);
			
			$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
			$parser_data["USER_ACTION_NAME"] = $user_action->first_name." ".$user_action->last_name;
			$parser_data["MODULE_NAME"] = $module_name;
						
			$parser_data["NOTIFIED_DATE"] = format_to_datetime($notified_date);
			$parser_data["SITE_URL"] = get_uri();
			$parser_data["CONTACT_URL"] = get_uri("contact");
			$parser_data_signature["SITE_URL"] = get_uri();
			$signature_message = $this->ci->parser->parse_string($email_template->signature, $parser_data_signature, TRUE);
			$parser_data["SIGNATURE"] = $signature_message;
			
			// Proyectos
			if($id_module == "4"){
				
				$element = $this->ci->Projects_model->get_one($id_element);
				
				$event_message = strtolower(lang("edited_the_item"));
				if($event == "project_edit_name"){
					$event_message .= ' '.lang("project_name");
				} elseif($event == "project_edit_auth_amb"){
					$event_message .= ' '.lang("environmental_authorization");
				} elseif($event == "project_edit_start_date"){
					$event_message .= ' '.lang("start_date");
				} elseif($event == "project_edit_end_date"){
					$event_message .= ' '.lang("term_date");
				} elseif($event == "project_edit_members"){
					$event_message .= ' '.lang("members");
				} elseif($event == "project_edit_desc"){
					$event_message .= ' '.lang("description");
				} elseif($event == "project_edit_status"){
					$event_message .= ' '.lang("status");
				} elseif($event == "project_edit_pu"){
					$event_message .= ' '.lang("unit_processes");
				} elseif($event == "project_edit_cat_impact"){
					$event_message .= ' '.lang("footprints");
				}

				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $element->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;

			}
			
			// Registros
			if($id_module == "5"){
				
				$element = $this->ci->Forms_model->get_one_where(array("id" => $id_element));
				$form_name = $element->nombre;
								
				if($event == "form_add"){
					$event_message = strtolower(lang("added_the_form"))." ".$form_name;
				} elseif($event == "form_edit_name"){
					$event_message = strtolower(lang("edited_the_item"))." ".lang("name")." ".lang("in")." ".lang("form")." ".$form_name;
				} elseif($event == "form_edit_cat"){
					$event_message = strtolower(lang("edited_the_item"))." ".lang("category")." ".lang("in")." ".lang("form")." ".$form_name;
				} elseif($event == "form_delete"){
					$event_message = strtolower(lang("deleted_an_item"))." ".lang("in")." ".$submodule_name;
					$event_message = ($massive) ? strtolower(lang("deleted_items_massively"))." ".lang("in")." ".$submodule_name : strtolower(lang("deleted_an_item"))." ".lang("in")." ".$submodule_name;
				}
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $project->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
				
			}
			
			// Indicadores
			if($id_module == "7"){
				
				$element = $this->ci->Functional_units_model->get_one_where(array("id" => $id_element));
								
				if($event == "uf_add_element"){
					$event_message = strtolower(lang("added_an_item"))." ".lang("in")." ".$submodule_name;
				} elseif($event == "uf_edit_element"){
					$event_message = strtolower(lang("edited_an_item"))." ".lang("in")." ".$submodule_name;
				} elseif($event == "uf_delete_element"){
					$event_message = ($massive) ? strtolower(lang("deleted_items_massively"))." ".lang("in")." ".$submodule_name : strtolower(lang("deleted_an_item"))." ".lang("in")." ".$submodule_name;
				}
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $project->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
				
			}
			
			// Compromisos
			if($id_module == "8"){

				if($event == "comp_rca_add"){
					$element = $this->ci->Values_compromises_rca_model->get_one($id_element);
					$event_message = strtolower(lang("added_an_item"))." ".lang("in")." ".lang("compromises_rca");
				}
				
				if($event == "comp_rep_add"){
					$element = $this->ci->Values_compromises_reportables_model->get_one($id_element);
					$event_message = strtolower(lang("added_an_item"))." ".lang("in")." ".lang("compromises_rep");
				}
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $project->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;

			}
			
			// Permisos
			if($id_module == "9"){
				
				if($event == "permitting_add"){
					$element = $this->ci->Values_permitting_model->get_one($id_element);
					$event_message = strtolower(lang("added_an_item"))." ".lang("in")." ".lang("permittings");
				}
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $project->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;

			}
			
		}
		
		return $send_app_mail;
		
	}
	
	private function _send_email_alerts($alert_historical, $alert_project_config, $id_user_to_alert){
		
		// Se envía correo a los usuarios del histórico menos al usuario que realizó la acción
		$send_app_mail = FALSE;
		$id_user_action = $alert_historical->id_user;
		$event = $alert_historical->event;
		$id_element = $alert_historical->id_element;
		$user_to_alert = $this->ci->Users_model->get_one($id_user_to_alert);
		$alert_date = format_to_datetime($alert_historical->alert_date);
		
		$email_template = $this->ci->Email_templates_model->get_final_template("ayn_alerts_admin");
		
		$id_module = $alert_historical->id_client_module;
		$id_submodule = $alert_historical->id_client_submodule;
		$module_name = $this->ci->Clients_modules_model->get_one($id_module)->name;
		$submodule_name = $this->ci->Clients_submodules_model->get_one($id_submodule)->name;
		$project = $this->ci->Projects_model->get_one($alert_historical->id_project);
		
		$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_alert->first_name." ".$user_to_alert->last_name;
		$parser_data["MODULE_NAME"] = $module_name;
		$parser_data["ALERT_DATE"] = $alert_date;
		$parser_data["PROJECT_NAME"] = $project->title;
		$parser_data["SITE_URL"] = get_uri();
		$parser_data["CONTACT_URL"] = get_uri("contact");
		$parser_data_signature["SITE_URL"] = get_uri();
		$signature_message = $this->ci->parser->parse_string($email_template->signature, $parser_data_signature, TRUE);
		$parser_data["SIGNATURE"] = $signature_message;
		
		$alerted_users = $this->ci->AYN_Alert_historical_users_model->get_all_where(array(
			"id_alert_historical" => $alert_historical->id,
			"deleted" => 0
		))->result();
		$html_alerted_users = "";
		foreach($alerted_users as $alerted_user){
			$user = $this->ci->Users_model->get_one($alerted_user->id_user);
			$user_name = $user->first_name." ".$user->last_name;
			$image_url = get_avatar($user->image);
			$avatar = anchor(get_uri("project_info/view_user_profile/".$user->id."/".$project->id), "<span style='width: 20px; height: 20px; display: inline-block; white-space: nowrap; margin-right: 10px;'><img width='20' height='20' src='$image_url' alt='...' style='height: auto; max-width: 100%; border-radius: 50%; -webkit-border-radius: 10px; -moz-border-radius: 10px;'></span>$user_name", array("title" => ""));
			$html_alerted_users .= $avatar;
			if(next($alerted_users)){
				$html_alerted_users .= "<br><br>";
			}
		}
		
		$parser_data["ALERTED_USERS"] = $html_alerted_users;

		
		// Registros Ambientales
		if($id_module == "2"){
			
			$element = $this->ci->Form_values_model->get_one_where(array("id" => $id_element));
			$form_rel_project = $this->ci->Form_rel_project_model->get_one($element->id_formulario_rel_proyecto);
			$id_form = $form_rel_project->id_formulario;
			$form_name = $this->ci->Forms_model->get_one($id_form)->nombre;
						
			$alert_config = json_decode($alert_historical->alert_config, TRUE);
			$categoria = $this->ci->Categories_model->get_one($alert_config["id_categoria"]);
			$alias_categoria = $this->ci->Categories_alias_model->get_one_where(array(
				"id_cliente" => $alert_historical->id_client,
				"id_categoria" => $categoria->id,
				"deleted" => 0
			));
			$nombre_categoria = ($alias_categoria->id) ? $alias_categoria->alias : $categoria->nombre;
			
			$unidad = $this->ci->Unity_model->get_one($alert_config["id_unidad"])->nombre;
			
			$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
			$valor_riesgo = $alert_config_project_field["risk_value"];
			$valor_umbral = $alert_config_project_field["threshold_value"];
			$suma_elementos = $alert_config["suma_elementos"];
									
			if( ($suma_elementos >= $valor_riesgo) && ($suma_elementos < $valor_umbral) ){
				
				$event_message = strtolower(lang("the_umbral_of"))." ".$nombre_categoria." - ".$valor_umbral." ".$unidad." ".lang("in")." ".lang("record")." ".$form_name." ".lang("is_close_to_being_exceeded");				
				$parser_data["MESSAGE_TYPE"] = lang("caution");
				$parser_data["EVENT"] = $event_message;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
				//echo $message;
	
			}
			
			if($suma_elementos >= $valor_umbral){
				
				$event_message = strtolower(lang("the_umbral_of"))." ".$nombre_categoria." - ".$valor_umbral." ".$unidad." ".lang("in")." ".lang("record")." ".$form_name." ".lang("has_been_exceeded");				
				$parser_data["MESSAGE_TYPE"] = lang("alert");
				$parser_data["EVENT"] = $event_message;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
				//echo $message;

			}
			
		}
		
		if($id_module == "6"){ // Compromisos
			
			if($id_submodule == "4"){ // Evaluación de Compromisos RCA

				$alert_config = json_decode($alert_historical->alert_config, TRUE);
				$nombre_compromiso = $this->ci->Values_compromises_rca_model->get_one($alert_config["id_valor_compromiso"])->nombre_compromiso;
				
				$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
				$valor_riesgo = $alert_config_project_field["risk_value"];
				$valor_umbral = $alert_config_project_field["threshold_value"];
				$id_estado_evaluacion = $alert_config["id_estado_evaluacion"];
				$estado_evaluacion = $this->ci->Compromises_compliance_status_model->get_one($id_estado_evaluacion)->nombre_estado;
								
				$event_message = strtolower(lang("an_evaluation_has_been_entered"))." - ".$estado_evaluacion." - ".lang("in")." ".$nombre_compromiso;
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
				if($id_estado_evaluacion == $valor_riesgo){
					$parser_data["MESSAGE_TYPE"] = lang("caution");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
					//echo $message;
				} elseif($id_estado_evaluacion == $valor_umbral) {
					$parser_data["MESSAGE_TYPE"] = lang("alert");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
					//echo $message;
				}
				
			}
			
			if($id_submodule == "22"){ // Evaluación de Compromisos Reportables
								
				$alert_config = json_decode($alert_historical->alert_config, TRUE);
				
				/*
					Se debe generar una alerta X días antes de la planificación (riesgo) si no se ha ingresado fecha de ejecución.
					Se debe generar una alerta X días después de la planificación (umbral) si no se ha ingresado fecha de ejecución.
				*/
				if($alert_config["id_planificacion"]){ // Planificación
					
					$planificacion = $this->ci->Plans_reportables_compromises_model->get_one($alert_config["id_planificacion"]);
					$valor_compromiso_reportable = $this->ci->Values_compromises_reportables_model->get_one($planificacion->id_compromiso)->nombre_compromiso;
					$fecha_actual = get_current_utc_time("Y-m-d");
					$fecha_planificacion = $planificacion->planificacion;
					$evaluacion = $this->ci->Compromises_compliance_evaluation_reportables_model->get_one_where(array(
						"id_planificacion" => $planificacion->id,
						"deleted" => 0
					));
					
					if(!$evaluacion->modified_by){ // Si la evaluación no se ha editado, se genera la alerta
						
						$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
						$valor_riesgo = $alert_config_project_field["risk_value"];
						$valor_umbral = $alert_config_project_field["threshold_value"];
						
						$dif_dias = strtotime($fecha_planificacion) - strtotime($fecha_actual);
						$dif_dias = round($dif_dias / (60 * 60 * 24));
													
						if($dif_dias >= 0){ // Si fecha actual es menor o igual a fecha de planificacion (dentro del plazo)
							
							if($valor_riesgo >= $dif_dias){

								$cantidad_dias = $valor_riesgo;
								$event_message = strtolower(lang("according_to_the_planning"))." ".$planificacion->descripcion.", ".lang("in")." ".$cantidad_dias." ".lang("days")." ".lang("it_must_be_reported")." ".$valor_compromiso_reportable." (".format_to_date($fecha_planificacion, false).")";
								$parser_data["MESSAGE_TYPE"] = lang("reminder_caution");
								$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
								$parser_data["EVENT"] = $event_message;
								$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
								$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
								//echo $message;
								
							}
							
						} else { // fuera del plazo
							
							$dif_dias = $dif_dias * -1;
							if($valor_umbral <= $dif_dias){
								
								$cantidad_dias = $valor_umbral;
								$event_message = strtolower(lang("according_to_the_planning"))." ".$planificacion->descripcion.", ".lang("in")." ".$cantidad_dias." ".lang("days")." ".lang("it_should_have_been_reported")." ".$valor_compromiso_reportable." (".format_to_date($fecha_planificacion, false).")";
								$parser_data["MESSAGE_TYPE"] = lang("reminder_alert");
								$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
								$parser_data["EVENT"] = $event_message;
								$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
								$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
								//echo $message;
								
							}
						
						}

					}
					
				} else {
										
					$nombre_compromiso = $this->ci->Values_compromises_reportables_model->get_one($alert_config["id_valor_compromiso"])->nombre_compromiso;
					$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
					$valor_riesgo = $alert_config_project_field["risk_value"];
					$valor_umbral = $alert_config_project_field["threshold_value"];
					$id_estado_evaluacion = $alert_config["id_estado_evaluacion"];
					$estado_evaluacion = $this->ci->Compromises_compliance_status_model->get_one($id_estado_evaluacion)->nombre_estado;
					
					$event_message = strtolower(lang("an_evaluation_has_been_entered"))." - ".$estado_evaluacion." - ".lang("in")." ".$nombre_compromiso;
					
					$parser_data["EVENT"] = $event_message;
					$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
					if($id_estado_evaluacion == $valor_riesgo){
						$parser_data["MESSAGE_TYPE"] = lang("caution");
						$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
						$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
						//echo $message;
					} elseif($id_estado_evaluacion == $valor_umbral) {
						$parser_data["MESSAGE_TYPE"] = lang("alert");
						$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
						$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
						//echo $message;
					}
					
				}
				
			}
			
		}
		
		if($id_module == "7"){ // Permisos
		
			$alert_config = json_decode($alert_historical->alert_config, TRUE);
			$nombre_permiso = $this->ci->Values_permitting_model->get_one($alert_config["id_valor_permiso"])->nombre_permiso;

			$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
			$valor_riesgo = $alert_config_project_field["risk_value"];
			$valor_umbral = $alert_config_project_field["threshold_value"];
			$id_estado_evaluacion = $alert_config["id_estado_evaluacion"];
			$estado_evaluacion = $this->ci->Permitting_procedure_status_model->get_one($id_estado_evaluacion)->nombre_estado;
			
			$event_message = strtolower(lang("an_evaluation_has_been_entered"))." - ".$estado_evaluacion." - ".lang("in")." ".$nombre_permiso;
			
			$parser_data["EVENT"] = $event_message;
			$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
			if($id_estado_evaluacion == $valor_riesgo){
				$parser_data["MESSAGE_TYPE"] = lang("caution");
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
				//echo $message;
			} elseif($id_estado_evaluacion == $valor_umbral) {
				$parser_data["MESSAGE_TYPE"] = lang("alert");
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
				//echo $message;
			}
			
		}
		
		if($id_module == "12"){ // Recordbook
			
			$valor_recordbook = $this->ci->Recordbook_values_model->get_one($id_element);
			$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
			$valor_riesgo = $alert_config_project_field["risk_value"];
			$valor_umbral = $alert_config_project_field["threshold_value"];

			$event_message = strtolower(lang("a_recordbook_record_has_been_entered"))." - ".$valor_recordbook->nombre." - ".lang("with")." ".lang($valor_recordbook->proposito_visita)." ".lang("as_visit_purpose");
			
			$parser_data["EVENT"] = $event_message;
			$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
			if($valor_recordbook->proposito_visita == $valor_riesgo){
				$parser_data["MESSAGE_TYPE"] = lang("caution");
			} elseif($valor_recordbook->proposito_visita == $valor_umbral) {
				$parser_data["MESSAGE_TYPE"] = lang("alert");
			}
			$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
			$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
			//echo $message;
										
		}
		
		return $send_app_mail;
		
	}
	
}
