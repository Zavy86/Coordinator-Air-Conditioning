<?php
/**
 * Framework - Template
 *
 * @package Coordinator\Modules\Settings
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
 $nav->addItem(api_icon("fa-th-large",NULL,"test hidden-link"),"?mod=air-conditioning&scr=dashboard");

 // locations
 if(substr(SCRIPT,0,9)=="locations"){
  // lists
  $nav->addItem(api_text("locations_list"),"?mod=air-conditioning&scr=locations_list");
  // locations view or edit
  if(in_array(SCRIPT,array("locations_view","locations_edit"))){
   // locations view operations
   if(SCRIPT=="locations_view"){  /** @todo check authorizations */
    $nav->addItem(api_text("nav-operations"),NULL,NULL,"active");
    // check for deleted
    if($location_obj->deleted){
     $nav->addSubItem(api_text("nav-locations-operations-undelete"),"?mod=air-conditioning&scr=submit&act=location_undelete&idLocation=".$location_obj->id,TRUE,api_text("nav-locations-operations-undelete-confirm"));
     $nav->addSubItem(api_text("nav-locations-operations-remove"),"?mod=air-conditioning&scr=submit&act=location_remove&idLocation=".$location_obj->id,TRUE,api_text("nav-locations-operations-remove-confirm"));
    }else{
     $nav->addSubItem(api_text("nav-locations-operations-edit"),"?mod=air-conditioning&scr=locations_edit&idLocation=".$location_obj->id);
     $nav->addSubItem(api_text("nav-locations-operations-zone_add"),"?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&act=zone_add");
    }
   }
   // locations edit
   if(SCRIPT=="locations_edit"){$nav->addItem(api_text("locations_edit"),"?mod=air-conditioning&scr=locations_edit");}
  }else{
   // locations add
   $nav->addItem(api_text("nav-locations-add"),"?mod=air-conditioning&scr=locations_edit");
  }
 }

 // add nav to html
 $html->addContent($nav->render(FALSE));
?>