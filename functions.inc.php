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

/**
 * Build Location Zone Planning Day Progress Bar
 *
 * @param object $location_obj Location object
 * @param integer $idZone Zone ID
 * @param string $day Planning day
 * @return object Progress bar
 */
function api_airConditioning_locationZonePlanningDayProgressBar($location_obj,$idZone,$day){
 // build progress bar object
 $progressBar=new cProgressBar(null,"margin-bottom:0px;");
 // cycle all day planning steps
 if(is_array($location_obj->zones_array[$idZone]->plannings[$day])){
  foreach($location_obj->zones_array[$idZone]->plannings[$day] as $step){
   // calculate percentages
   $step->percentage=round(($step->time_end-$step->time_start)*100/86399,1); // 86400 one day - 1 second
   // add element to progress bar
   $progressBar->addElement($step->percentage,gmdate("H:i",$step->time_start),"progress-bar-striped","text-align:left;padding-left:4px;background-color:".($step->fkModality?$location_obj->modalities_array[$step->fkModality]->color:"#cccccc"));
  }
 }else{
  // build empty progress bar
  $progressBar->addElement(100,null,"progress-bar-striped","background-color:#cccccc");
  /** @todo verificare forse non serve se le inizializzo bene */
 }
 // return progress bar object
 return $progressBar;
}

?>