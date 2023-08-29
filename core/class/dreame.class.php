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
require_once __DIR__ . '/../../../../core/php/core.inc.php';

class dreame extends eqLogic {
    /* * *************************Attributs****************************** */

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

    /* * ***********************Methode static*************************** */


    //* Fonction exécutée automatiquement toutes les minutes par Jeedom

    public static function cron() {
        $eqLogics = self::byType('dreame');
        if (count($eqLogics) > 0) {
            /** @var dreame $eqLogic */
            foreach ($eqLogics as $eqLogic) {
                if ($eqLogic->getIsEnable() == 1 && $eqLogic->getConfiguration('model') != 'dreame.vacuum.p2008') {
                    $eqLogic->updateCmd();
                }
            }
        }
    }



    // * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
    public static function cron5() {
        $eqLogics = self::byType('dreame');
        if (count($eqLogics) > 0) {
            /** @var dreame $eqLogic */
            foreach ($eqLogics as $eqLogic) {
                if ($eqLogic->getIsEnable() == 1 && $eqLogic->getConfiguration('model') == 'dreame.vacuum.p2008') {
                    $eqLogic->updateCmd();
                }
            }
        }
    }

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

    /* * *********************Méthodes d'instance************************* */

    // Fonction exécutée automatiquement avant la création de l'équipement
    public function preInsert() {
        $this->setIsEnable(1);
        $this->setIsVisible(1);
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

    /* * **********************Getteur Setteur*************************** */

    public static function detectDevices() {

        log::add(__CLASS__, "debug", "============================ DISCOVER ============================");

        $accountEmail = trim(config::byKey('account-email', __CLASS__));
        $accountPassword = trim(config::byKey('account-password', __CLASS__));
        $accountCountry = trim(config::byKey('account-country', __CLASS__));

        $cmd = system::getCmdSudo() . " micloud get-devices -u '" . $accountEmail . "' -p '" . $accountPassword . "' -c " . $accountCountry . " 2>&1";
        exec($cmd, $outputArray, $resultCode);
        log::add(__CLASS__, "debug", json_encode($outputArray));

        if ($resultCode != 0) {
            if (strstr($outputArray[23], 'Access denied')) { //$outputArray[23] = "micloud.micloudexception.MiCloudAccessDenied: Access denied. Did you set the correct api key and/or username?")
                log::add(__CLASS__, "debug", "Erreur Mot de Passe ou Email");

                event::add('jeedom::alert', array(
                    'level' => 'danger',
                    'page' => 'dreame',
                    'ttl' => 10000,
                    'message' => __('Identification impossible, vérifiez votre identifiant et votre mot de passe Xiami Home.', __FILE__),
                ));
                return [
                    "newEq" => 0,
                ];
            }
        } else {
            $json = json_decode($outputArray[0]);
            log::add(__CLASS__, "debug", json_encode($json));
            $getAllDevices = eqLogic::byType(__CLASS__);
            $numberNewDevice = 0;
            foreach ($json as $response) {
                $alreadyExist = false;
                foreach ($getAllDevices as $device) {
                    if ($device->getLogicalId() == $response->did) {
                        $alreadyExist = true;
                        break;
                    }
                }

                if ($alreadyExist) {
                    if ($device->getConfiguration('ip') != $response->localip || $device->getConfiguration('token') != $response->token) {
                        $device->setConfiguration('ip', $response->localip);
                        $device->setConfiguration('token', $response->token);
                        $device->setConfiguration('modelType', self::getModelType($response->model));
                        $device->save();
                        log::add(__CLASS__, "debug", "Mise à jour de l'IP et du token pour l'équipement existant.");
                    } elseif ($device->getConfiguration('modelType') == '') {
                        $device->setConfiguration('modelType', self::getModelType($response->model));
                        $device->save();
                    } else {
                        log::add(__CLASS__, "debug", "Equipement déjà présent, pas de modification");
                    }
                } else {
                    // Check if $response->model contains 'Dreame' or 'viomi'
                    if (strpos($response->model, 'dreame') !== false || strpos($response->model, 'viomi') !== false || strpos($response->model, 'roborock') !== false) {
                        $eqlogic = new dreame();
                        $eqlogic->setName($response->name);
                        $eqlogic->setIsEnable(1);
                        $eqlogic->setIsVisible(0);
                        $eqlogic->setLogicalId($response->did);
                        $eqlogic->setEqType_name('dreame');
                        $eqlogic->setConfiguration('did', $response->did);
                        $eqlogic->setConfiguration('ip', $response->localip);
                        $eqlogic->setConfiguration('token', $response->token);
                        $eqlogic->setConfiguration('model', $response->model);
                        $eqlogic->setConfiguration('modelType', self::getModelType($response->model));
                        $eqlogic->save();
                        $numberNewDevice++;
                        log::add(__CLASS__, "debug", "Nouvel Equipement, ajout en cours.");
                        $eqlogic->createCmd();
                    } else {
                        log::add(__CLASS__, "debug", "Le modèle de l'équipement n'est pas pris en charge : " . $response->model);
                    }
                }
            }
        }

        return [
            "newEq" => $numberNewDevice,
        ];
    }


    public static function getFileContent($path) {
        if (!file_exists($path)) {
            log::add(__CLASS__, 'error', 'File not found  : ' . $path);
            return null;
        }

        $content = file_get_contents($path);

        if (!is_json($content)) {
            // log::add(__CLASS__, 'debug', 'not JSON file ' . $path);
            return $content;
        }

        $configFile = json_decode($content, true);
        return $configFile;
    }

    public function createCmd() {

        log::add(__CLASS__, "debug", "============================ CREATING CMD ============================");

        $modelType = $this->getConfiguration('modelType');
        $order_cmd = 1;

        $path = __DIR__ . '/../conf/' . $modelType . '.json';
        log::add(__CLASS__, 'debug', 'GENERATE CMD FROM CONFIG FILE : ' . $path);

        $configFile = self::getFileContent($path);

        $updateCmd = true;

        // get status to know with info are available and then create only the associate cmd (no more!)
        try {
            $status = $this->execCmd('status');
            if ($status === null) {
                log::add(__CLASS__, 'warning', 'Skipping cmd creation - No status available : ' . json_last_error_msg());
                $updateCmd = false;
            } else {

                $keyAvailable = array_keys($status);

                foreach ($configFile['cmds'] as $command) {
                    if (
                        $command["type"] == 'info' && !in_array($command["logicalId"], $keyAvailable)
                        && !isset($command["configuration"]["request"])  //si la cmd ne fait pas parti d'une requete dédiée
                    ) {
                        log::add(__CLASS__, 'warning', '  -- skipping cmd ' . $command['name']);
                        continue;
                    }

                    log::add(__CLASS__, 'debug', '  -- creating cmd ' . $command['name']);

                    $cmd = $this->getCmd(null, $command["logicalId"]);
                    if (!is_object($cmd)) {
                        $cmd = new dreameCmd();
                        $cmd->setOrder($order_cmd);
                        $cmd->setEqLogic_id($this->getId());
                    }
                    utils::a2o($cmd, $command);
                    $cmd->save();
                    $order_cmd++;
                }
            }
        } catch (Exception $e) {
            log::add(__CLASS__, 'warning', $e->getMessage());
            // log::add(__CLASS__, 'error', 'Cannot save Cmd for this EqLogic -- ' . $e->getMessage());
        }

        // check if the vacuum accept room action
        // if so create the associate room cmd action
        if (key_exists('rooms', $configFile) && key_exists('request', $configFile['rooms']) && key_exists('cmd', $configFile['rooms'])) {
            try {

                $rooms = $this->execCmd($configFile['rooms']['request']);

                if ($rooms === null) {
                    log::add(__CLASS__, 'warning', 'Skipping cmd creation - No rooms available : ' . json_last_error_msg());
                }
                log::add(__CLASS__, 'debug', 'rooms => : ' . json_encode($rooms));


                foreach ($rooms as $room) {
                    $command = $configFile['rooms']['cmd'];
                    $logicalId = "clean_room_" . $room[0];
                    $name = "Nettoyer Pièce " . $room[0];

                    log::add(__CLASS__, 'debug', '  -- creating cmd ' . $name);

                    $cmd = $this->getCmd(null, $logicalId);
                    if (!is_object($cmd)) {
                        $cmd = new dreameCmd();
                        $cmd->setOrder($order_cmd);
                        $cmd->setEqLogic_id($this->getId());
                        $cmd->setName($name);
                        $cmd->setConfiguration('roomId', "[" . $room[0] . "]");
                        $cmd->setLogicalId($logicalId);
                    }
                    utils::a2o($cmd, $command);
                    $cmd->save();
                    $order_cmd++;
                }
            } catch (Exception $e) {
                log::add(__CLASS__, 'warning', $e->getMessage());
            }
        }

        // update the cmd based on the last one received
        if ($updateCmd) $this->updateCmd($status);
    }

    public function execCmd($cmd, $value = '') {
        $ip = $this->getConfiguration('ip');
        $token = $this->getConfiguration('token');
        $modelType = $this->getConfiguration('modelType');

        $errorFile = __DIR__ . '/../../data/exec/error_' . $this->getId() . '.txt';

        if (!empty($ip) && !empty($token)) {
            $call = ($modelType == 'genericmiot') ? ' call' : '';
            $val = ($value === '') ? '' :  escapeshellarg($value);
            $exec = system::getCmdSudo() . " miiocli -o json_pretty " . $modelType . " --ip " . $ip . " --token " . $token . $call . " " . $cmd . " " . $val .  " >&1 2>" . $errorFile;
            log::add(__CLASS__, 'debug', 'CMD BY ' . $modelType . " => " . $exec);
            exec($exec, $outputArray, $resultCode);
        } else {
            log::add(__CLASS__, 'debug', "updateCmd impossible : Pas d'IP ou pas de Token");
            return array();
        }

        $resultContent = implode(PHP_EOL, $outputArray);
        $jsonResultData = json_decode($resultContent, true);
        log::add(__CLASS__, 'debug', "CMD result :" . json_encode($jsonResultData));

        if ($jsonResultData === null) {
            $errorContent = self::getFileContent($errorFile);
            $errorPos = strpos($errorContent, 'Error:');
            if ($errorPos !== false) {
                throw new Exception(substr($errorContent, $errorPos));
            }
        }

        if (filesize($errorFile)) unlink($errorFile);

        return $jsonResultData;
    }

    public function updateCmd($statusOutput = '') {
        log::add(__CLASS__, "debug", "============================ UPDATING CMD ============================");
        $modelType = $this->getConfiguration('modelType');

        if ($statusOutput == '') {
            try {
                $statusOutput = $this->execCmd('status');

                if ($statusOutput === null) {
                    log::add(__CLASS__, 'debug', 'Erreur JSON (null) : ' . json_last_error_msg());
                    return;
                }
            } catch (Exception $e) {
                log::add(__CLASS__, 'warning', $e->getMessage());
                return;
            }
        }

        log::add(__CLASS__, 'debug', 'JSON ' . json_encode($statusOutput));

        foreach ($statusOutput as $key => $value) {
            $cmd = $this->getCmd('info', $key);
            if (!is_object($cmd)) {
                // log::add(__CLASS__, 'warning', 'Pas d\'update pour la clé ' . $key);
                continue;
            }
            log::add(__CLASS__, 'debug', 'Updating [' . $key . '] with value [' . $value . ']');
            // log::add(__CLASS__, 'debug', 'cmd exist ' . json_encode(utils::o2a($cmd)));
            $this->checkAndUpdateCmd($key, $value);
        }

        if ($modelType == 'dreamevacuum') {

            $device_status_str = "";
            if ($statusOutput["device_status"] == 1) $device_status_str = "Aspiration en cours";
            if (($statusOutput["device_status"] == 2) && ($statusOutput["charging_state"] == 1)) $device_status_str = "Prêt à démarrer";
            if (($statusOutput["device_status"] == 2) && ($statusOutput["charging_state"] != 1)) $device_status_str = "Arrêt";
            if (($statusOutput["device_status"] == 3) && ($statusOutput["charging_state"] != 1)) $device_status_str = "En pause";
            if ($statusOutput["device_status"] == 4) $device_status_str = "Erreur";
            if (($statusOutput["device_status"] == 5) && ($statusOutput["charging_state"] == 5)) $device_status_str = "Retour maison";
            if (($statusOutput["device_status"] == 6) && ($statusOutput["charging_state"] == 1)) $device_status_str = "En charge";
            if ($statusOutput["device_status"] == 7) $device_status_str = "Aspiration et lavage en cours";
            if ($statusOutput["device_status"] == 8) $device_status_str = "Séchage de la serpillère";
            if ($statusOutput["device_status"] == 12) $device_status_str = "Nettoyage en cours de la zone";
            if ($device_status_str != "")  $this->checkAndUpdateCmd("device_status_str", $device_status_str);

            $error_device = "";
            if ($statusOutput["filter_life_level"] == 51) $error_device = "Le filtre est mouillé";
            if ($statusOutput["filter_life_level"] == 106) $error_device = "Vider le bac et nettoyer la planche de lavage.";
            if ($error_device != "") $this->checkAndUpdateCmd("error_device", $error_device);
        } elseif ($modelType == 'genericmiot') {

            $device_status_str = "";
            if (($statusOutput["vacuum:status"] == 2) and ($statusOutput["battery:charging-state"] == 1)) $device_status_str = "Prêt à démarrer";
            if ($statusOutput["vacuum:status"] == 1) $device_status_str =  "Aspiration en cours";
            if (($statusOutput["vacuum:status"] == 2) and ($statusOutput["battery:charging-state"] != 1)) $device_status_str =  "Arret";
            if (($statusOutput["vacuum:status"] == 3) and ($statusOutput["battery:charging-state"] != 1)) $device_status_str =  "En Pause";
            if ($statusOutput["vacuum:status"] == 4) $device_status_str =  "Erreur";
            if (($statusOutput["vacuum:status"] == 5) and ($statusOutput["battery:charging-state"] == 5)) $device_status_str =  "Retour Maison";
            if (($statusOutput["vacuum:status"] == 6) and ($statusOutput["battery:charging-state"] == 1)) $device_status_str =  "En Charge";
            if ($statusOutput["vacuum:status"] == 7) $device_status_str =  "Aspiration et Lavage en cours";
            if ($statusOutput["vacuum:status"] == 8) $device_status_str =  "Séchage de la serpillère";
            if ($statusOutput["vacuum:status"] == 12) $device_status_str =  "Nettoyage en cours de la Zone";
            if ($device_status_str != "")  $this->checkAndUpdateCmd("device_status_str", $device_status_str);

            $error_device = "";
            if ($statusOutput->{"vacuum:fault"} == 51) $error_device = "Filtre est mouillé";
            if ($statusOutput->{"vacuum:fault"} == 106) $error_device = "Vider le bac et nettoyer la planche de lavage.";
            if ($error_device != "") $this->checkAndUpdateCmd("error_device", $error_device);
        }


        $child_lock = $this->getCmd('info', 'child_lock');
        if (is_object($child_lock)) {
            $child_lock_action = $child_lock->getConfiguration('request');

            try {
                $statusOutput = $this->execCmd($child_lock_action);

                if ($statusOutput === null) {
                    log::add(__CLASS__, 'debug', 'Erreur JSON (null) : ' . json_last_error_msg());
                    return;
                }

                $this->checkAndUpdateCmd('child_lock', $statusOutput);
            } catch (Exception $e) {
                log::add(__CLASS__, 'warning', $e->getMessage());
                return;
            }
        }
    }

    public static function getModelType($model) {

        switch ($model) {
            case 'viomi.vacuum.v8':
                $type = 'viomivacuum';
                break;

            case 'dreame.vacuum.p2008':
                $type = 'dreamevacuum';
                break;

            default:
                if (strpos($model, 'roborock') !== false) {
                    $type = 'roborockvacuum';
                } else {
                    $type = 'genericmiot';
                }
                break;
        }

        return $type;
    }

    public function getEqIcon() {
        $modelType = $this->getConfiguration('modelType', 'unknownvacuum');
        $imgPath = 'plugins/dreame/data/img/' . $modelType . "_icon.png";

        return $imgPath;
    }

    public function sendCmd($cmd, $value = '') {
        $ip = $this->getConfiguration('ip');
        $token = $this->getConfiguration('token');
        $modelType = $this->getConfiguration('modelType');

        $call = ($modelType == 'genericmiot') ? ' call' : '';
        $cmdExec = system::getCmdSudo() . " miiocli $modelType --ip $ip --token $token" . $call;

        if (!empty($ip) && !empty($token)) {
            $finalCmd = "$cmdExec $cmd $value";

            log::add(__CLASS__, 'debug', '[CMD] ' . $finalCmd);
            exec($finalCmd, $outputArray, $resultCode);
            $this->updateCmd();
        } else {
            log::add(__CLASS__, 'debug', "updateCmd impossible : Pas d'IP ou pas de Token");
        }
    }
}

class dreameCmd extends cmd {
    /* * *************************Attributs****************************** */

    /*
    public static $_widgetPossibility = array();
    */

    /* * ***********************Methode static*************************** */


    /* * *********************Methode d'instance************************* */

    /*
    * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
    public function dontRemoveCmd() {
    return true;
    }
    */

    // Exécution d'une commande
    public function execute($_options = array()) {
        log::add('dreame', "debug", "============================ EXEC CMD ============================");

        /** @var dreame $eqLogic */
        $eqLogic = $this->getEqLogic(); // Récupération de l’eqlogic
        Log::add('dreame', 'debug', '  with options : ' . json_encode($_options));

        $request = $this->getConfiguration('request', null);

        if (is_null($request)) {
            log::add('dreame', 'error', 'Command cannot be exec, no conf found on ' . $this->getHumanName() . ' for action [' . $this->getLogicalId() . '/' . $this->getId() . ']');
            return;
        }

        $logicalId = (strpos(strtolower($this->getLogicalId()), 'clean_room') !== false) ? 'cleanRoom' : $this->getLogicalId();

        switch ($logicalId) {
            case 'refresh':
                log::add('dreame', 'debug', 'running : ' . $this->getLogicalId());
                $eqLogic->updateCmd();
                break;

            case 'start':
            case 'stop':
            case 'home':
            case 'position':
            case 'playSound':
                log::add('dreame', 'debug', 'running : ' . $this->getLogicalId() . ' - request: ' . $request);
                $eqLogic->execCmd($request);
                break;

            case 'setSpeed':
                log::add('dreame', 'debug', 'running : ' . $this->getLogicalId() . ' request: ' . $request);
                $speed = isset($_options['select']) ? $_options['select'] : $_options['slider'];
                $eqLogic->execCmd($request, $speed);
                break;

            case 'setChildLock':
                log::add('dreame', 'debug', 'running : ' . $this->getLogicalId() . ' request: ' . $request);
                $speed = isset($_options['select']) ? $_options['select'] : $_options['slider'];
                $eqLogic->execCmd($request, $speed);
                break;

            case 'cleanRoom':
                log::add('dreame', 'debug', 'running : ' . $this->getLogicalId() . ' request: ' . $request);
                $roomId = $this->getConfiguration('roomId');
                if ($roomId == '') {
                    log::add('dreame', 'error', 'No room id found for cmd [' . $this->getId() . ']');
                    return;
                }
                $eqLogic->execCmd($request, $roomId);
                break;

            case 'custom':
                $request = $_options['title'] ?? '';
                if ($request == '') {
                    log::add('dreame', 'error', 'Empty field "' . $this->getDisplay('title_placeholder', 'Titre') . '" [cmdId : ' . $this->getId() . ']');
                    return;
                }
                $option = jeedom::evaluateExpression($_options['message']) ?? '';
                if ($option == '') {
                    log::add('dreame', 'debug', 'Empty field "' . $this->getDisplay('message_placeholder', 'Message') . '" [cmdId : ' . $this->getId() . ']');
                }
                log::add('dreame', 'debug', 'running : ' . $this->getLogicalId() . ' request: ' . $request . ' / options : ' . json_encode($option));
                try {
                    $result =  $eqLogic->execCmd($request, $option);
                    event::add('jeedom::alert', array(
                        'level' => 'success',
                        'page' => 'dreame',
                        'ttl' => 10000,
                        'message' => __('Résultat de la commande : ', __FILE__) . $result,
                    ));
                } catch (Exception $e) {
                    event::add('jeedom::alert', array(
                        'level' => 'error',
                        'page' => 'dreame',
                        'ttl' => 10000,
                        'message' => __('Résultat de la commande : ', __FILE__) . $e->getMessage(),
                    ));
                }
                return $result;
                break;

            case '':
                log::add('dreame', 'debug', 'running custom action named "' . $this->getName() . '"');
                break;

            default:
                log::add('dreame', 'error', 'Aucune commande associée : ' . $this->getLogicalId());
                break;
        }
    }



    /* * **********************Getteur Setteur*************************** */
}
