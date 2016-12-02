<?php

	class Subscription extends IPSModule {
		
		public function Create()
		{
			//Never delete this line!
			parent::Create();

                        $this->RegisterPropertyInteger("Type", -1);
                        $this->RegisterPropertyInteger("WebFrontID", 0);
                        $this->RegisterPropertyInteger("WebFrontTimeout", 0);
                        $this->RegisterPropertyInteger("SMTPID", 0);
                        $this->RegisterPropertyString("MailTo", '');
                        
                        $this->CreateVariableProfiles();
                        $this->CreateVariableByIdent($this->InstanceID, "Active", "Benachrichtigungen senden", 0, "~Switch", 0);
                        //$this->CreateVariableByIdent($this->InstanceID, "Update", "Subscriptions", 1, "Subscription.Update", 1);
                        //$this->CreateVariableByIdent($this->InstanceID, "Subscriptions", "Nachrichten Abonnieren", 1, "Subscription.Active");                       
                        //$this->EnableAction("Update");
                        $this->EnableAction("Active");
                        
			
		}
                
		public function ApplyChanges() {
			//Never delete this line!
			parent::ApplyChanges();
                        // Connect to NotificationController
                        $this->ConnectParent("{F1AF3F8A-4420-476A-9DF0-B3B2EAC5154D}");
                        $this->UpdateSubscriptions();

			
		}
               

                public function UpdateSubscriptions() {
                    $instances = IPS_GetInstanceListByModuleID("{DE9F7760-C60E-4138-B38A-11801F8878F7}");
                    $abonnements = IPS_GetChildrenIDs($this->InstanceID);
                    foreach($instances as $instance){                       
                        $aboID = $this->CreateVariableByIdent($this->InstanceID, 'Source'.$instance, IPS_GetName($instance), 0, "Subscription.Active", 10);
                        IPS_SetName($aboID, IPS_GetName($instance));
                        $this->EnableAction('Source'.$instance);
                    }                    
                    // Nicht mehr vorhandene Statusmeldungen löschen
                    foreach ($abonnements as $aboID) {
                        $ident = IPS_GetObject($aboID)['ObjectIdent'];
                        if ( strncmp($ident, 'Source', strlen('Source') ) === 0 ){
                            $instanceID = (int)substr($ident, strlen('Source'));
                            if (!in_array($instanceID, $instances) ){
                                IPS_DeleteVariable($aboID);
                            }
                        }
                    }
                }                
                
                public function RequestAction($Ident, $Value) {

                    switch($Ident) {
                        case "Update":
                            SetValue($this->GetIDForIdent($Ident), $Value);
                            $this->UpdateSubscriptions();
                            break;                        
                        case "Active":
                            SetValue($this->GetIDForIdent($Ident), $Value);
                            $this->UpdateSubscriptions();
                            break;                         
                        default:
                            SetValue($this->GetIDForIdent($Ident), $Value);
                            $this->UpdateSubscriptions();
                    }

                }
                public function SendNotification(){
                    
                }
                public function ReceiveData($JSONString) {

                    // Empfangene Daten von der Device Instanz
                    $data = json_decode($JSONString);
                    $title = utf8_decode($data->Buffer->Title);
                    $text = utf8_decode($data->Buffer->Text);
                    $sound = '';
                    $mediaID = $data->Buffer->MediaID;
                    $icon = $data->Buffer->Icon;
                    //IPS_LogMessage("Subscription", print_r($data->Buffer, true));
                    
                    $this->UpdateSubscriptions();
                    $active = GetValue($this->GetIDforIdent('Active'));
                    
                    $aboID = $this->GetIDforIdent('Source'.$data->Buffer->InstanceID);
                    if (!$aboID){
                        return false;
                    }
                    if ($active && GetValue($aboID)){
                        switch ($this->ReadPropertyInteger('Type')) {
                            case 0:
                                $result = SMTP_SendMailEx($this->ReadPropertyInteger('SMTPID'), $this->ReadPropertyString('MailTo'), $title, $text);
                                break;
                            case 1:
                                $result = WFC_PushNotification($this->ReadPropertyInteger('WebFrontID'), $title, $text, $sound, 0); //Sound: String, TargetID: Integer
                                break;
                            case 2:
                                $result = WFC_SendNotification($this->ReadPropertyInteger('WebFrontID'), $title, $text, $icon, $this->ReadPropertyString('WebFrontTimeout'));
                                break;
                            case 3:
                                $result = WFC_AudioNotification($this->ReadPropertyInteger('WebFrontID'), $title, $mediaID);
                                break;
                            case 4:
                                $result = IPS_RunScriptEx ( $this->ReadPropertyInteger('ScriptID'), $data);
                                break;

                        }
                    } else {
                        $result = false;
                    }
                    return $result;

                }
                
                private function CreateVariableProfiles() {
                    if (!IPS_VariableProfileExists('Subscription.Active')){
                        IPS_CreateVariableProfile('Subscription.Active', 0);
                        IPS_SetVariableProfileAssociation('Subscription.Active', 0, "inaktiv", "Mail", 0x000000);
                        IPS_SetVariableProfileAssociation('Subscription.Active', 1, "abonniert", "Mail", 0x00ff00);
                    }
                    
                    if (!IPS_VariableProfileExists('Subscription.Update')){
                        IPS_CreateVariableProfile('Subscription.Update', 1);
                        IPS_SetVariableProfileAssociation( 'Subscription.Update', 0, 'Aktualisieren', "Database", 0x000000);
                    }                    
                }
                
		private function CreateVariableByIdent($id, $ident, $name, $type, $profile = "", $position = 0) {
			
			 $vid = @IPS_GetObjectIDByIdent($ident, $id);
			 if($vid === false)
			 {
				 $vid = IPS_CreateVariable($type);
				 IPS_SetParent($vid, $id);
				 IPS_SetName($vid, $name);
				 IPS_SetIdent($vid, $ident);
                                 IPS_SetPosition($vid, $position);
				 if($profile != "")
					IPS_SetVariableCustomProfile($vid, $profile);
			 }
			 return $vid;
		}

                private function CreateLinkByIdent($id, $ident, $name, $target, $profile = "") {
			
			 $LinkID = @IPS_GetObjectIDByIdent($ident, $id);
			 if($LinkID === false)
			 {
				 $LinkID = IPS_CreateLink();
				 IPS_SetParent($LinkID, $id);
				 IPS_SetName($LinkID, $name);
				 IPS_SetIdent($LinkID, $ident);
                                 IPS_SetLinkTargetID($LinkID, $target);
				 if($profile != "")
					IPS_SetVariableCustomProfile($LinkID, $profile);
			 }
			 return $LinkID;
		}
                
                public function GetConfigurationForm() {
                        $element['type'] = 'Select';
                        $element['name'] = 'Type';
                        $element['caption'] = 'Typ';
                        $element['options'] = [
                            array( 'label' => '[Typ auswählen]', 'value' => -1),
                            array( 'label' => 'Email', 'value' => 0),
                            array( 'label' => 'Push Notification', 'value' => 1),
                            array( 'label' => 'WebFront Notification', 'value' => 2),
                            array( 'label' => 'WebFront Audionotification', 'value' => 3),
                            array( 'label' => 'Skript', 'value' => 4)
                        ];
                        $form['elements'][] = $element;
                        
                        switch ($this->ReadPropertyInteger("Type")){
                            case -1:
                                $form['elements'][] = array( 'type' => 'Label', 'label' => 'Typ auswählen und "übernehmen" klicken, um fortzufahren.' );
                                break;
                            case 0:
                                $form['elements'][] = array( 'type' => 'SelectInstance', 'name' => 'SMTPID', 'caption' => 'Absender (SMTP)' );
                                $form['elements'][] = array( 'type' => 'ValidationTextBox', 'name' => 'MailTo', 'caption' => 'Empfänger Andresse' );
                                break;
                            
                            case 1:
                                $form['elements'][] = array( 'type' => 'SelectInstance', 'name' => 'WebFrontID', 'caption' => 'WebFront Konfigurator' );
                                break;
                            case 2:
                                $form['elements'][] = array( 'type' => 'SelectInstance', 'name' => 'WebFrontID', 'caption' => 'WebFront Konfigurator' );
                                $form['elements'][] = array( 'type' => 'NumberSpinner', 'name' => 'WebFrontTimeout', 'caption' => 'Timeout [s]' );
                                break;
                            case 3:
                                $form['elements'][] = array( 'type' => 'SelectInstance', 'name' => 'WebFrontID', 'caption' => 'WebFront Konfigurator' );
                                break;
                            case 4:
                                $form['elements'][] = array( 'type' => 'SelectScript', 'name' => 'ScriptID', 'caption' => 'Sende Skript' );
                                break;                            
                        }
                        $form['actions'][] = array('type' => 'Button', 'label' => 'Update Subscriptions', 'onClick' => 'SUBSCR_UpdateSubscriptions($id);' );
                        return json_encode($form);
                }                

	}

?>
