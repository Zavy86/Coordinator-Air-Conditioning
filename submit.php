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
 // locations
 case "location_save":location_save();break;
 case "location_delete":location_deleted(true);break;
 case "location_undelete":location_deleted(false);break;
 case "location_remove":location_remove();break;
 // locations modalities
 case "location_modality_save":location_modality_save();break;
 case "location_modality_delete":location_modality_delete();break;
 // locations zones
 case "location_zone_save":location_zone_save();break;
 case "location_zone_move_up":location_zone_move("up");break;
 case "location_zone_move_down":location_zone_move("down");break;
 case "location_zone_delete":location_zone_delete();break;
 // locations zones planning
 case "location_zone_planning_save":location_zone_planning_save();break;
 case "location_zone_planning_clone":location_zone_planning_clone();break;
 case "location_zone_planning_delete":location_zone_planning_delete();break;
 case "location_zone_planning_manual":location_zone_planning_manual();break;
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

 /** @todo check authorizations */

 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($location_obj,"location object");
 // build location query objects
 $location_qobj=new stdClass();
 $location_qobj->id=$location_obj->id;
 $location_qobj->name=addslashes($_REQUEST['name']);
 $location_qobj->description=addslashes($_REQUEST['description']);
 $location_qobj->latitude=$_REQUEST['latitude'];
 $location_qobj->longitude=$_REQUEST['longitude'];
 // check location
 if($location_obj->id){
  // update location
  $location_qobj->updTimestamp=time();
  $location_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($location_qobj,"location query object");
  // execute query
  $GLOBALS['database']->queryUpdate("air-conditioning_locations",$location_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationUpdated"),"success");
 }else{
  // insert location
  $location_qobj->addTimestamp=time();
  $location_qobj->addFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($location_qobj,"location query object");
  // execute query
  $location_qobj->id=$GLOBALS['database']->queryInsert("air-conditioning_locations",$location_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationCreated"),"success");
 }
 // redirect
 api_redirect("?mod=air-conditioning&scr=locations_manage&idLocation=".$location_qobj->id);
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

 /** @todo check authorizations */

 // build location query objects
 $location_qobj=new stdClass();
 $location_qobj->id=$location_obj->id;
 $location_qobj->deleted=($deleted?1:0);
 $location_qobj->updTimestamp=time();
 $location_qobj->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($_REQUEST);
 api_dump($location_qobj);
 // update location
 $GLOBALS['database']->queryUpdate("air-conditioning_locations",$location_qobj);
 // alert
 if($deleted){api_alerts_add(api_text("air-conditioning_alert_locationDeleted"),"warning");}
 else{api_alerts_add(api_text("air-conditioning_alert_locationUndeleted"),"success");}
 // redirect
 api_redirect("?mod=air-conditioning&scr=locations_manage&idLocation=".$location_obj->id);
}
/**
 * Location Remove
 */
function location_remove(){
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}

 /** @todo check authorizations */

 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($location_obj,"location_obj");
 // delete location
 $GLOBALS['database']->queryDelete("air-conditioning_locations",$location_obj->id);
 // redirect
 api_alerts_add(api_text("air-conditioning_alert_locationRemoved"),"warning");
 api_redirect("?mod=air-conditioning&scr=locations_list");
}

/**
 * Location Modality Save
 */
function location_modality_save(){
 // get location object
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $modality_obj=$location_obj->modalities_array[$_REQUEST['idModality']];
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");} /** @todo verificare e spostare su nuova pagina */

 /** @todo check location authorizations */

 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($modality_obj,"modality object");
 // build location query objects
 $modality_qobj=new stdClass();
 $modality_qobj->id=$modality_obj->id;
 $modality_qobj->fkLocation=$location_obj->id;
 $modality_qobj->name=addslashes($_REQUEST['name']);
 $modality_qobj->temperature=$_REQUEST['temperature'];
 $modality_qobj->color=addslashes($_REQUEST['color']);
 // check location
 if($modality_qobj->id){
  // set update properties
  $modality_qobj->updTimestamp=time();
  $modality_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($modality_qobj,"modality query object");
  // execute query
  $GLOBALS['database']->queryUpdate("air-conditioning_locations_modalities",$modality_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationModalityUpdated"),"success");
 }else{
  // set insert properties
  $modality_qobj->addTimestamp=time();
  $modality_qobj->addFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($modality_qobj,"modality query object");
  // execute query
  $modality_qobj->id=$GLOBALS['database']->queryInsert("air-conditioning_locations_modalities",$modality_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationModalityCreated"),"success");
 }
 // redirect
 api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$_REQUEST['idZone']);
}
/**
 * Location Modality Delete
 */
function location_modality_delete(){ /** @todo verificare se tenere delete o mettere remove */
 // get location object
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $modality_obj=$location_obj->modalities_array[$_REQUEST['idModality']];
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");} /** @todo verificare e spostare su nuova pagina */
 if(!$modality_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationModalityNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$_REQUEST['idZone']);}

 /** @todo check location authorizations */

 /** @todo check if modality is in use */

 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($modality_obj,"modality object");
 // delete zone
 $GLOBALS['database']->queryDelete("air-conditioning_locations_modalities",$modality_obj->id);
 // redirect
 api_alerts_add(api_text("air-conditioning_alert_modalityDeleted"),"warning");
 api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$_REQUEST['idZone']);
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

 /** @todo check authorizations */

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
  $zone_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($zone_qobj,"zone query object");
  // execute query
  $GLOBALS['database']->queryUpdate("air-conditioning_locations_zones",$zone_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationZoneUpdated"),"success");
 }else{
  // get maximum position
  $v_order=$GLOBALS['database']->queryCount("air-conditioning_locations_zones","`fkLocation`='".$location_obj->id."'");
  // set insert properties
  $zone_qobj->order=($v_order+1);
  $zone_qobj->token=md5(date("YmdHis").rand(1,99999));
  $zone_qobj->plannings='{"sunday":[{"time_start":0,"time_end":86399,"fkModality":0}],"monday":[{"time_start":0,"time_end":86399,"fkModality":0}],"tuesday":[{"time_start":0,"time_end":86399,"fkModality":0}],"wednesday":[{"time_start":0,"time_end":86399,"fkModality":0}],"thursday":[{"time_start":0,"time_end":86399,"fkModality":0}],"friday":[{"time_start":0,"time_end":86399,"fkModality":0}],"saturday":[{"time_start":0,"time_end":86399,"fkModality":0}]}';
  $zone_qobj->addTimestamp=time();
  $zone_qobj->addFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($zone_qobj,"zone query object");
  // execute query
  $zone_qobj->id=$GLOBALS['database']->queryInsert("air-conditioning_locations_zones",$zone_qobj);
  api_alerts_add(api_text("air-conditioning_alert_locationZoneCreated"),"success");
 }
 // redirect
 api_redirect("?mod=air-conditioning&scr=locations_manage&idLocation=".$location_obj->id."&idZone=".$zone_qobj->id);
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
 if(!$zone_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationZoneNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_manage&idLocation=".$location_obj->id);}
 // check parameters
 if(!in_array(strtolower($direction),array("up","down"))){api_alerts_add(api_text("dashboard_alert_zoneError"),"warning");api_redirect("?mod=dashboard&scr=dashboard_customize&idTile=".$zone_obj->id);}

 /** @todo check authorizations */

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
 api_redirect("?mod=air-conditioning&scr=locations_manage&idLocation=".$location_obj->id."&idZone=".$zone_obj->id);
}
/**
 * Location Zone Delete
 */
function location_zone_delete(){ /** @todo verificare se tenere delete o mettere remove */
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 if(!$zone_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationZoneNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_manage&idLocation=".$location_obj->id);}

 /** @todo check authorizations */

 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($zone_obj,"zone_obj");
 // delete zone
 $GLOBALS['database']->queryDelete("air-conditioning_locations_zones",$zone_obj->id);
 // redirect
 api_alerts_add(api_text("air-conditioning_alert_locationDeleted"),"warning");
 api_redirect("?mod=air-conditioning&scr=locations_manage&idLocation=".$location_obj->id);
}

/**
 * Location Zone Planning Save
 */
function location_zone_planning_save(){
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];

 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 if(!$zone_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationZoneNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id);}

 /** @todo check authorizations */

 // debug
 api_dump($_REQUEST,"_REQUEST");

 // acquire variables
 $r_day=$_REQUEST['day'];
 $r_end=$_REQUEST['end'];
 $r_fkModality=$_REQUEST['fkModality'];

 // check variables
 if(!$r_day||!strpos($r_end,":")||!$r_fkModality){api_alerts_add(api_text("air-conditioning_alert_locationZonePlanningError"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&act=manage_plannings_edit&idLocation=".$location_obj->id."&idZone=".$_REQUEST['idZone']."&idPlanning=".$_REQUEST['idPlanning']."&day=".$_REQUEST['day']);}

 // make time end
 $v_end_array=explode(":",$r_end);
 $v_time_end=(($v_end_array[0]*3600)+($v_end_array[1]*60)+59);
 if($v_time_end>86399){$v_time_end=86399;}

 // clone days array
 $days_array=$zone_obj->plannings;
 // get last step of selected day
 $last_step=end($days_array[$r_day]);
 //debug
 api_dump($last_step,"last step");

 // check last step
 //

 // check if time end is up to time start
 if($v_time_end<$last_step->time_start){api_alerts_add(api_text("air-conditioning_alert_locationZonePlanningError"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&act=manage_plannings_edit&idLocation=".$location_obj->id."&idZone=".$_REQUEST['idZone']."&idPlanning=".$_REQUEST['idPlanning']."&day=".$_REQUEST['day']);}

 // update last step of day
 $last_step->time_end=$v_time_end;
 $last_step->fkModality=$r_fkModality;
 //debug
 api_dump($last_step,"last step updated");
 // check if time end is lower than 86399 (23:59)
 if($last_step->time_end<86399){
  // build step object
  $step_obj=new stdClass();
  $step_obj->time_start=$v_time_end+1;
  $step_obj->time_end=86399;
  $step_obj->fkModality=0;
  api_dump($step_obj,"new last step object");
  // add step object to selected day
  $days_array[$r_day][]=$step_obj;
 }
 //debug
 api_dump($days_array[$r_day],"days_array ".$r_day);
 // build zone query objects
 $zone_qobj=new stdClass();
 $zone_qobj->id=$zone_obj->id;
 $zone_qobj->plannings=json_encode($days_array);
 $zone_qobj->updTimestamp=time();
 $zone_qobj->updFkUser=$GLOBALS['session']->user->id;
 //debug
 api_dump($zone_qobj,"zone query object");
 // execute query
 $GLOBALS['database']->queryUpdate("air-conditioning_locations_zones",$zone_qobj);
 // make redirect action
 if(end($days_array[$r_day])->fkModality){$redirect_act="manage_plannings";}else{$redirect_act="manage_plannings_edit";}
 // redirect
 api_alerts_add(api_text("air-conditioning_alert_locationZonePlanningUpdated"),"success");
 api_redirect("?mod=air-conditioning&scr=locations_view&act=".$redirect_act."&idLocation=".$location_obj->id."&idZone=".$zone_obj->id."&day=".$_REQUEST['day']);
}
/**
 * Location Zone Planning Clone
 */
function location_zone_planning_clone(){
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];

 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 if(!$zone_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationZoneNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id);}

 /** @todo check authorizations */

 // debug
 api_dump($_REQUEST,"_REQUEST");
 // acquire variables
 $r_day=$_REQUEST['day'];
 $r_days=$_REQUEST['days'];
 // check variables
 if(!$r_day || !is_array($r_days)){api_alerts_add(api_text("air-conditioning_alert_locationZonePlanningError"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&zone=".$zone_obj->id);}
 // clone days from plannings
 $days_array=$zone_obj->plannings;
 // debug
 api_dump($days_array,"days array");
 // cycle all selected days and replace with selected day
 foreach($r_days as $day_fo){$days_array[$day_fo]=$days_array[$r_day];}
 // debug
 api_dump($days_array,"days array updated");
 // build zone query objects
 $zone_qobj=new stdClass();
 $zone_qobj->id=$zone_obj->id;
 $zone_qobj->plannings=json_encode($days_array);
 $zone_qobj->updTimestamp=time();
 $zone_qobj->updFkUser=$GLOBALS['session']->user->id;
 //debug
 api_dump($zone_qobj,"zone query object");
 // execute query
 $GLOBALS['database']->queryUpdate("air-conditioning_locations_zones",$zone_qobj);
 // redirect
 api_alerts_add(api_text("air-conditioning_alert_locationZonePlanningUpdated"),"success");
 api_redirect("?mod=air-conditioning&scr=locations_view&act=manage_plannings&idLocation=".$location_obj->id."&idZone=".$zone_obj->id."&day=".$_REQUEST['day']);
}
/**
 * Location Zone Planning Delete
 */
function location_zone_planning_delete(){ /** @todo vedere se tenere delete o se chiamare in altro modo */
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];

 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 if(!$zone_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationZoneNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id);}

 /** @todo check authorizations */

 // debug
 api_dump($_REQUEST,"_REQUEST");
 // acquire variables
 $r_day=$_REQUEST['day'];
 // clone days array
 $days_array=$zone_obj->plannings;
 // get last step of selected day
 $last_step=end($days_array[$r_day]);
 //debug
 api_dump($last_step,"last step");
 // check if last step of selected day is defined
 if($last_step->fkModality){
  // set last step to not defined
  $last_step->fkModality=0;
  //debug
  api_dump($last_step,"last step updated");
 }else{
  // remove last step from selected day (the undefined)
  array_pop($days_array[$r_day]);
  // get last step of selected day
  $last_step=end($days_array[$r_day]);
  $last_step->time_end=86399;
  $last_step->fkModality=0;
  // debug
  api_dump($last_step,"last step updated");
 }
 //debug
 api_dump($days_array[$r_day],"days_array ".$r_day);
 // build zone query objects
 $zone_qobj=new stdClass();
 $zone_qobj->id=$zone_obj->id;
 $zone_qobj->plannings=json_encode($days_array);
 $zone_qobj->updTimestamp=time();
 $zone_qobj->updFkUser=$GLOBALS['session']->user->id;
 //debug
 api_dump($zone_qobj,"zone query object");
 // execute query
 $GLOBALS['database']->queryUpdate("air-conditioning_locations_zones",$zone_qobj);
 // redirect
 api_alerts_add(api_text("air-conditioning_alert_locationZonePlanningUpdated"),"success");
 api_redirect("?mod=air-conditioning&scr=locations_view&act=manage_plannings_edit&idLocation=".$location_obj->id."&idZone=".$zone_obj->id."&day=".$_REQUEST['day']);
}
/**
 * Location Zone Planning Manual
 */
function location_zone_planning_manual(){
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];

 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 if(!$zone_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationZoneNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id);}

 /** @todo check authorizations */

 // debug
 api_dump($_REQUEST,"_REQUEST");
 // acquire variables
 $r_temperature=$_REQUEST['temperature'];
 $r_duration=$_REQUEST['duration'];
 $r_disable=$_REQUEST['disable'];
 // build zone query objects
 $zone_qobj=new stdClass();
 $zone_qobj->id=$zone_obj->id;
 $zone_qobj->updTimestamp=time();
 $zone_qobj->updFkUser=$GLOBALS['session']->user->id;
 // check for disable
 if($r_disable){
  // disable manual
  $zone_qobj->manual_timestamp=null;
 }else{
  // check variables
  if(!$r_temperature || !$r_duration){api_alerts_add(api_text("air-conditioning_alert_locationZonePlanningError"),"danger");api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&zone=".$zone_obj->id);}
  // set new manual temperature and calculate timestamp
  $zone_qobj->manual_temperature=$r_temperature;
  $zone_qobj->manual_timestamp=$r_duration+time();
 }
 //debug
 api_dump($zone_qobj,"zone query object");
 // execute query
 $GLOBALS['database']->queryUpdate("air-conditioning_locations_zones",$zone_qobj);
 // redirect
 api_alerts_add(api_text("air-conditioning_alert_locationZonePlanningUpdated"),"success");
 api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$zone_obj->id);
}

?>