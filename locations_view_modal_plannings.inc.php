<?php
/**
 * Air Conditioning - Locations View - Plannings modal window
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
// plannings list
if(ACTION=="manage_plannings"){
 // build plannings table
 $plannings_table=new cTable();
 $plannings_table->addHeader(api_text("locations_view-plannings-th-day"),"nowarp");
 $plannings_table->addHeader(api_text("locations_view-plannings-th-planning"),null,"100%");
 // cycle all days
 foreach(api_weekly_days() as $day){
  // add table row
  $plannings_table->addRow();
  $plannings_table->addRowField(api_text($day),"nowrap");
  //$plannings_table->addRowField($progressBar->render());
  $plannings_table->addRowField(api_airConditioning_locationZonePlanningDayProgressBar($location_obj,$_REQUEST['idZone'],$day)->render());
  $plannings_table->addRowFieldAction("?mod=air-conditioning&scr=locations_view&act=manage_plannings_edit&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&day=".$day,api_icon("fa-pencil",api_text("locations_view-plannings-td-edit"),"hidden-link"));
  $plannings_table->addRowFieldAction("?mod=air-conditioning&scr=locations_view&act=manage_plannings_clone&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&day=".$day,api_icon("fa-clone",api_text("locations_view-plannings-td-clone"),"hidden-link"));
 }
 // build zone info modal window
 $plannings_modal=new cModal(api_text("locations_view-plannings-modal-title",array($location_obj->name,$selected_zone_obj->name)),null,"locations_view-plannings");
 $plannings_modal->setBody($plannings_table->render());
 // add modal to html object
 $html->addModal($plannings_modal);
 // jQuery scripts
 $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_locations_view-plannings\").modal('show');});");
}
// plannings edit
if(ACTION=="manage_plannings_edit"){
 // get steps of selected day
 $days_array=$selected_zone_obj->plannings[$_REQUEST['day']];
 // build plannings form
 $plannings_form=new cForm("?mod=air-conditioning&scr=submit&act=location_zone_planning_save&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&day=".$_REQUEST['day'],"POST",null,"locations_view_plannings_edit");
 // check if last step of selected day is not defined
 if(!end($days_array)->fkModality){
  $plannings_form->addField("select","fkModality",api_text("locations_view-plannings-ff-fkModality"),null,api_text("locations_view-plannings-ff-fkModality-placeholder"),null,null,null,"required");
  foreach($location_obj->modalities_array as $modality){$plannings_form->addFieldOption($modality->id,$modality->name." (".$modality->temperature."°C)");}
  $plannings_form->addField("time","from",api_text("locations_view-plannings-ff-start"),gmdate("H:i",end($days_array)->time_start),null,null,null,null,null,false);
  $plannings_form->addField("time","end",api_text("locations_view-plannings-ff-end"),"23:59",null,null,null,null,"required");
  /** @todo migliorare... provare con slider */
  $plannings_form->addControl("submit",api_text("form-fc-submit"));
 }
 // count plannings or check if there was only one step not undefined
 if(count($days_array)>1 || (count($days_array)==1 && $days_array[0]->fkModality>0)){$plannings_form->addControl("button",api_text("locations_view-plannings-fc-delete"),"?mod=air-conditioning&scr=submit&act=location_zone_planning_delete&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&day=".$_REQUEST['day'],"btn-danger",api_text("locations_view-plannings-fc-delete-confirm"));}
 // close planning
 $plannings_form->addControl("button",api_text("form-fc-close"),"?mod=air-conditioning&scr=locations_view&act=manage_plannings&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id);
 // build zone info modal window
 $plannings_modal=new cModal(api_text("locations_view-plannings-modal-title-edit",array($location_obj->name,$selected_zone_obj->name,api_text($_REQUEST['day']))),null,"locations_view-plannings_edit");
 $plannings_modal->setBody(api_airConditioning_locationZonePlanningDayProgressBar($location_obj,$_REQUEST['idZone'],$_REQUEST['day'])->render()."<br>".$plannings_form->render(2));

 /** @todo verificare con margnini in api progress bar */

 // add modal to html object
 $html->addModal($plannings_modal);
 // jQuery scripts
 $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_locations_view-plannings_edit\").modal('show');});");
}
// plannings clone
if(ACTION=="manage_plannings_clone"){
 // build plannings form
 $plannings_form=new cForm("?mod=air-conditioning&scr=submit&act=location_zone_planning_clone&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&day=".$_REQUEST['day'],"POST",null,"locations_view_plannings_clone");
 $plannings_form->addField("checkbox","days[]",api_text("locations_view-plannings-ff-days"));
 foreach(api_weekly_days() as $day){$plannings_form->addFieldOption($day,api_text($day),null,null,null,($day==$_REQUEST['day']?false:true));}
 $plannings_form->addControl("submit",api_text("locations_view-plannings-fc-clone"),null,null,api_text("locations_view-plannings-fc-clone-confirm",api_text($_REQUEST['day'])));
 $plannings_form->addControl("button",api_text("form-fc-cancel"),"?mod=air-conditioning&scr=locations_view&act=manage_plannings&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id);
 // build zone info modal window
 $plannings_modal=new cModal(api_text("locations_view-plannings-modal-title-clone",array($location_obj->name,$selected_zone_obj->name,api_text($_REQUEST['day']))),null,"locations_view-plannings_clone");
 $plannings_modal->setBody(api_airConditioning_locationZonePlanningDayProgressBar($location_obj,$_REQUEST['idZone'],$_REQUEST['day'])->render()."<br>".$plannings_form->render(2));
 // add modal to html object
 $html->addModal($plannings_modal);
 // jQuery scripts
 $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_locations_view-plannings_clone\").modal('show');});");

 /** @todo validation su array */

}
// plannings clone
if(ACTION=="manage_plannings_manual"){
 // get current day
 $v_day=(strtolower(date("l")));
 // build plannings form
 $plannings_form=new cForm("?mod=air-conditioning&scr=submit&act=location_zone_planning_manual&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id,"POST",null,"locations_view_plannings_manual");
 $plannings_form->addField("number","temperature",api_text("locations_view-plannings-ff-temperature"),$selected_zone_obj->manual_temperature,api_text("locations_view-plannings-ff-temperature-placeholder"),null,null,null,"step='0.5' required");
 $plannings_form->addField("select","duration",api_text("locations_view-plannings-ff-duration"),null/** @todo inserire la durata di default */,api_text("locations_view-plannings-ff-duration-placeholder"),null,null,null,"required");
 $plannings_form->addFieldOption(3600,"1 ".api_text("hour"));
 $plannings_form->addFieldOption(7200,"2 ".api_text("hours"));
 $plannings_form->addFieldOption(10800,"3 ".api_text("hours"));
 $plannings_form->addControl("submit",api_text("form-fc-submit"));
 $plannings_form->addControl("button",api_text("locations_view-plannings-fc-cancel"),"?mod=air-conditioning&scr=submit&act=location_zone_planning_manual&disable=1&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id,"btn-warning",api_text("locations_view-plannings-fc-cancel-confirm"));
 $plannings_form->addControl("button",api_text("locations_view-plannings-fc-edit",api_text($v_day)),"?mod=air-conditioning&scr=locations_view&act=manage_plannings_edit&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&day=".$v_day);
 // build zone info modal window
 $plannings_modal=new cModal(api_text("locations_view-plannings-modal-title-manual",array($location_obj->name,$selected_zone_obj->name)),null,"locations_view-plannings_clone");
 $plannings_modal->setBody($plannings_form->render(2));
 // add modal to html object
 $html->addModal($plannings_modal);
 // jQuery scripts
 $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_locations_view-plannings_clone\").modal('show');});");
}
?>