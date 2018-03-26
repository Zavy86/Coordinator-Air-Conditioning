<?php
/**
 * Air Conditioning - Locations Manage
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="air-conditioning-manage";
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $selected_zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("air-conditioning_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("locations_manage"));

       // deleted alert
       if($location_obj->deleted){api_alerts_add(api_text("locations_manage-deleted-alert"),"warning");}

 // build zones table
 $zones_table=new cTable(api_text("locations_manage-zones-tr-unvalued"));
 // cycle location zones
 foreach($location_obj->zones_array as $zone_obj){
  // check selected
  if($zone_obj->id==$selected_zone_obj->id){$tr_class="info";}else{$tr_class=null;}
  // build operations button
  $ob_obj=new cOperationsButton();
  $ob_obj->addElement("?mod=air-conditioning&scr=locations_manage&act=zone_info&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-info-circle",api_text("locations_manage-zones-td-info"));
  $ob_obj->addElement("?mod=air-conditioning&scr=locations_zones_edit&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-pencil",api_text("locations_manage-zones-td-edit"));
  $ob_obj->addElement("?mod=air-conditioning&scr=submit&act=location_zone_move_up&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-arrow-up",api_text("locations_manage-zones-td-move-up"),($zone_obj->order>1?true:false));
  $ob_obj->addElement("?mod=air-conditioning&scr=submit&act=location_zone_move_down&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-arrow-down",api_text("locations_manage-zones-td-move-down"),($zone_obj->order<count($location_obj->zones_array)?true:false));
  $ob_obj->addElement("?mod=air-conditioning&scr=submit&act=location_zone_delete&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-trash",api_text("locations_manage-zones-td-delete"),true,api_text("locations_manage-zones-td-delete-confirm"));
  // add zone row
  $zones_table->addRow($tr_class);
  $zones_table->addRowField($zone_obj->name,"nowrap");
  $zones_table->addRowField($zone_obj->getAppliances("&nbsp;",true,false),"nowrap");
  $zones_table->addRowField($zone_obj->description,"truncate-ellipsis");
  $zones_table->addRowField($ob_obj->render(),"text-right");
 }

      /** @todo authorization for single user in single zone */

      // build authorizations table
      $authorizations_table=new cTable(api_text("locations_manage-zones-tr-unvalued"));
      // cycle location authorizations
      foreach($location_obj->zones_array as $zone_obj){
       // add authorization row
       $authorizations_table->addRow();
       $authorizations_table->addRowField($zone_obj->name,"nowrap");
       $authorizations_table->addRowField($zone_obj->name,"nowrap");
       $authorizations_table->addRowFieldAction("#",api_icon("fa-trash",api_text("locations_manage-authorizations-td-delete"),"hidden-link"),true,api_text("locations_manage-authorizations-td-delete-confirm"));
      }

 // make coordinates
 if($location_obj->latitude && $location_obj->longitude){
  $coordinates_dd=round($location_obj->latitude,2)."N, ".round($location_obj->longitude,2)."E";
  $coordinates_dd.=api_link("https://www.google.com/maps/?q=".$location_obj->latitude.",".$location_obj->longitude,api_icon("fa-crosshairs"),null,null,false,null,null,null,"_blank");
 }

 // build left location description list
 $dl_left=new cDescriptionList("br","dl-horizontal");
 $dl_left->addElement(api_text("locations_manage-dt-name"),api_tag("strong",$location_obj->name));
 $dl_left->addElement(api_text("locations_manage-dt-description"),$location_obj->description);
 if($coordinates_dd){$dl_left->addElement(api_text("locations_manage-dt-coordinates"),$coordinates_dd);}

 // build right location description list
 $dl_right=new cDescriptionList("br","dl-horizontal");
 $dl_right->addElement(api_text("locations_manage-dt-zones"),$zones_table->render());

      //$dl_right->addElement(api_text("locations_manage-dt-authorizations"),$authorizations_table->render());

 // check for action
 if(ACTION=="zone_info"){
  // build zone info description list
  $zone_info_dl=new cDescriptionList("br","dl-horizontal");
  if($selected_zone_obj->description){$zone_info_dl->addElement(api_text("locations_manage-zone_info-dt-description"),$selected_zone_obj->description);}
  $zone_info_dl->addElement(api_text("locations_manage-zone_info-dt-token"),$selected_zone_obj->token);
  if(count($selected_zone_obj->appliances_array)){$zone_info_dl->addElement(api_text("locations_manage-zone_info-dt-appliances"),$selected_zone_obj->getAppliances("<br>"));}
  // build zone info modal window
  $zone_info_modal=new cModal($selected_zone_obj->name,null,"locations_manage-zone_info_modal");
  $zone_info_modal->setBody($zone_info_dl->render());
  // add modal to html object
  $html->addModal($zone_info_modal);
  // jQuery scripts
  $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_locations_manage-zone_info_modal\").modal('show');});");
 }
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($dl_left->render(),"col-xs-12 col-sm-4");
 $grid->addCol($dl_right->render(),"col-xs-12 col-sm-8");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($location_obj,"location object");}
?>