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
  echo json_encode($return);
 }
 if(!$r_humidity){
  // humidity not defined
  $return->ok=FALSE;
  echo json_encode($return);
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
  echo json_encode($return);
 }

 // ok
 $return->ok=TRUE;
 echo json_encode($return);

 // debug
 if($GLOBALS['debug']){
  api_dump($return,"return");
  api_dump($zone_obj,"zone object");
 }

}
/**
 * Zone Move
 *
 * @param string direction
 */
function location_zone_move($direction){
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 if(!$zone_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationZoneNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id);}
 // check parameters
 if(!in_array(strtolower($direction),array("up","down"))){api_alerts_add(api_text("dashboard_alert_zoneError"),"warning");api_redirect("?mod=dashboard&scr=dashboard_customize&idTile=".$zone_obj->id);}
 // build zone query objects
 $zone_qobj=new stdClass();
 $zone_qobj->id=$zone_obj->id;
 //switch direction
 switch(strtolower($direction)){
  // up -> order -1
  case "up":
   // set previous order
   $zone_qobj->order=$zone_obj->order-1;
   // check for order
   if($zone_qobj->order<1){api_alerts_add(api_text("dashboard_alert_zoneError"),"warning");api_redirect("?mod=dashboard&scr=dashboard_customize&idTile=".$zone_obj->id);}
   // update zone
   $GLOBALS['database']->queryUpdate("air-conditioning_locations_zones",$zone_qobj);
   // rebase other zones
   api_dump($rebase_query="UPDATE `air-conditioning_locations_zones` SET `order`=`order`+'1' WHERE `order`<'".$zone_obj->order."' AND `order`>='".$zone_qobj->order."' AND `order`<>'0' AND `id`!='".$zone_obj->id."' AND `fkLocation`='".$location_obj->id."'","rebase_query");
   $GLOBALS['database']->queryExecute($rebase_query);
   break;
  // down -> order +1
  case "down":
   // set following order
   $zone_qobj->order=$zone_obj->order+1;
   // update zone
   $GLOBALS['database']->queryUpdate("air-conditioning_locations_zones",$zone_qobj);
   // rebase other zones
   api_dump($rebase_query="UPDATE `air-conditioning_locations_zones` SET `order`=`order`-'1' WHERE `order`>'".$zone_obj->order."' AND `order`<='".$zone_qobj->order."' AND `order`<>'0' AND `id`!='".$zone_obj->id."' AND `fkLocation`='".$location_obj->id."'","rebase_query");
   $GLOBALS['database']->queryExecute($rebase_query);
   break;
 }
 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($direction,"direction");
 api_dump($zone_obj,"zone_obj");
 api_dump($zone_qobj,"zone_qobj");
 // redirect
 api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$zone_obj->id);
}
/**
 * Location Zone Delete
 */
function location_zone_delete(){
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 if(!$zone_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationZoneNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id);}
 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($zone_obj,"zone_obj");
 // delete zone
 $GLOBALS['database']->queryDelete("air-conditioning_locations_zones",$zone_obj->id);
 // redirect
 api_alerts_add(api_text("air-conditioning_alert_locationRemoved"),"warning");
 api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id);
}

?>