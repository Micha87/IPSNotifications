<?php

	class NotificationControl extends IPSModule {
		
		public function Create()
		{
			//Never delete this line!
			parent::Create();
                        //$log = $this->CreateVariableByIdent($this->InstanceID, "Log", "Ereignisse", 3, "~String");
                        
		

                        // Create 'Log' Switch and activate logging
                        $vid = @IPS_GetObjectIDByIdent("Log", $this->InstanceID);  
                        if($vid === false){
                                $vid = IPS_CreateVariable(3);
                                IPS_SetParent($vid, $this->InstanceID);
                               IPS_SetName($vid, "Ereignisse");
                                IPS_SetIdent($vid, "Log");
                                IPS_SetVariableCustomProfile($vid, "~String");
                                IPS_SetIcon( $vid, 'Database');
                                $archive = IPS_GetInstanceListByModuleID("{43192F0B-135B-4CE7-A0A7-1475603F3060}");
                                AC_SetLoggingStatus($archive[0], $vid, true);
                                IPS_ApplyChanges($archive[0]);
                        }   
                        
                        // Create 'Active' Switch and set is TRUE
                        $vid = @IPS_GetObjectIDByIdent("Active", $this->InstanceID); 
                        if($vid === false){
                                $vid = IPS_CreateVariable(0);
                                IPS_SetParent($vid, $this->InstanceID);
                                IPS_SetName($vid, "Benachrichtigungen senden");
                                IPS_SetIdent($vid, "Active");
                                IPS_SetVariableCustomProfile($vid, "~Switch");
                                SetValue($vid, true);
                        }                        
                        $this->EnableAction("Active");			
		}
                
		public function ApplyChanges() {
			//Never delete this line!
			parent::ApplyChanges();
			
		}
                
                
                public function ForwardData($JSONString) {

                    // Empfangene Daten von der Device Instanz
                    $data = json_decode($JSONString);
                    $result = false;
                    
                    SetValueString( $this->GetIDforIdent('Log'), ' | '.$data->Buffer->Title.' | '.$data->Buffer->Text);
                    //IPS_LogMessage("NotificationControl", print_r($data->Buffer, true));
                    if (GetValue($this->GetIDforIdent('Active'))){
                        // Data from Statusmeldung
                        if ($data->DataID == "{858A61A3-F139-4A08-97EB-B7F2BCA842B4}"){  
                            
                            // Weiterleiten an Abonnement
                            $result = $this->SendDataToChildren(json_encode(Array("DataID" => "{F79D2B31-49A9-498B-A26C-22CAB56C6852}", "Buffer" => $data->Buffer)));                        
                        }
                    }
                    // Weiterverarbeiten und durchreichen
                    return $result;

                }

                public function SetActive(bool $value) {
                    SetValue($this->GetIDforIdent('Active'), $value);
                }                
               
                public function RequestAction($Ident, $Value) {

                    switch($Ident) {
                        case "Active":
                            SetValue($this->GetIDForIdent($Ident), $Value);
                            break;
                        default:
                            throw new Exception("Invalid Ident");
                    }

                }

		private function CreateVariableByIdent($id, $ident, $name, $type, $profile = "") {
			
			 $vid = @IPS_GetObjectIDByIdent($ident, $id);
			 if($vid === false)
			 {
				 $vid = IPS_CreateVariable($type);
				 IPS_SetParent($vid, $id);
				 IPS_SetName($vid, $name);
				 IPS_SetIdent($vid, $ident);
				 if($profile != "")
					IPS_SetVariableCustomProfile($vid, $profile);
			 }
			 return $vid;
		}
                
  
	}

?>
