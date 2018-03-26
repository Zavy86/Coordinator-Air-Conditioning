<?php
/**
 * Air Conditioning - Dashboard
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("air-conditioning"));
 // build dashboard object
 $dashboard=new cDashboard();
 // cycle all locations
 foreach(api_airConditioning_locations() as $location_obj){
  $location_zones=null;
  foreach($location_obj->zones_array as $zone_obj){
   $last_detection=$zone_obj->getDetections(1)[0];
   // che if last detection is not oldest than 15 minutes
   if((time()-$last_detection->timestamp)<900){
    $detection=api_text("dashboard-location-detection",array(round($last_detection->temperature,1),round($last_detection->humidity)));
   }else{
    $detection=api_text("dashboard-location-detection_offline");
   }
   $location_zones.="<br>".$zone_obj->name.": ".$detection;
  }
  $dashboard->addTile("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id,$location_obj->name,substr($location_zones,4),true,"1x1","fa-thermometer-three-quarters");
 }
 // manage locations
 $dashboard->addTile("?mod=air-conditioning&scr=locations_list",api_text("dashboard-manage"),api_text("dashboard-manage-description"),(api_checkAuthorization(MODULE,"air-conditioning-manage")),"1x1","fa-bars");
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($dashboard->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>