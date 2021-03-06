<?php
/**
 * Air Conditioning - Locations Edit
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="air-conditioning-manage";
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(($zone_obj->id?api_text("locations_edit"):api_text("locations_edit-add")));
 // build location form
 $form=new cForm("?mod=air-conditioning&scr=submit&act=location_save&idLocation=".$location_obj->id,"POST",null,"locations_edit");
 $form->addField("text","name",api_text("locations_edit-ff-name"),$location_obj->name,api_text("locations_edit-ff-name-placeholder"),null,null,null,"required");
 $form->addField("text","description",api_text("locations_edit-ff-description"),$location_obj->description,api_text("locations_edit-ff-description-placeholder"));
 $form->addField("text","latitude",api_text("locations_edit-ff-latitude"),$location_obj->latitude,api_text("locations_edit-ff-latitude-placeholder"));
 $form->addField("text","longitude",api_text("locations_edit-ff-longitude"),$location_obj->longitude,api_text("locations_edit-ff-longitude-placeholder"));
 // controls
 $form->addControl("submit",api_text("form-fc-submit"));
 if($location_obj->id){
  $form->addControl("button",api_text("form-fc-cancel"),"?mod=air-conditioning&scr=locations_manage&idLocation=".$location_obj->id);
  if(!$location_obj->deleted){$form->addControl("button",api_text("form-fc-delete"),"?mod=air-conditioning&scr=submit&act=location_delete&idLocation=".$location_obj->id,"btn-danger",api_text("locations_edit-fc-delete-confirm"));}
  else{
   $form->addControl("button",api_text("form-fc-undelete"),"?mod=air-conditioning&scr=submit&act=location_undelete&idLocation=".$location_obj->id,"btn-warning");
   $form->addControl("button",api_text("form-fc-remove"),"?mod=air-conditioning&scr=submit&act=location_remove&idLocation=".$location_obj->id,"btn-danger",api_text("form-fc-remove-confirm"));
  }
 }else{$form->addControl("button",api_text("form-fc-cancel"),"?mod=air-conditioning&scr=locations_list");}
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($location_obj,"location");}
?>