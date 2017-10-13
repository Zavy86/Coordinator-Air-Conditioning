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
 $plannings_table->addHeader(api_text("locations_view-plannings-th-planning"),NULL,"100%");
 // cycle all days
 foreach(api_weekly_days() as $day){
  // add table row
  $plannings_table->addRow();
  $plannings_table->addRowField(api_text($day),"nowrap");
  //$plannings_table->addRowField($progressBar->render());
  $plannings_table->addRowField(api_airConditioning_locationZonePlanningDayProgressBar($location_obj,$_REQUEST['idZone'],$day)->render());
  $plannings_table->addRowFieldAction("?mod=air-conditioning&scr=locations_view&act=manage_plannings_edit&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&day=".$day,api_icon("fa-pencil",api_text("locations_view-plannings-td-edit"),"hidden-link"));
 }
 // build zone info modal window
 $plannings_modal=new cModal(api_text("locations_view-plannings-modal-title",$location_obj->name),NULL,"locations_view-plannings");
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
 $plannings_form=new cForm("?mod=air-conditioning&scr=submit&act=location_zone_planning_save&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&day=".$_REQUEST['day'],"POST",NULL,"locations_view_plannings");
 // check if last step of selected day is not defined
 if(!end($days_array)->fkModality){
  $plannings_form->addField("select","fkModality",api_text("locations_view-plannings-ff-fkModality"));
  $plannings_form->addFieldOption("",api_text("locations_view-plannings-ff-fkModality-select"));
  foreach($location_obj->modalities_array as $modality){$plannings_form->addFieldOption($modality->id,$modality->name." (".$modality->temperature."°C)");}
  $plannings_form->addField("time","from",api_text("locations_view-plannings-ff-start"),gmdate("H:i",end($days_array)->time_start),null,null,null,null,null,false);
  $plannings_form->addField("time","end",api_text("locations_view-plannings-ff-end"),"23:59");
  /** @todo migliorare... provare con slider */
  $plannings_form->addControl("submit",api_text("form-fc-submit"));
 }
 // count plannings edit modal window
 if(count($days_array)>1){$plannings_form->addControl("button",api_text("locations_view-plannings-fc-delete"),"?mod=air-conditioning&scr=submit&act=location_zone_planning_delete&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&day=".$_REQUEST['day'],"btn-danger",api_text("locations_view-plannings-fc-delete-confirm"));}
 // close planning
 $plannings_form->addControl("button",api_text("form-fc-close"),"?mod=air-conditioning&scr=locations_view&act=manage_plannings&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id);
 // build zone info modal window
 $plannings_modal=new cModal(api_text("locations_view-plannings-modal-title-edit",array($location_obj->name,api_text($_REQUEST['day']))),NULL,"locations_view-plannings");
 $plannings_modal->setBody(api_airConditioning_locationZonePlanningDayProgressBar($location_obj,$_REQUEST['idZone'],$_REQUEST['day'])->render()."<br>".$plannings_form->render(2));

 /** @todo verificare con margnini in api progress bar */

 // add modal to html object
 $html->addModal($plannings_modal);
 // jQuery scripts
 $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_locations_view-plannings\").modal('show');});");

 /** @todo validation */

}
?>