<?php
/**
 * Framework - Submit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
// check for actions
if(!defined('ACTION')){die("ERROR EXECUTING WEB SERVICE: The action was not defined");}
// switch action
switch(ACTION){
 // zones
 case "zone_upload":zone_upload();break;
 case "zone_download":zone_download();break;
 case "zone_updates":zone_updates();break;
 // default
 default:
  // definitions
  $return=new stdClass();
  $return->ok=FALSE;
  $return->errors=array();
  // build error object
  $error=new stdClass();
  $error->code=1;
  $error->name="Action not found";
  $error->description="The action \"".ACTION."\" was not found in \"".MODULE."\" web service";
  // add error to return object
  $return->errors[]=$error;
  // encode and return
  echo json_encode($return);
}

/**
 * Zone Upload
 */
function zone_upload(){
 // definitions
 $return=new stdClass();
 // get objects
 $zone_obj=new cAirConditioningLocationZone($_REQUEST['token']);
 // check objects
 if(!$zone_obj->id){
  // zone not found
  $return->ok=FALSE;
  echo json_encode($return);
 }
 // acqurie variables
 $r_temperature=$_REQUEST['temperature'];
 $r_humidity=$_REQUEST['humidity'];
 // check parameters
 if(!$r_temperature){
  // temperature not defined
  $return->ok=FALSE;
  return json_encode($return);
 }
 if(!$r_humidity){
  // humidity not defined
  $return->ok=FALSE;
  return json_encode($return);
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
  $return->ok=FALSE;
  return json_encode($return);
 }

 // debug
 if($GLOBALS['debug']){
  api_dump($return,"return");
  api_dump($zone_obj,"zone object");
 }

 // ok
 $return->ok=TRUE;
 return json_encode($return);

}

?>