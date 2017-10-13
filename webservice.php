<?php
/**
 * Framework - Submit
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
// check for actions
if(!defined('ACTION')){die("ERROR EXECUTING WEB SERVICE: The action was not defined");}

// errors /** @todo migliorabile */
function make_error($error_code,$error_name,$error_description=null){
 // build error object
 $error_obj=new stdClass();
 $error_obj->code=$error_code;
 $error_obj->name=$error_name;
 $error_obj->description=$error_description;
 // return error object
 return $error_obj;
}

// definitions
$return=new stdClass();
$return->ok=false;
$return->errors=array();

// switch action
switch(ACTION){
 // zones
 case "zone_upload":zone_upload($return);break;
 case "zone_download":zone_download($return);break;
 case "zone_updates":zone_updates($return);break;
 // default
 default:
  // action not found
  $return->ok=false;
  $return->errors[]=make_error(1,"Action not found","The action \"".ACTION."\" was not found in \"".MODULE."\" web service");
}

// encode and return
echo json_encode($return);

/**
 * Zone Upload
 */
function zone_upload($return){
 // get objects
 $zone_obj=new cAirConditioningLocationZone($_REQUEST['token']);
 // check objects
 if(!$zone_obj->id){
  // zone not found
  $return->ok=false;
  $return->errors[]=make_error(2,"Zone not found","The zone with token ".$_REQUEST['token']." was not found");
  return $return;
 }
 // acqurie variables
 $r_temperature=$_REQUEST['temperature'];
 $r_humidity=$_REQUEST['humidity'];
 // check parameters
 if(!$r_temperature){
  // temperature not defined
  $return->ok=false;
  $return->errors[]=make_error(3,"Temperature not defined","The temperature is not defined");
  return $return;
 }
 if(!$r_humidity){
  // humidity not defined
  $return->ok=false;
  $return->errors[]=make_error(3,"Humidity not defined","The humidity is not defined");
  return $return;
 }
 // build location query objects
 $detection_qobj=new stdClass();
 $detection_qobj->fkZone=$zone_obj->id;
 $detection_qobj->timestamp=time();
 $detection_qobj->temperature=(double)$_REQUEST['temperature'];
 $detection_qobj->humidity=(double)$_REQUEST['humidity'];
 $detection_qobj->heater_status=(int)$_REQUEST['heater_status'];
 $detection_qobj->cooler_status=(int)$_REQUEST['cooler_status'];
 $detection_qobj->humidifier_status=(int)$_REQUEST['humidifier_status'];
 $detection_qobj->dehumidifier_status=(int)$_REQUEST['dehumidifier_status'];
 // debug
 if($GLOBALS['debug']){
  api_dump($_REQUEST,"_REQUEST");
  api_dump($detection_qobj,"detection query object");
 }
 // execute query
 $detection_id=$GLOBALS['database']->queryInsert("air-conditioning_locations_zones_detections",$detection_qobj);

 // check insert
 if(!$detection_id){
  // error
  $return->ok=false;
  $return->errors[]=make_error(4,"Detection not saved","There was an error while saving the measurement");
  return $return;
 }

 // debug
 if($GLOBALS['debug']){
  api_dump($return,"return");
  api_dump($zone_obj,"zone object");
 }

 // ok
 $return->ok=TRUE;
 return $return;

}

?>