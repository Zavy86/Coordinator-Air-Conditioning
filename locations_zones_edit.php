<?php
/**
 * Air Conditioning - Locations Zones Edit
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="air-conditioning-locations_manage";
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("framework_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(($zone_obj->id?api_text("locations_zones_edit"):api_text("locations_zones_edit-add")));
 // build location form
 $form=new cForm("?mod=air-conditioning&scr=submit&act=location_zone_save&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"POST",null,"locations_zones_edit");
 $form->addField("static",NULL,api_text("locations_zones_edit-ff-location"),api_tag("strong",$location_obj->name));
 if($zone_obj->id){
  $form->addField("checkbox","token",api_text("locations_zones_edit-ff-token"),NULL,$zone_obj->token);
  $form->addFieldOption("new",api_text("locations_zones_edit-fo-token-new"));
 }
 $form->addField("text","name",api_text("locations_zones_edit-ff-name"),$zone_obj->name,api_text("locations_zones_edit-ff-name-placeholder"),NULL,NULL,NULL,"required");
 $form->addField("text","description",api_text("locations_zones_edit-ff-description"),$zone_obj->description,api_text("locations_zones_edit-ff-description-placeholder"));
 foreach(api_airConditioning_availableAppliances() as $code=>$appliance){
  $field=$code."_relay";
  $form->addField("select",$field,$appliance,$zone_obj->$field,api_text("locations_view-zones_modal-ff-appliance-placeholder"));
  for($relay=1;$relay<=4;$relay++){$form->addFieldOption($relay,api_text("locations_view-zones_modal-fo-relay",$relay));}
 }
 // controls
 $form->addControl("submit",api_text("form-fc-submit"));
 $form->addControl("button",api_text("form-fc-cancel"),"?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$zone_obj->id);
 if($zone_obj->id){$form->addControl("button",api_text("form-fc-delete"),"?mod=air-conditioning&scr=submit&act=location_zone_delete&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"btn-danger",api_text("locations_zones_edit-fc-delete-confirm"));}
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($zone_obj,"zone");}
 if($GLOBALS['debug']){api_dump($location_obj,"location");}
?>