<?php
/**
 * Dashboard Functions
 *
 * @package Coordinator\Modules\Dashboard
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

// include classes
require_once(ROOT."modules/air-conditioning/classes/cAirConditioningLocation.class.php");
require_once(ROOT."modules/air-conditioning/classes/cAirConditioningLocationModality.class.php");
require_once(ROOT."modules/air-conditioning/classes/cAirConditioningLocationZone.class.php");
require_once(ROOT."modules/air-conditioning/classes/cAirConditioningLocationZoneDetection.class.php");

/**
 * Air Conditioning - Locations
 *
 * @param boolean $deleted Show deleted locations
 * @return array Locations objects
 */
function api_airConditioning_locations($deleted=FALSE){
 // definitions
 $locations_array=array();
 // get location objects
 $locations_results=$GLOBALS['database']->queryObjects("SELECT * FROM `air-conditioning_locations` ORDER BY `name`",$GLOBALS['debug']);
 foreach($locations_results as $location){/** @toto order? */
  if(!$deleted && $location->deleted){continue;}
  $locations_array[$location->id]=new cAirConditioningLocation($location);
 }
 // return
 return $locations_array;
}

/**
 * Air Conditioning - Available Appliances
 *
 * @return array Available Appliances
 */
function api_airConditioning_availableAppliances(){
 // definitions
 $appliances_array=array();
 $appliances_array["heater"]=api_text("appliance-heater");
 $appliances_array["cooler"]=api_text("appliance-cooler");
 $appliances_array["dehumidifier"]=api_text("appliance-dehumidifier");
 $appliances_array["humidifier"]=api_text("appliance-humidifier");
 // return
 return $appliances_array;
}

?>