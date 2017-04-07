<?php
/**
 * Air Conditioning - Locations Edit
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="air-conditioning-locations_manage";
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("locations_edit"));
 // build location form
 $form=new cForm("?mod=air-conditioning&scr=submit&act=location_save&idLocation=".$location_obj->id,"POST",null,"locations_edit");
 $form->addField("text","name",api_text("locations_edit-ff-name"),$location_obj->name,api_text("locations_edit-ff-name-placeholder"),NULL,NULL,NULL,"required");
 $form->addField("text","description",api_text("locations_edit-ff-description"),$location_obj->description,api_text("locations_edit-ff-description-placeholder"));
 // controls
 $form->addControl("submit",api_text("form-fc-submit"));
 if($location_obj->id){
  $form->addControl("button",api_text("form-fc-cancel"),"?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id);
  if(!$location_obj->deleted){$form->addControl("button",api_text("form-fc-delete"),"?mod=air-conditioning&scr=submit&act=location_delete&idLocation=".$location_obj->id,"btn-danger",api_text("locations_edit-fc-delete-confirm"));}
  else{$form->addControl("button",api_text("form-fc-undelete"),"?mod=air-conditioning&scr=submit&act=location_undelete&idLocation=".$location_obj->id,"btn-warning");}
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