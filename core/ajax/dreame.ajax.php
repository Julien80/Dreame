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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

  	require_once dirname(__FILE__) . '/../php/dreame.inc.php';
  /* Fonction permettant l'envoi de l'entête 'Content-Type: application/json'
    En V3 : indiquer l'argument 'true' pour contrôler le token d'accès Jeedom
    En V4 : autoriser l'exécution d'une méthode 'action' en GET en indiquant le(s) nom(s) de(s) action(s) dans un tableau en argument
  */
    ajax::init();


	if (init('action') == 'detectDevices') { 
		ajax::success(detectDevices());
	}
  
	if (init('action') == 'discoverFromIp') { 
		ajax::success(discoverFromIp());
	}

  
    throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . init('action'));
    /*     * *********Catch exeption*************** */
}
catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}

function detectDevices() {
  log::add("dreame", "debug", "============================ DISCOVER 1============================");
  $accountEmail = 	trim(config::byKey('account-email', 'dreame'));
  $accountPassword = 	trim(config::byKey('account-password', 'dreame'));
  $accountCountry = 	trim(config::byKey('account-country', 'dreame'));
  	$cmd = "micloud -u " . $accountEmail . " -p " . $accountPassword . " -c ". $accountCountry. " 2>&1";
	exec($cmd,$outputArray,$resultCode);
  	if ($resultCode != 0) {
      log::add("dreame", "debug", "============================ DISCOVER ERROR============================");
      log::add("dreame", "debug", "============================ ERROR============================");
      log::add("dreame", "debug", $outputArray[23]);
      log::add("dreame", "debug", "============================ ERROR============================");
      	if (strstr( $outputArray[23],'Access denied')){ //$outputArray[23] = "micloud.micloudexception.MiCloudAccessDenied: Access denied. Did you set the correct api key and/or username?")

          event::add('jeedom::alert', array(
          'level' => 'danger',
          'page' => 'dreame',
          'ttl' => 10000,
          'message' => __('Identification impossible, vérifiez votre identifiant et votre mot de passe Xiami Home.', __FILE__),
          ));
         return ["countResults" => 0,
          "newEq" => 0,
          "results"=> []];
  		}
      
    }else{
      log::add("dreame", "debug", "============================ DISCOVER NO ERROR============================");
  
    $json = json_decode($outputArray[0]);

  	$getAllDevices = eqLogic::byType('dreame');
            log::add("dreame", "debug", "la");
  	foreach($json as $response) {
      	$alreadyExist = false;
		foreach($getAllDevices as $allDevices) {
 log::add("dreame", "debug", "la2");
         	if ($allDevices->getLogicalId() == $response->did) {
                $alreadyExist = true;
              log::add("dreame", "debug", "la3");
              	break;
            }
        }
      log::add("dreame", "debug", "la4");
        if ($alreadyExist) {
			log::add("dreame", "debug", "Equipement déjà présent, il ne faut donc pas l'ajouter");
        }else{
           log::add("dreame", "debug", "la5");
              $eqlogic = new dreame();
          log::add("dreame", "debug", "la8");
              $eqlogic->setName($response->name);
              $eqlogic->setIsEnable(1);
              $eqlogic->setIsVisible(0);
              $eqlogic->setLogicalId($response->did);
              $eqlogic->setEqType_name('dreame');
            log::add("dreame", "debug", "la6");
              $eqlogic->setConfiguration('did', $response->did);
              $eqlogic->setConfiguration('ip', $response->localip);
              $eqlogic->setConfiguration('token', $response->token);      
              $eqlogic->setConfiguration('model', $response->model);
            log::add("dreame", "debug", "laè");
              $eqlogic->save();
          	log::add("dreame", "debug", "Nouvel Equipement, ajout en cours.");
         }
      	
    }

    }

  log::add("dreame", "debug", "============================ DISCOVER ============================");

  
  return ["countResults" => $countResults,
          "newEq" => 12,
          "results"=> $arrayResults
          ];
  
}