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
 $location_qobj=new stdClass();
 $location_qobj->id=$location_obj->id;
 $location_qobj->name=addslashes($_REQUEST['name']);
 $location_qobj->description=addslashes($_REQUEST['description']);
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
 api_redirect("?mod=air-conditioning&scr=locations_view&idLocation=".$location_qobj->id);
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

?>