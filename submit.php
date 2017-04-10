<?php
/**
 * Framework - Submit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
// check for actions
if(!defined('ACTION')){die("ERROR EXECUTING SCRIPT: The action was not defined");}
// switch action
switch(ACTION){
 // location
 case "location_save":location_save();break;
 case "location_delete":location_deleted(TRUE);break;
 case "location_undelete":location_deleted(FALSE);break;
 case "location_remove":location_remove();break;
 case "location_zone_save":location_zone_save();break;
 case "location_zone_move_up":location_zone_move("up");break;
 case "location_zone_move_down":location_zone_move("down");break;
 case "location_zone_delete":location_zone_delete();break;
 // default
 default:
  api_alerts_add(api_text("alert_submitFunctionNotFound",array(MODULE,SCRIPT,ACTION)),"danger");
  api_redirect("?mod=".MODULE);
}

/**
 * Location Save
 */
function location_save(){
 // get location object
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 // debug
 api_dump($location_obj,"location object");
 // build location query objects
 $zone_qobj=new stdClass();
 $zone_qobj->id=$location_obj->id;
 $zone_qobj->name=addslashes($_REQUEST['name']);
 $zone_qobj->description=addslashes($_REQUEST['description']);
 // check location
 if($location_obj->id){
  // update location
  $zone_qobj->updTimestamp=time();
  $zone_qobj->updFkUser=$location_obj->id;
  // debug
  api_dump($zone_qobj,"location query object");
  // execute query
  $GLOBALS['database']->queryUpdate("air-conditioning_locations",$zone_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationUpdated"),"success");
 }else{
  // insert location
  $zone_qobj->addTimestamp=time();
  $zone_qobj->addFkUser=$location_obj->id;
  // debug
  api_dump($zone_qobj,"location query object");
  // execute query
  $zone_qobj->id=$GLOBALS['database']->queryInsert("air-conditioning_locations",$zone_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationCreated"),"success");
 }
 // redirect
 api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$zone_qobj->id);
}
/**
 * Location Deleted
 *
 * @param boolean $deleted Deleted or Undeleted
 */
function location_deleted($deleted){
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 // check
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 // build location query objects
 $zone_qobj=new stdClass();
 $zone_qobj->id=$location_obj->id;
 $zone_qobj->deleted=($deleted?1:0);
 $zone_qobj->updTimestamp=time();
 $zone_qobj->updFkUser=$location_obj->id;
 // debug
 api_dump($_REQUEST);
 api_dump($zone_qobj);
 // update location
 $GLOBALS['database']->queryUpdate("air-conditioning_locations",$zone_qobj);
 // alert
 if($deleted){api_alerts_add(api_text("air-conditioning_alert_locationDeleted"),"warning");}
 else{api_alerts_add(api_text("air-conditioning_alert_locationUndeleted"),"success");}
 // redirect
 api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id);
}
/**
 * Location Remove
 */
function location_remove(){
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($location_obj,"location_obj");
 // delete zones /** @todo non serve ma per completezza si potrebbe fare sia zones che appliances */
 /*$GLOBALS['database']->queryExecute("DELETE FROM `air-conditioning_locations_join_groups` WHERE `fkLocation`='".$location_obj->id."' AND `fkGroup`='".$_REQUEST['idGroup']."'");*/
 // delete location
 $GLOBALS['database']->queryDelete("air-conditioning_locations",$location_obj->id);
 // redirect
 api_alerts_add(api_text("air-conditioning_alert_locationRemoved"),"warning");
 api_redirect("?mod=air-conditioning&scr=locations_list");
}
/**
 * Location Zone Save
 */
function location_zone_save(){
 // get location object
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 // debug
 api_dump($zone_obj,"zone object");
 // build location query objects
 $zone_qobj=new stdClass();
 $zone_qobj->id=$zone_obj->id;
 $zone_qobj->fkLocation=$location_obj->id;
 $zone_qobj->name=addslashes($_REQUEST['name']);
 $zone_qobj->description=addslashes($_REQUEST['description']);
 $zone_qobj->heater_relay=addslashes($_REQUEST['heater_relay']);
 $zone_qobj->cooler_relay=addslashes($_REQUEST['cooler_relay']);
 $zone_qobj->dehumidifier_relay=addslashes($_REQUEST['dehumidifier_relay']);
 $zone_qobj->humidifier_relay=addslashes($_REQUEST['humidifier_relay']);
 // check location
 if($zone_qobj->id){
  // set update properties
  if($_REQUEST['token']=="new"){$zone_qobj->token=md5(date("YmdHis").rand(1,99999));}
  $zone_qobj->updTimestamp=time();
  $zone_qobj->updFkUser=$location_obj->id;
  // debug
  api_dump($zone_qobj,"location query object");
  // execute query
  $GLOBALS['database']->queryUpdate("air-conditioning_locations_zones",$zone_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationZoneUpdated"),"success");
 }else{
  // get maximum position
  $v_order=$GLOBALS['database']->queryCount("air-conditioning_locations_zones","`fkLocation`='".$location_obj->id."'");
  // set add properties
  $zone_qobj->order=($v_order+1);
  $zone_qobj->token=md5(date("YmdHis").rand(1,99999));
  $zone_qobj->addTimestamp=time();
  $zone_qobj->addFkUser=$location_obj->id;
  // debug
  api_dump($zone_qobj,"location query object");
  // execute query
  $zone_qobj->id=$GLOBALS['database']->queryInsert("air-conditioning_locations_zones",$zone_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationZoneCreated"),"success");
 }
 // redirect
 api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$zone_qobj->id);
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