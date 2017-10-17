<?php
/**
 * Framework - Template
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */

 // check authorizations /** @todo fare API */
 if($authorization){if(!api_checkAuthorization(MODULE,$authorization)){api_alerts_add(api_text("alert_unauthorized",array(MODULE,$authorization)),"danger");api_redirect("?mod=air-conditioning&scr=dashboard");}}

 // build html object
 $html=new cHTML($module_name);
 // build nav object
 $nav=new cNav("nav-tabs");
 $nav->setTitle(api_text("air-conditioning"));

 // dashboard
 $nav->addItem(api_icon("fa-th-large",null,"test hidden-link"),"?mod=air-conditioning&scr=dashboard");

 // locations
 if(substr(SCRIPT,0,9)=="locations"){

  // selected location /** @todo integrare bene */
  if($location_obj->id && SCRIPT=="locations_view"){
   $nav->addItem($location_obj->name,null,null,"active");
   $nav->addSubItem(api_text("nav-locations-operations-modalities"),"?mod=air-conditioning&scr=locations_modalities_list&idLocation=".$location_obj->id);
   if($selected_zone_obj->id){
    $nav->addSubHeader(api_text("nav-locations-zones-operations"));
    $nav->addSubItem(api_text("nav-locations-zones-operations-plannings"),"?mod=air-conditioning&scr=locations_zones_plannings_list&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id);
   }
  }

  // locations view or edit
  if(in_array(SCRIPT,array("locations_manage","locations_edit","locations_zones_edit")) && $location_obj->id){
   // locations operations
   $nav->addItem(api_text("nav-operations"),null,null,"active");
   // locations view operations
   if(SCRIPT=="locations_manage"){  /** @todo check authorizations */
    // check for deleted
    if($location_obj->deleted){
     $nav->addSubItem(api_text("nav-locations-operations-undelete"),"?mod=air-conditioning&scr=submit&act=location_undelete&idLocation=".$location_obj->id,true,api_text("nav-locations-operations-undelete-confirm"));
     $nav->addSubItem(api_text("nav-locations-operations-remove"),"?mod=air-conditioning&scr=submit&act=location_remove&idLocation=".$location_obj->id,true,api_text("nav-locations-operations-remove-confirm"));
    }else{
     $nav->addSubItem(api_text("nav-locations-operations-edit"),"?mod=air-conditioning&scr=locations_edit&idLocation=".$location_obj->id);
     $nav->addSubItem(api_text("nav-locations-operations-zone_add"),"?mod=air-conditioning&scr=locations_zones_edit&idLocation=".$location_obj->id);
    }
   }
  }
 }

 // management
 if(api_checkAuthorization(MODULE,"air-conditioning-locations_manage")){
  $nav->addItem(api_text("nav-management"));
  $nav->addSubItem(api_text("locations_list"),"?mod=air-conditioning&scr=locations_list");
  $nav->addSubItem(api_text("nav-locations-add"),"?mod=air-conditioning&scr=locations_edit");

  /** @todo impostazioni per temperatura minima (forse pero è meglio per zona) */

 }

 // add nav to html
 $html->addContent($nav->render(false));
?>