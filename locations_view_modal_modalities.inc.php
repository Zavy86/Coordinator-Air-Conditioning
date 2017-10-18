<?php
/**
 * Air Conditioning - Locations View - Modalities modal window
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
// modalities list
if(ACTION=="manage_modalities"){
 // build modalities table
 $modalities_table=new cTable();
 $modalities_table->addHeader("&nbsp;",null,16);
 $modalities_table->addHeader(api_text("locations_view-modalities-th-temperature"),"nowrap");
 $modalities_table->addHeader(api_text("locations_view-modalities-th-modality"),null,"100%");
 foreach($location_obj->modalities_array as $modality){
  $modalities_table->addRow();
  $modalities_table->addRowField($modality->icon,"nowrap");
  $modalities_table->addRowField($modality->temperature."°C","nowrap");
  $modalities_table->addRowField($modality->name,"truncate-ellipsis");
  $modalities_table->addRowFieldAction("?mod=air-conditioning&scr=locations_view&act=manage_modalities_edit&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&idModality=".$modality->id,api_icon("fa-pencil",api_text("locations_view-modalities-td-edit"),"hidden-link"));
 }
 $modalities_table->addRow();
 $modalities_table->addRowField(api_text("locations_view-modalities-td-add"),"nowrap",null,"colspan='3'");
 $modalities_table->addRowFieldAction("?mod=air-conditioning&scr=locations_view&act=manage_modalities_edit&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id,api_icon("fa-plus",api_text("locations_view-modalities-td-add"),"hidden-link"));
 // build zone info modal window
 $modalities_modal=new cModal(api_text("locations_view-modalities-modal-title",$location_obj->name),null,"locations_view-modalities");
 $modalities_modal->setBody($modalities_table->render());
 // add modal to html object
 $html->addModal($modalities_modal);
 // jQuery scripts
 $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_locations_view-modalities\").modal('show');});");
}
// modalities edit
if(ACTION=="manage_modalities_edit"){
 // get objects
 $selected_modality_obj=$location_obj->modalities_array[$_REQUEST['idModality']];
 // build modalities form
 $modalities_form=new cForm("?mod=air-conditioning&scr=submit&act=location_modality_save&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&idModality=".$selected_modality_obj->id,"POST",null,"locations_view_modalities");
 $modalities_form->addField("text","name",api_text("locations_view-modalities-ff-name"),$selected_modality_obj->name,api_text("locations_view-modalities-ff-name-placeholder"),null,null,null,"required");
 $modalities_form->addField("number","temperature",api_text("locations_view-modalities-ff-temperature"),$selected_modality_obj->temperature,api_text("locations_view-modalities-ff-temperature-placeholder"),null,null,null,"step='0.5' required");
 $modalities_form->addField("text","color",api_text("locations_view-modalities-ff-color"),$selected_modality_obj->color,api_text("locations_view-modalities-ff-color-placeholder"),null,null,null,"required");
 $modalities_form->addControl("submit",api_text("form-fc-submit"));
 $modalities_form->addControl("button",api_text("form-fc-cancel"),"?mod=air-conditioning&scr=locations_view&act=manage_modalities&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id);
 $modalities_form->addControl("button",api_text("form-fc-delete"),"?mod=air-conditioning&scr=submit&act=location_modality_delete&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id."&idModality=".$selected_modality_obj->id,"btn-danger",api_text("locations_view-modalities-fc-delete-confirm"));
 // build zone info modal window
 $modalities_modal=new cModal(api_text("locations_view-modalities-modal-title-".($selected_modality_obj->id?"edit":"add"),$location_obj->name),null,"locations_view-modalities");
 $modalities_modal->setBody($modalities_form->render(2));
 // add modal to html object
 $html->addModal($modalities_modal);
 // jQuery scripts
 $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_locations_view-modalities\").modal('show');});");
}
?>