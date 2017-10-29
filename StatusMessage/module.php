<?php

	class StatusMessage extends IPSModule {
		
		public function Create()
		{
			//Never delete this line!
			parent::Create();
                        
                        $this->RegisterPropertyInteger("Type", -1);
                        $this->RegisterPropertyInteger("ParentID", 0);
                        $this->RegisterPropertyInteger("VariableID", 0);
                        $this->RegisterPropertyString("VariableProfile", '');
                        $this->RegisterPropertyString("Operator", '');
                        $this->RegisterPropertyBoolean("ConditionValueBoolean", false);
                        $this->RegisterPropertyInteger("ConditionValueInteger", 0);
                        $this->RegisterPropertyFloat("ConditionValueFloat", 0);
                        $this->RegisterPropertyString("ConditionValueString", '');
                        $this->RegisterPropertyBoolean("Hide", true);
                        $this->RegisterPropertyBoolean("NotifyConditionTrue", true);
                        $this->RegisterPropertyBoolean("NotifyConditionFalse", false);
                        $this->RegisterPropertyString("Title", '');
                        $this->RegisterPropertyString("Icon", '');
                        $this->RegisterPropertyInteger("MediaID", 0);
                        $this->RegisterTimer("Update", 24*60*60*1000, "STATEMSG_Update();");
                        $this->Update();
			
		}
                
		public function ApplyChanges() {
			//Never delete this line!
			parent::ApplyChanges();

                        $this->ConnectParent ("{F1AF3F8A-4420-476A-9DF0-B3B2EAC5154D}");
                        $this->Update();
		}


		public function Update() {
			
                    $parentID = $this->ReadPropertyInteger("ParentID");
                    $variableProfile = $this->ReadPropertyString("VariableProfile");

                    // Get all variables with given profile
                    $variableList = $this->GetVariableList();
                    // Remove old sensors
                    foreach(IPS_GetChildrenIDs($this->InstanceID) as $childID) {
                        $ident = IPS_GetObject($childID)['ObjectIdent'];
                        if (strncmp($ident, 'Trigger', strlen('Trigger')) === 0){
                            if (!in_array( substr($ident, strlen('Trigger')), $variableList)){
                                IPS_DeleteEvent($childID);
                            }
                        }
                        if (strncmp($ident, 'Source', strlen('Source')) === 0){
                            if (!in_array( substr($ident, strlen('Source')), $variableList)){
                                IPS_DeleteLink($childID);
                            }
                        }                        
                    }
                    
                    // Create links and triggers for watched variables
                    foreach ( $variableList as $varID ){
                        $name = str_replace('\\', ' - ', IPS_GetLocation($varID));
                        $LinkID = $this->CreateLinkByIdent($this->InstanceID, "Source".$varID, $name, $varID);
                        IPS_SetName($LinkID, $name);
                        if(@IPS_GetObjectIDByIdent("Trigger".$varID, $this->InstanceID) === false) {
                                        $eid = IPS_CreateEvent(0 /* Trigger */);
                                        IPS_SetParent($eid, $this->InstanceID);
                                        IPS_SetName($eid, "Trigger for #".$varID);
                                        IPS_SetIdent($eid, "Trigger".$varID);
                                        IPS_SetEventTrigger($eid, 1, $varID);
                                        IPS_SetEventScript($eid, "STATEMSG_VariableChanged(\$_IPS['TARGET'], \$_IPS['VARIABLE'], \$_IPS['VALUE'], \$_IPS['OLDVALUE']);");
                                        IPS_SetEventActive($eid, true);
                                        IPS_SetHidden($eid, true);
                        }
                        $state = $this->CheckCondition(GetValue($varID), $varID);
                        $this->HideLinks($varID, $state);
                    }
		}

                public function GetVariableList(){ 
                    $variableList = array();
                    switch ($this->ReadPropertyInteger("Type")){
                        case 0:
                            $variableProfile = $this->ReadPropertyString("VariableProfile");
                        
                            if ($variableProfile === "~Window"){
                                $variableProfiles = array("~Window", "~Window.HM", "~Window.Hoppe", "~Window.Reversed");
                            } else {
                                $variableProfiles = array($variableProfile);
                            }
                            if ( !IPS_VariableProfileExists($variableProfile)) return array();
                            
                            $parentID = $this->ReadPropertyInteger("ParentID");
                            foreach ( IPS_GetVariableList() as $varID ){
                                if (in_array( $this->GetProfileName( $varID ), $variableProfiles)){
                                    if (IPS_IsChild($varID, $parentID, true)){
                                            $variableList[] = $varID;      
                                    }
                                }
                            }
                            break;
                            
                        case 1:
                            if ( $this->ReadPropertyInteger("VariableID") > 0){
                                $variableList[] = $this->ReadPropertyInteger("VariableID");
                            }
                            break;
                    }
                    return $variableList;
                }
                
                private function HideLinks( int $varID, bool $state){
                    $LinkID = @IPS_GetObjectIDByIdent('Source'.$varID, $this->InstanceID);
                    if ( !$state && $this->ReadPropertyBoolean("Hide") ){
                        IPS_SetHidden($LinkID, true);
                    } else {
                        IPS_SetHidden($LinkID, false);
                    }                    
                }
          
                
                private function CheckCondition( $SourceValue, $SourceID ){
                    if ( in_array($this->GetProfileName($SourceID), array("~Window.HM", "~Window.Hoppe")) ){
                        $SourceValue = $SourceValue > 0;
                        //var_dump($SourceValue);
                    }
                    if (in_array($this->GetProfileName($SourceID), array("~Window.Reversed"))){
                        $SourceValue = !$SourceValue;
                    }
                    
                    switch ($this->GetVariableType()){
                        case 0:
                            $condition = $this->ReadPropertyBoolean("ConditionValueBoolean");
                            break;
                        case 1:
                            $condition = $this->ReadPropertyInteger("ConditionValueInteger");
                            break;
                        case 2:
                            $condition = $this->ReadPropertyFloat("ConditionValueFloat");
                            break;
                        case 3:
                            $condition = $this->ReadPropertyString("ConditionValueString");
                            break;                        
                    }
                    
                    switch ($this->ReadPropertyString("Operator")){
                        case "==":
                            $result = ($SourceValue == $condition);
                            break;
                        case "!=":
                            $result = ($SourceValue != $condition);
                            break;
                        case ">":
                            $result = ($SourceValue > $condition);
                            break;                        
                        case "<":
                            $result = ($SourceValue < $condition);
                            break; 
                        case "isequal":
                            $result = strcmp($SourceValue, $condition);
                            break; 
                        case "contains":
                            $result = stripos( $SourceValue, $condition) !== false;
                            break; 
                        case "start":
                            $result = strncmp($SourceValue, $condition, strlen($condition)) === 0;
                            break;
                        case "end":
                            $result = strcasecmp(substr($SourceValue, strlen($SourceValue) - strlen($condition)), $condition) === 0;
                            break;
                        default:
                            $result = false;
                    }
                    return $result;
                }
                
                public function VariableChanged(int $SourceID, $SourceValue, $SourceOldValue){
                    $state = $this->CheckCondition( $SourceValue, $SourceID );
                    $oldState = $this->CheckCondition( $SourceOldValue, $SourceID );
                    $this->Update();
                    $this->HideLinks($SourceID, $state);
                    //$data['SenderID'] = $this->$id;
                    $data['Title'] = $this->ReadPropertyString('Title');
                    $data['InstanceID'] = $this->InstanceID;
                    $data['VariableID'] = $SourceID;
                    $data['VariableValue'] = GetValue($SourceID);
                    
                    $data['MediaID'] = $this->ReadPropertyInteger('MediaID');
                    
                    $VariableProfile = @IPS_GetVariableProfile( $this->GetProfileName($SourceID) );
                    if ($VariableProfile){
                        $data['Icon'] = $VariableProfile['Icon'];
                        $data['VariableValueFomatted'] = GetValueFormatted($SourceID);
                    } else {
                        $data['Icon'] = '';
                        $data['VariableValueFomatted'] = GetValue($SourceID);
                    }
                    $data['Text'] = str_replace('\\', ' - ', IPS_GetLocation($SourceID)) . ": ".$data['VariableValueFomatted'];
                    
                    if ($state != $oldState){
                        // State changed
                        if ( ($state === true && $this->ReadPropertyBoolean('NotifyConditionTrue')) || ($state === false && $this->ReadPropertyBoolean('NotifyConditionFalse')) ){
                            $resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{858A61A3-F139-4A08-97EB-B7F2BCA842B4}", "Buffer" => $data)));
                        }
                    }
                    
                }

		

		public function RequestAction($Ident, $Value) {
			
			switch($Ident) {
				default:
					throw new Exception("Invalid ident");
			}
		
		}

		private function GetProfileName($variableID) {
			$variable = IPS_GetVariable($variableID);
			if($variable['VariableCustomProfile'] != "")
				return $variable['VariableCustomProfile'];
			else
				return $variable['VariableProfile'];
		}

		
		private function CreateCategoryByIdent($id, $ident, $name) {
			
			 $cid = @IPS_GetObjectIDByIdent($ident, $id);
			 if($cid === false)
			 {
				 $cid = IPS_CreateCategory();
				 IPS_SetParent($cid, $id);
				 IPS_SetName($cid, $name);
				 IPS_SetIdent($cid, $ident);
			 }
			 return $cid;
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
                
                private function GetVariableType(){
                        switch ($this->ReadPropertyInteger("Type")){
                            case 0:
                                $VariableProfileName = $this->ReadPropertyString("VariableProfile");
                                $VariableProfile = @IPS_GetVariableProfile( $VariableProfileName );
                                $VariableType = $VariableProfile['ProfileType'];
                                break;
                            
                            case 1:
                                $VariableID = $this->ReadPropertyInteger("VariableID");
                                if ($VariableID){
                                    $VariableProfileName = $this->GetProfileName( $VariableID );
                                    $VariableProfile = @IPS_GetVariableProfile( $VariableProfileName );
                                    $VariableType = IPS_GetVariable($VariableID)['VariableType'];
                                } else {
                                    $VariableType = false;
                                }
                                break;
                        }
                        return $VariableType;
                        
                }
                
                private function GetConditionValue(){
                    switch ($this->GetVariableType()){
                        case 0:
                            $condition = $this->ReadPropertyBoolean("ConditionValueBoolean");
                            break;
                        case 1:
                            $condition = $this->ReadPropertyInteger("ConditionValueInteger");
                            break;
                        case 2:
                            $condition = $this->ReadPropertyFloat("ConditionValueFloat");
                            break;
                        case 3:
                            $condition = $this->ReadPropertyString("ConditionValueString");
                            break;                        
                    }
                    return $condition;
                }
                
                private function ValidateConditionValue(){
                    switch ($this->GetVariableType()){
                        case 0:
                            $result = is_bool($this->ReadPropertyBoolean("ConditionValueBoolean"));
                            break;
                        case 1:
                            $result = is_integer($this->ReadPropertyInteger("ConditionValueInteger"));
                            break;
                        case 2:
                            $result = is_float($this->ReadPropertyFloat("ConditionValueFloat"));
                            break;
                        case 3:
                            $result = is_string($this->ReadPropertyString("ConditionValueString"));
                            break;                        
                    }
                    return $result;
                }
                
                public function GetConfigurationForm() {
                        $element['type'] = 'Select';
                        $element['name'] = 'Type';
                        $element['caption'] = 'Typ';
                        $element['options'] = [
                            array( 'label' => '[Typ auswählen]', 'value' => -1),
                            array( 'label' => 'Variablenprofil', 'value' => 0),
                            array( 'label' => 'Variable', 'value' => 1),
                        ];
                        $form['elements'][] = $element;
                        
                        switch ($this->ReadPropertyInteger("Type")){
                            case -1:
                                $form['elements'][] = array( 'type' => 'Label', 'label' => 'Typ auswählen und "übernehmen" klicken, um fortzufahren.' );
                                break;
                            case 0:
                                $Type = 0;
                                $form['elements'][] = array( 'type' => 'Label', 'label' => 'Alle Variablen mit folgendem Variablenprofil unterhalb der ausgewälten Kategorie (inklusive Unterkategorien) ' );
                                $form['elements'][] = array( 'type' => 'SelectCategory', 'name' => 'ParentID', 'caption' => 'Kategorie' );
                                
                                $options = array();
                                $profiles = IPS_GetVariableProfileList();
                                sort($profiles);
                                foreach ($profiles as $value => $label){
                                    if (!in_array( $label, array("~Window.HM", "~Window.Hoppe", "~Window.Reversed"))){
                                        $options[] = array('label' => $label, 'value' => $label);
                                    }
                                }
                                $form['elements'][] = array( 'type' => 'Select', 'name' => 'VariableProfile', 'caption' => 'Profil', 'options' => $options);
                                
                                
                                $VariableProfileName = $this->ReadPropertyString("VariableProfile");
                                $VariableProfile = @IPS_GetVariableProfile( $VariableProfileName );
                                $VariableType = $VariableProfile['ProfileType'];
                                break;
                            
                            case 1:
                                $Type = 1;
                                $form['elements'][] = array( 'type' => 'SelectVariable', 'name' => 'VariableID', 'caption' => 'Variable' );
                                $VariableID = $this->ReadPropertyInteger("VariableID");
                                if ($VariableID > 0){
                                    $VariableProfileName = $this->GetProfileName( $VariableID );
                                    $VariableProfile = @IPS_GetVariableProfile( $VariableProfileName );
                                    $VariableType = IPS_GetVariable($VariableID)['VariableType'];
                                }
                                break;
                          
                        }
                        if (isset($Type) && isset($VariableType)){
                            if ( $VariableProfileName ){
                                
                                $form['elements'][] = array( 'type' => 'Label', 'label' => 'Bedingungen bei denen eine Statusmeldung erfolgen soll:' );
                                $options = array();
                                foreach ($VariableProfile['Associations'] as $item){
                                    $options[] = array('label' => $item['Name'], 'value' => $item['Value']);
                                }
                            } else {
                                $options = array();
                            }
                            switch ($VariableType){
                                case 0:
                                    $form['elements'][] = array( 'type' => 'Select', 'name' => 'Operator', 'caption' => 'Bedingung', 'options' => array( array('label' => '==', 'value' => '=='), array('label' => '!=', 'value' => '!=')) );
                                    $form['elements'][] = array( 'type' => 'Select', 'name' => 'ConditionValueBoolean', 'caption' => 'Wert', 'options' => $options );                                            
                                    break;    
                                case 1:
                                    $form['elements'][] = array( 'type' => 'Select', 'name' => 'Operator', 'caption' => 'Bedingung', 'options' => array( array('label' => '==', 'value' => '=='), array('label' => '!=', 'value' => '!='), array('label' => '<', 'value' => '<'), array('label' => '>', 'value' => '>') ) );
                                    if ($options) {
                                        $form['elements'][] = array( 'type' => 'Select', 'name' => 'ConditionValueInteger', 'caption' => 'Wert', 'options' => $options );
                                    } else {
                                        $form['elements'][] = array( 'type' => 'NumberSpinner', 'name' => 'ConditionValueInteger', 'caption' => 'Wert');
                                    }
                                    break;
                                case 2:
                                    $form['elements'][] = array( 'type' => 'Select', 'name' => 'Operator', 'caption' => 'Bedingung', 'options' => array( array('label' => '==', 'value' => '=='), array('label' => '!=', 'value' => '!='), array('label' => '<', 'value' => '<'), array('label' => '>', 'value' => '>') ) );
                                    if ($options) {
                                        $form['elements'][] = array( 'type' => 'Select', 'name' => 'ConditionValueInteger', 'caption' => 'Wert', 'options' => $options );
                                    } else {
                                        $form['elements'][] = array( 'type' => 'NumberSpinner', 'name' => 'ConditionValueInteger', 'caption' => 'Wert', 'digits' => 2);
                                    }
                                    break;
                                case 3:
                                    $form['elements'][] = array( 'type' => 'Select', 'name' => 'Operator', 'caption' => 'Bedingung', 'options' => array( array('label' => 'is equal', 'value' => 'isequal'), array('label' => 'contains', 'value' => 'contains'), array('label' => 'starts with', 'value' => 'starts'), array('label' => 'ends with', 'value' => 'end') ) );
                                    $form['elements'][] = array( 'type' => 'ValidationTextBox', 'name' => 'ConditionValueString', 'caption' => 'Wert');
                                    break;
                            }
                            
                            $form['elements'][] = array('type' => 'CheckBox', 'name' => 'Hide', 'caption' => 'Variablen im WebFront verstecken, wenn Bedingung nicht erfüllt ist.');

                            if ( $this->ReadPropertyString("Operator") != '' && $this->ValidateConditionValue()){
                                $form['elements'][] = array( 'type' => 'Label', 'label' => 'Benachrichtigung:' );
                                $form['elements'][] = array('type' => 'CheckBox', 'name' => 'NotifyConditionTrue', 'caption' => 'Benachrichtigen, wenn Bedingung erfüllt ist.');
                                $form['elements'][] = array('type' => 'CheckBox', 'name' => 'NotifyConditionFalse', 'caption' => 'Benachrichtigen, wenn Bedingung nicht mehr erfüllt ist.');

                                $form['elements'][] = array( 'type' => 'ValidationTextBox', 'name' => 'Title', 'caption' => 'Titel' );
                                //$form['elements'][] = array( 'type' => 'SelectMedia', 'name' => 'Icon', 'caption' => 'Icon' );
                                //$form['elements'][] = array( 'type' => 'Label', 'label' => 'Optional: Media-Element (Ton), das je nach Empfänger abgespielt wird (z.B. WebFront Audio Notification)' );
                                $form['elements'][] = array( 'type' => 'SelectMedia', 'name' => 'MediaID', 'caption' => 'Audiodatei' );   

                                $form['actions'][] = array('type' => 'Button', 'label' => 'Aktualisieren', 'onClick' => 'STATEMSG_Update($id);' );
                            }else {
                                $form['elements'][] = array( 'type' => 'Label', 'label' => 'Bitte "Übernehmen" klicken, um fortzufahren.' );
                            }
                        } else {
                            $form['elements'][] = array( 'type' => 'Label', 'label' => 'Bitte "Übernehmen" klicken, um fortzufahren.' );
                        }
                        return json_encode($form);
                }
	}

?>
