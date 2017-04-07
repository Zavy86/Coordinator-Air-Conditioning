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
require_once(ROOT."modules/air-conditioning/classes/cAirConditioningLocationZone.class.php");
require_once(ROOT."modules/air-conditioning/classes/cAirConditioningLocationZoneAppliance.class.php");

/**
 * Air Conditioning - Locations
 *
 * @return array of locations objects
 */
function api_airConditioning_locations(){
 // definitions
 $locations_array=array();
 // get location objects
 $locations_results=$GLOBALS['database']->queryObjects("SELECT * FROM `air-conditioning_locations` ORDER BY `name`",$GLOBALS['debug']);
 foreach($locations_results as $location){$locations_array[$location->id]=new cAirConditioningLocation($location);} /** @toto order? */
 // return
 return $locations_array;
}

?>