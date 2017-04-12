<?php
/**
 * Air Conditioning - Locations View
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // get objects
 $location_obj=new cAirConditioningLocation($_REQUEST['idLocation']);
 $selected_zone_obj=$location_obj->zones_array[$_REQUEST['idZone']];
 if(!$selected_zone_obj->id){$selected_zone_obj=reset($location_obj->zones_array);}
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("framework_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("locations_view"));

       // deleted alert
       if($location_obj->deleted){api_alerts_add(api_text("locations_view-deleted-alert"),"warning");}

 // build zones table
 $zones_table=new cTable(api_text("locations_view-zones-tr-unvalued"));
 // cycle location zones
 foreach($location_obj->zones_array as $zone_obj){
  // check selected
  if($zone_obj->id==$selected_zone_obj->id){$tr_class="info";}else{$tr_class=NULL;}
  // build operations button
  $ob_obj=new cOperationsButton();
  $ob_obj->addElement("?mod=air-conditioning&scr=locations_view&act=zone_info&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-info-circle",api_text("locations_view-zones-td-info"));
  $ob_obj->addElement("?mod=air-conditioning&scr=locations_zones_edit&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-pencil",api_text("locations_view-zones-td-edit"));
  $ob_obj->addElement("?mod=air-conditioning&scr=submit&act=location_zone_move_up&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-arrow-up",api_text("locations_view-zones-td-move-up"),($zone_obj->order>1?TRUE:FALSE));
  $ob_obj->addElement("?mod=air-conditioning&scr=submit&act=location_zone_move_down&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-arrow-down",api_text("locations_view-zones-td-move-down"),($zone_obj->order<count($location_obj->zones_array)?TRUE:FALSE));
  $ob_obj->addElement("?mod=air-conditioning&scr=submit&act=location_zone_delete&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,"fa-trash",api_text("locations_view-zones-td-delete"),true,api_text("locations_view-zones-td-delete-confirm"));
  // add zone row
  $zones_table->addRow($tr_class);
  $zones_table->addRowField($zone_obj->name,"nowrap");
  $zones_table->addRowField($zone_obj->getAppliances("&nbsp;",TRUE,FALSE),"nowrap");
  $zones_table->addRowField($zone_obj->description,"truncate-ellipsis");
  $zones_table->addRowField($ob_obj->render(),"text-right");
 }

 // definitions
 $zone_panels_array=array();


 // build zone panel
 $zone_panel=new cPanel($location_obj->name,"panel-primary");
 $zone_panel->SetBody($location_obj->description);

 $zone_panels_array[]=$zone_panel;




 foreach($location_obj->zones_array as $zone_obj){

  $zone_panel_body=NULL;

  $last_detection=$zone_obj->getDetections(1)[0];
  // che if last detection is not oldest than 15 minutes
  if((time()-$last_detection->timestamp)<900){
   $zone_panel_body=api_icon("fa-thermometer-three-quarters")."&nbsp;".round($last_detection->temperature,1)."°C&nbsp;&nbsp;".api_icon("fa-tint")."&nbsp;".round($last_detection->humidity)."%";
  }else{
   $zone_panel_body=api_text("locations_view-panel-offline");
  }

  $zone_panel_body=api_tag("center",api_tag("h3",$zone_panel_body));


  $line_data=NULL;
  $detections=$zone_obj->getDetections(48);
  foreach($detections as $detection){$line_data.=",".round($detection->temperature,1);}

  $zone_panel_body.=api_tag("span",substr($line_data,1),"peity-line");

  $html->addScript("$(\"span.peity-line\").peity(\"line\",{width:'100%',height:30,stroke:'#337AB7'});");

  $panel_link=api_link("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,api_icon("fa-arrow-circle-o-right",NULL,"hidden-link"));
  if($zone_obj->id==$selected_zone_obj->id){$panel_link=NULL;}

  // build zone panel
  $zone_panel=new cPanel($zone_obj->name." ".api_tag("span",$panel_link,"pull-right"),($zone_obj->id==$selected_zone_obj->id?"panel-primary":NULL));
  $zone_panel->SetBody($zone_panel_body);

  $zone_panels_array[]=$zone_panel;
 }

 // build left location description list
 $dl_left=new cDescriptionList("br","dl-horizontal");
 $dl_left->addElement(api_text("locations_view-dt-name"),api_tag("strong",$location_obj->name));
 $dl_left->addElement(api_text("locations_view-dt-description"),$location_obj->description);

 // build right location description list
 $dl_right=new cDescriptionList("br","dl-horizontal");
 $dl_right->addElement(api_text("locations_view-dt-zones"),$zones_table->render());



 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 //$grid->addCol($dl_left->render(),"col-xs-12");
 //$grid->addCol($dl_right->render(),"col-xs-12 col-sm-8");

 foreach($zone_panels_array as $index=>$zone_panel){$a.=$zone_panel->render();}

 $grid->addCol($a,"col-xs-12 col-sm-3");


 // selected zone

 $last_detection=$selected_zone_obj->getDetections(1)[0];

 // build XX panel
 $zone_panel=new cPanel($selected_zone_obj->name);
 $zone_panel->SetBody(api_text("locations_view-last_synchronization",api_timestampDifferenceFormat(time()-$last_detection->timestamp,FALSE)));

 // build XX panel
 $notifications_panel=new cPanel("Registro eventi");
 $notifications_panel->SetBody("2017-01-01 21:01 Sistema offline<br>2017-01-01 20:50 Riscaldamento acceso<br>2017-01-01 18:25 Riscaldamento spento");

 // build XX panel
 $planning_panel=new cPanel("Planning attuale");
 $planning_panel->SetBody("<div class=\"progress\"><div class=\"progress-bar progress-bar-striped\" role=\"progressbar\" aria-valuenow=\"45\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 45%\"><span class=\"sr-only\">45% Complete</span></div></div>");

 // build XX panel
 $sensors_panel=new cPanel("Rilevazione");
 $sensors_panel->SetBody(api_tag("span",$last_detection->temperature."/25","peity-pie").api_tag("span",$last_detection->humidity."/100","peity-pie"));
 $html->addScript("$(\"span.peity-pie\").peity(\"pie\",{width:'50%',height:125,fill:['#518DC1','#C6D9FD'],innerRadius:25});");

 // build XX panel
 $trend_panel=new cPanel("Ultime 24 ore");
 $trend_panel->SetBody(api_tag("span","19.5,19,18.5,18,17.5,18,18.5,18,17.5,17,16,17,18,19,20,21,22,21,20,19,18,18.5,19","peity-trend"));
 $html->addScript("$(\"span.peity-trend\").peity(\"bar\",{width:'100%',height:80,fill:['#518DC1']});");

 $grid->addCol($planning_panel->render().$sensors_panel->render().$trend_panel->render(),"col-xs-12 col-sm-5");
 $grid->addCol($zone_panel->render().$notifications_panel->render(),"col-xs-12 col-sm-4");

 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($location_obj,"location object");}
?>