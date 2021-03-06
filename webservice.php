<?php
/**
 * Air Conditioning - Submit
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
$return->datas=array();
$return->errors=array();

// switch action
switch(ACTION){
 // zones
 case "zone_upload":zone_upload($return);break;
 case "zone_getDetection":zone_getDetection($return);break;
 case "zone_getTemperatureSetpoint":zone_getTemperatureSetpoint($return);break;
 case "zone_setTemperatureSetpoint":zone_setTemperatureSetpoint($return);break;
 // default
 default:
  // action not found
  $return->ok=false;
  $return->errors[]=make_error(101,"Action not found","The action \"".ACTION."\" was not found in \"".MODULE."\" web service");
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
  $return->errors[]=make_error(201,"Zone not found","The zone with token ".$_REQUEST['token']." was not found");
  return $return;
 }
 // acqurie variables
 $r_temperature=$_REQUEST['temperature'];
 $r_humidity=$_REQUEST['humidity'];
 // check parameters
 if(!$r_temperature){
  // temperature not defined
  $return->ok=false;
  $return->errors[]=make_error(202,"Temperature not defined","The temperature is not defined");
  return $return;
 }
 if(!$r_humidity){
  // humidity not defined
  $return->ok=false;
  $return->errors[]=make_error(203,"Humidity not defined","The humidity is not defined");
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
 api_dump($_REQUEST,"_REQUEST");
 api_dump($detection_qobj,"detection query object");
 // execute query
 $detection_id=$GLOBALS['database']->queryInsert("air-conditioning_locations_zones_detections",$detection_qobj);
 // check insert
 if(!$detection_id){
  // error
  $return->ok=false;
  $return->errors[]=make_error(204,"Detection not saved","There was an error while saving the measurement");
  return $return;
 }
 // ok
 $return->ok=true;
 // debug
 api_dump($return,"return");
 api_dump($zone_obj,"zone object");
 // return
 return $return;
}

/**
 * Zone Get Detection
 */
function zone_getDetection($return){
 // debug
 api_dump($_REQUEST,"_REQUEST");
 // get objects
 $zone_obj=new cAirConditioningLocationZone($_REQUEST['token']);
 // check objects
 if(!$zone_obj->id){
  // zone not found
  $return->ok=false;
  $return->errors[]=make_error(301,"Zone not found","The zone with token ".$_REQUEST['token']." was not found");
  return $return;
 }
 // get last detection
 $last_detection=$zone_obj->getDetections(1)[0];
 // debug
 api_dump($last_detection,"last_detection");
 // check if device is offline (15 minutes)
 if((time()-$last_detection->timestamp)>900){
  // error
  $return->ok=false;
  $return->errors[]=make_error(302,"Zone offline","This zone is offline for ".(time()-$last_detection->timestamp)." seconds");
  return $return;
 }
 // ok
 $return->ok=true;
 $return->datas['temperature']=$last_detection->temperature;
 $return->datas['humidity']=$last_detection->humidity;
 $return->datas['heater_status']=$last_detection->heater_status;
 // debug
 api_dump($return,"return");
 api_dump($zone_obj,"zone object");
 // return
 return $return;
}

/**
 * Zone Get Temperature Setpoint
 */
function zone_getTemperatureSetpoint($return){
 // debug
 api_dump($_REQUEST,"_REQUEST");
 // get objects
 $zone_obj=new cAirConditioningLocationZone($_REQUEST['token']);
 // check objects
 if(!$zone_obj->id){
  // zone not found
  $return->ok=false;
  $return->errors[]=make_error(401,"Zone not found","The zone with token ".$_REQUEST['token']." was not found");
  return $return;
 }
 // get current temperature setpoint
 $current_temperature=$zone_obj->getCurrentTemperature();
 // debug
 api_dump($current_temperature,"current_temperature");
 // check temperature
 if(!$current_temperature){
  // error
  $return->ok=false;
  $return->datas['temperature']=10; /** @todo verificare e in caso mandare giu la temperatura di antigelo */
  $return->errors[]=make_error(402,"Modality not found","There was an error loading current modality");
  return $return;
 }
 // ok
 $return->ok=true;
 $return->datas['temperature']=$current_temperature;
 // debug
 api_dump($return,"return");
 api_dump($zone_obj,"zone object");
 // return
 return $return;
}

/**
 * Zone Set Temperature Setpoint
 */
function zone_setTemperatureSetpoint($return){
 // debug
 api_dump($_REQUEST,"_REQUEST");
 // get objects
 $zone_obj=new cAirConditioningLocationZone($_REQUEST['token']);
 // acquire variables
 $r_temperature=$_REQUEST['temperature'];
 $r_duration=$_REQUEST['duration'];
 // check objects
 if(!$zone_obj->id){
  // zone not found
  $return->ok=false;
  $return->errors[]=make_error(501,"Zone not found","The zone with token ".$_REQUEST['token']." was not found");
  return $return;
 }
 // check temperatures
 if(!$r_temperature){
  // error
  $return->ok=false;
  $return->errors[]=make_error(502,"Temperature not setted","No temperature received or temperature received is lesser than current temperature setted..");
  return $return;
 }
 // check duration
 if(!$r_duration){$r_duration=3600;}
 /*
 // get current temperature setpoint
 $current_temperature=$zone_obj->getCurrentTemperature();
 // debug
 api_dump($current_temperature,"current_temperature");
 */
 // build zone query objects
 $zone_qobj=new stdClass();
 $zone_qobj->id=$zone_obj->id;
 $zone_qobj->updTimestamp=time();
 $zone_qobj->updFkUser=1;
 // set new manual temperature and calculate timestamp
 $zone_qobj->manual_temperature=$r_temperature;
 $zone_qobj->manual_timestamp=(time()+$r_duration);
 //debug
 api_dump($zone_qobj,"zone query object");
 // execute query
 $GLOBALS['database']->queryUpdate("air-conditioning_locations_zones",$zone_qobj);
 // ok
 $return->ok=true;
 // debug
 api_dump($return,"return");
 api_dump($zone_obj,"zone object");
 // return
 return $return;
}

?>
