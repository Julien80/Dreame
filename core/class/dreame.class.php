<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class dreame extends eqLogic {
  /*     * *************************Attributs****************************** */

  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */

  /*     * ***********************Methode static*************************** */

  
  //* Fonction exécutée automatiquement toutes les minutes par Jeedom
	public static function cron() {
  		$eqLogics = self::byType('dreame');
		if(count($eqLogics) > 0) {
			foreach($eqLogics as $eqLogic) {
				if($eqLogic->getIsEnable() == 1) {
					$eqLogic->updateCmd();
				}
			} 
		} 
	}
  

  
 // * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  //public static function cron5() {
  
//	if($this->getIsEnable() == 1)
	//		self::createAndUpdateCmd();
 //   }
  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  public static function cron10() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert() {
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
    if($this->getIsEnable() == 1)
		self::createCmd();
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */

  /*     * **********************Getteur Setteur*************************** */
	public function createCmd($bCreateCmd = true) {

      	$order = 1;

		$batteryLevel = $this->getCmd(null, 'batteryLevel');
		if (!is_object($batteryLevel)) {
			$batteryLevel = new dreameCmd();
			$batteryLevel->setName(__('Batterie', __FILE__));
		}
		$batteryLevel->setOrder($order++);
		$batteryLevel->setLogicalId('batteryLevel');
		$batteryLevel->setEqLogic_id($this->getId());
		$batteryLevel->setType('info');
		$batteryLevel->setUnite('%');
		$batteryLevel->setSubType('numeric');
     	$batteryLevel->setTemplate('dashboard', 'default'); 
      	$batteryLevel->setTemplate('mobile', 'default'); 
		$batteryLevel->setIsVisible(1);
		$batteryLevel->setIsHistorized(1);
		$batteryLevel->setDisplay('forceReturnLineBefore', false);
		$batteryLevel->save();
      
      $isCharging = $this->getCmd(null, 'isCharging');
		if (!is_object($isCharging)) {
			$isCharging = new dreameCmd();
			$isCharging->setName(__('Etat de Charge', __FILE__));
		}
		$isCharging->setOrder($order++);
		$isCharging->setLogicalId('isCharging');
		$isCharging->setEqLogic_id($this->getId());
		$isCharging->setType('info');
		$isCharging->setTemplate('dashboard', 'default');
      	$isCharging->setTemplate('mobile', 'default');
		$isCharging->setSubType('numeric');
		$isCharging->setIsVisible(1);
		$isCharging->setIsHistorized(1);
		$isCharging->setDisplay('forceReturnLineBefore', false);
		$isCharging->save();
      
       	$error = $this->getCmd(null, 'error');
		if (!is_object($error)) {
			$error = new dreameCmd();
			$error->setName(__('Erreur', __FILE__));
		}
		$error->setOrder($order++);
		$error->setLogicalId('error');
		$error->setEqLogic_id($this->getId());
		$error->setType('info');
		$error->setTemplate('dashboard', 'line');
      	$error->setTemplate('mobile', 'line');
		$error->setSubType('numeric');
		$error->setIsVisible(1);
		$error->setIsHistorized(1);
		$error->setDisplay('forceReturnLineBefore', false);
		$error->save();
      
        $stateDevice = $this->getCmd(null, 'stateDevice');
		if (!is_object($stateDevice)) {
			$stateDevice = new dreameCmd();
			$stateDevice->setName(__('Statut', __FILE__));
		}
		$stateDevice->setOrder($order++);
		$stateDevice->setLogicalId('stateDevice');
		$stateDevice->setEqLogic_id($this->getId());
		$stateDevice->setType('info');
		$stateDevice->setTemplate('dashboard', 'line');
      	$stateDevice->setTemplate('mobile', 'line');
		$stateDevice->setSubType('numeric');
		$stateDevice->setIsVisible(1);
		$stateDevice->setIsHistorized(1);
		$stateDevice->setDisplay('forceReturnLineBefore', false);
		$stateDevice->save();
      
        $statusDevice = $this->getCmd(null, 'statusDevice');
		if (!is_object($statusDevice)) {
			$statusDevice = new dreameCmd();
			$statusDevice->setName(__('Etat', __FILE__));
		}
		$statusDevice->setOrder($order++);
		$statusDevice->setLogicalId('statusDevice');
		$statusDevice->setEqLogic_id($this->getId());
		$statusDevice->setType('info');
		$statusDevice->setTemplate('dashboard', 'line');
      	$statusDevice->setTemplate('mobile', 'line');
		$statusDevice->setSubType('other');
		$statusDevice->setIsVisible(1);
		$statusDevice->setIsHistorized(0);
		$statusDevice->setDisplay('forceReturnLineBefore', true);
		$statusDevice->save();
      
        $timeBrush = $this->getCmd(null, 'timeBrush');
		if (!is_object($timeBrush)) {
			$timeBrush = new dreameCmd();
			$timeBrush->setName(__('Temps restant brosse principale', __FILE__));
		}
		$timeBrush->setOrder($order++);
		$timeBrush->setLogicalId('timeBrush');
		$timeBrush->setEqLogic_id($this->getId());
		$timeBrush->setType('info');
		$timeBrush->setUnite('min');
      	$timeBrush->setTemplate('dashboard', 'line');
      	$timeBrush->setTemplate('mobile', 'line');
		$timeBrush->setSubType('numeric');
		$timeBrush->setIsVisible(1);
		$timeBrush->setIsHistorized(1);
		$timeBrush->setDisplay('forceReturnLineBefore', false);
		$timeBrush->save();
      
       $lifeBrush = $this->getCmd(null, 'lifeBrush');
		if (!is_object($lifeBrush)) {
			$lifeBrush = new dreameCmd();
			$lifeBrush->setName(__('Etat brosse principale', __FILE__));
		}
		$lifeBrush->setOrder($order++);
		$lifeBrush->setLogicalId('lifeBrush');
		$lifeBrush->setEqLogic_id($this->getId());
		$lifeBrush->setType('info');
        $lifeBrush->setTemplate('dashboard', 'line');
      	$lifeBrush->setTemplate('mobile', 'line');
		$lifeBrush->setUnite('%');
		$lifeBrush->setSubType('numeric');
		$lifeBrush->setIsVisible(1);
		$lifeBrush->setIsHistorized(1);
		$lifeBrush->setDisplay('forceReturnLineBefore', false);
		$lifeBrush->save();
      
        $timeBrushLeft = $this->getCmd(null, 'timeBrushLeft');
		if (!is_object($timeBrushLeft)) {
			$timeBrushLeft = new dreameCmd();
			$timeBrushLeft->setName(__('Durée de vie restante brosse latérale', __FILE__));
		}
		$timeBrushLeft->setOrder($order++);
		$timeBrushLeft->setLogicalId('timeBrushLeft');
		$timeBrushLeft->setEqLogic_id($this->getId());
		$timeBrushLeft->setType('info');
		$timeBrushLeft->setUnite('min');
		$timeBrushLeft->setSubType('numeric');
        $timeBrushLeft->setTemplate('dashboard', 'line');
      	$timeBrushLeft->setTemplate('mobile', 'line');
		$timeBrushLeft->setIsVisible(1);
		$timeBrushLeft->setIsHistorized(1);
		$timeBrushLeft->setDisplay('forceReturnLineBefore', false);
		$timeBrushLeft->save();
      
       $lifeBrushLeft = $this->getCmd(null, 'lifeBrushLeft');
		if (!is_object($lifeBrushLeft)) {
			$lifeBrushLeft = new dreameCmd();
			$lifeBrushLeft->setName(__('Etat brosse latérale', __FILE__));
		}
		$lifeBrushLeft->setOrder($order++);
		$lifeBrushLeft->setLogicalId('lifeBrushLeft');
		$lifeBrushLeft->setEqLogic_id($this->getId());
		$lifeBrushLeft->setType('info');
		$lifeBrushLeft->setUnite('%');
 	    $lifeBrushLeft->setTemplate('dashboard', 'line');
      	$lifeBrushLeft->setTemplate('mobile', 'line');
		$lifeBrushLeft->setSubType('numeric');
		$lifeBrushLeft->setIsVisible(1);
		$lifeBrushLeft->setIsHistorized(1);
		$lifeBrushLeft->setDisplay('forceReturnLineBefore', false);
		$lifeBrushLeft->save();
      
    	$timeFilterLeft = $this->getCmd(null, 'timeFilterLeft');
		if (!is_object($timeFilterLeft)) {
			$timeFilterLeft = new dreameCmd();
			$timeFilterLeft->setName(__('Durée de vie restante filtre', __FILE__));
		}
		$timeFilterLeft->setOrder($order++);
		$timeFilterLeft->setLogicalId('timeFilterLeft');
		$timeFilterLeft->setEqLogic_id($this->getId());
		$timeFilterLeft->setType('info');
		$timeFilterLeft->setUnite('min');
		$timeFilterLeft->setSubType('numeric');
        $timeFilterLeft->setTemplate('dashboard', 'line');
     	$timeFilterLeft->setTemplate('mobile', 'line');
		$timeFilterLeft->setIsVisible(1);
		$timeFilterLeft->setIsHistorized(1);
		$timeFilterLeft->setDisplay('forceReturnLineBefore', false);
		$timeFilterLeft->save();
      
       $lifeFilterLeft = $this->getCmd(null, 'lifeFilterLeft');
		if (!is_object($lifeFilterLeft)) {
			$lifeFilterLeft = new dreameCmd();
			$lifeFilterLeft->setName(__('Etat Filtre', __FILE__));
		}
		$lifeFilterLeft->setOrder($order++);
		$lifeFilterLeft->setLogicalId('lifeFilterLeft');
		$lifeFilterLeft->setEqLogic_id($this->getId());
		$lifeFilterLeft->setType('info');
		$lifeFilterLeft->setUnite('%');
    	$lifeFilterLeft->setTemplate('dashboard', 'line');
      	$lifeFilterLeft->setTemplate('mobile', 'line');
		$lifeFilterLeft->setSubType('numeric');
		$lifeFilterLeft->setIsVisible(1);
		$lifeFilterLeft->setIsHistorized(1);
		$lifeFilterLeft->setDisplay('forceReturnLineBefore', false);
		$lifeFilterLeft->save();
      
        $cleaningTime = $this->getCmd(null, 'cleaningTime');
		if (!is_object($cleaningTime)) {
			$cleaningTime = new dreameCmd();
			$cleaningTime->setName(__('Temps de nettoyage', __FILE__));
		}
		$cleaningTime->setOrder($order++);
		$cleaningTime->setLogicalId('cleaningTime');
		$cleaningTime->setEqLogic_id($this->getId());
		$cleaningTime->setType('info');
		$cleaningTime->setUnite('min');
      	$cleaningTime->setTemplate('dashboard', 'line');
      	$cleaningTime->setTemplate('mobile', 'line');
		$cleaningTime->setSubType('numeric');
		$cleaningTime->setIsVisible(1);
		$cleaningTime->setIsHistorized(1);
		$cleaningTime->setDisplay('forceReturnLineBefore', false);
		$cleaningTime->save();
      
    	$cleaningArea = $this->getCmd(null, 'cleaningArea');
		if (!is_object($cleaningArea)) {
			$cleaningArea = new dreameCmd();
			$cleaningArea->setName(__('Surface Nettoyée', __FILE__));
		}
		$cleaningArea->setOrder($order++);
		$cleaningArea->setLogicalId('cleaningArea');
		$cleaningArea->setEqLogic_id($this->getId());
		$cleaningArea->setType('info');
		$cleaningArea->setUnite('m2');
		$cleaningArea->setTemplate('dashboard', 'line');
      	$cleaningArea->setTemplate('mobile', 'line');
		$cleaningArea->setSubType('numeric');
		$cleaningArea->setIsVisible(1);
		$cleaningArea->setIsHistorized(1);
		$cleaningArea->setDisplay('forceReturnLineBefore', false);
		$cleaningArea->save();
      
		$stop = $this->getCmd('action', 'stop');
		if (!is_object($stop)) {
			$stop = new dreameCmd();
			$stop->setName(__('Arreter', __FILE__));
		}
		$stop->setOrder($order++);
		$stop->setIsVisible(1);
		$stop->setLogicalId('stop');
		$stop->setEqLogic_id($this->getId());
		$stop->setType('action');
		$stop->setSubType('other');
		$stop->setDisplay('generic_type', 'ENERGY_OFF');
		//$info->setDisplay('forceReturnLineBefore', true);
		$stop->setDisplay('forceReturnLineAfter', true);
		$stop->save();
      
      	$start = $this->getCmd('action', 'start');
		if (!is_object($start)) {
			$start = new dreameCmd();
			$start->setName(__('Démarrer', __FILE__));
		}
		$start->setOrder($order++);
		$start->setIsVisible(1);
		$start->setLogicalId('start');
		$start->setEqLogic_id($this->getId());
		$start->setType('action');
		$start->setSubType('other');
		$start->setDisplay('generic_type', 'ENERGY_ON');
		//$info->setDisplay('forceReturnLineBefore', true);
		$start->setDisplay('forceReturnLineAfter', true);
		$start->save();
      
        $home = $this->getCmd('action', 'home');
		if (!is_object($home)) {
			$home = new dreameCmd();
			$home->setName(__('Maison', __FILE__));
		}
		$home->setOrder($order++);
		$home->setIsVisible(1);
		$home->setLogicalId('home');
		$home->setEqLogic_id($this->getId());
		$home->setType('action');
		$home->setSubType('other');
		$home->setDisplay('generic_type', 'ENERGY_ON');
		//$info->setDisplay('forceReturnLineBefore', true);
		$home->setDisplay('forceReturnLineAfter', true);
		$home->save();
      
        $refresh = $this->getCmd('action', 'refresh');
		if (!is_object($refresh)) {
			$refresh = new dreameCmd();
			$refresh->setName(__('Rafraichir', __FILE__));
		}
		$refresh->setOrder($order++);
		$refresh->setIsVisible(1);
		$refresh->setLogicalId('refresh');
		$refresh->setEqLogic_id($this->getId());
		$refresh->setType('action');
		$refresh->setSubType('other');
		$refresh->setDisplay('generic_type', 'ENERGY_ON');
		//$info->setDisplay('forceReturnLineBefore', true);
		$refresh->setDisplay('forceReturnLineAfter', true);
		$refresh->save();
      
      	self::updateCmd();
      
 
      
    }
  
	public function updateCmd() {
		$did = 			$this->getConfiguration('did');
		$ip = 			$this->getConfiguration('ip');
		$token = 		$this->getConfiguration('token');
		$model = 		$this->getConfiguration('model'); 
		
		
		if(!empty($ip) && !empty($token)) {
			$cmd = "sudo miiocli --output json dreamevacuum --ip " . $ip . " --token " . $token ." status 2>&1";
			exec($cmd,$outputArray,$resultCode);
			log::add('dreame', 'debug', '[GET CMD] ' .$cmd);
		} else {
			log::add('dreame', 'debug', "Anomalie Token ou IP Vide");
			return;
		}
		
		log::add('dreame', 'debug', 'json outputArray '.json_encode($outputArray));
		$json = json_decode($outputArray[0]);
      	if ($json != null) 
        {
          log::add('dreame', 'debug', 'json json '.json_encode($json));

          $this->checkAndUpdateCmd("batteryLevel", 		$json->battery_level);
          $this->checkAndUpdateCmd("isCharging", 		$json->charging_state);
          $this->checkAndUpdateCmd("error", 		$json->device_faults);
          $this->checkAndUpdateCmd("stateDevice", 		$json->device_status);
          $this->checkAndUpdateCmd("timeBrush", 		$json->brush_left_time);
          $this->checkAndUpdateCmd("lifeBrush", 		$json->brush_life_level);
          $this->checkAndUpdateCmd("timeBrushLeft", 		$json->brush_left_time2);
          $this->checkAndUpdateCmd("lifeBrushLeft", 		$json->brush_life_level2);
          $this->checkAndUpdateCmd("timeFilterLeft", 		$json->filter_left_time);
          $this->checkAndUpdateCmd("lifeFilterLeft", 		$json->filter_life_level);
          $this->checkAndUpdateCmd("cleaningTime", 		$json->cleaning_time);
          $this->checkAndUpdateCmd("cleaningArea", 		$json->cleaning_area);

          if (($json->device_status == 2) AND ($json->charging_state == 1)){
              $this->checkAndUpdateCmd("statusDevice","Prêt à démarrer");
          }

          if ($json->device_status == 1){
              $this->checkAndUpdateCmd("statusDevice","Aspiration en cours");
          }

          if (($json->device_status == 2) AND ($json->charging_state != 1)){
              $this->checkAndUpdateCmd("statusDevice","Arret");
          }

          if (($json->device_status == 3) AND ($json->charging_state != 1)){
              $this->checkAndUpdateCmd("statusDevice","En Pause");
          }
        if ($json->device_status == 4){
              $this->checkAndUpdateCmd("statusDevice","Erreur");
          }
        if (($json->device_status == 5) AND ($json->charging_state == 5)){
              $this->checkAndUpdateCmd("statusDevice","Retour Maison");
          }
        
              if (($json->device_status == 6) AND ($json->charging_state == 1)){
              $this->checkAndUpdateCmd("statusDevice","En Charge");
          }
        }
	}

	public function sendCmd($cmd, $val = "") {
		
		$did = 			$this->getConfiguration('did');
		$ip = 			$this->getConfiguration('ip');
		$token = 		$this->getConfiguration('token');
		$model = 		$this->getConfiguration('model'); 
		
		
		$cmdLabel = "";
		$cmdValue = "";
		switch ($cmd) {
			case "home":
				$cmdLabel = "home";
				break;
			case "start":
				$cmdLabel = "start";
				break;
			case "stop":
				$cmdLabel = "stop";
				break;
				
			default:
			throw new Error('This should not append!');
			log::add('dreame', 'debug', 'Error while executing cmd ' . $this->getLogicalId());
			return;
		}
		
		
		if($cmdLabel == "")
		return;
		
		if(!empty($ip) && !empty($token)) {
			$cmd = "miiocli dreamevacuum --ip " . $ip . " --token " . $token ." ".$cmdLabel;
			exec($cmd,$outputArray,$resultCode);
			log::add('dreame', 'debug', '[GET CMD] ' .$cmd);
          	self::updateCmd();
			} 
		else {
			log::add('dreame', 'debug', "Anomalie Token ou IP Vide");
			return;
		}

	} 
  
}

class dreameCmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
    
    $eqLogic = $this->getEqLogic(); // Récupération de l’eqlogic

		switch ($this->getLogicalId()) {                
			case 'refresh': 
            	log::add('dreame', 'debug', 'Refresh : ' . $this->getLogicalId());
				$eqLogic->updateCmd();
				break;
			case 'start':
            	log::add('dreame', 'debug', 'start : ' . $this->getLogicalId());
            	$eqLogic->sendCmd('start');
				break;
			case 'stop':
            	log::add('dreame', 'debug', 'stop : ' . $this->getLogicalId());
            	$eqLogic->sendCmd('stop');
				break;
			case 'home':
            	log::add('dreame', 'debug', 'home : ' . $this->getLogicalId());
            	$eqLogic->sendCmd('home');
				break;
			     
			default:
				throw new Error('This should not append!');
				log::add('dreame', 'error', 'Aucune commande associée : ' . $this->getLogicalId());
				break;
		}

  }

  /*     * **********************Getteur Setteur*************************** */

}