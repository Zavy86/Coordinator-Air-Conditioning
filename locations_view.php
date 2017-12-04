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
 if(!$selected_zone_obj){ /** @todo migliorabile */
  foreach($location_obj->zones_array as $zone_obj){$selected_zone_obj=$zone_obj;break;}
  $_REQUEST['idZone']=$selected_zone_obj->id;
 }
 // check objects
 if(!$location_obj->id){api_alerts_add(api_text("framework_alert_locationNotFound"),"danger");api_redirect("?mod=air-conditioning&scr=locations_list");}
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("locations_view"));

 // definitions
 $zone_panels_array=array();


 // location menu
 $location_list=new cList();
 $location_list->addElement(api_link("?mod=air-conditioning&scr=locations_view&act=manage_modalities&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id,api_text("locations_view-panel-modalities")));

 if($selected_zone_obj->id){$location_list->addElement(api_link("?mod=air-conditioning&scr=locations_view&act=manage_plannings&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id,api_text("locations_view-panel-plannings",$selected_zone_obj->name)));}

 // build location panel
 $location_panel=new cPanel($location_obj->name,"panel-primary");
 $location_panel->SetBody($location_obj->description.$location_list->render());





 foreach($location_obj->zones_array as $zone_obj){

  $zone_panel_body=null;

  $last_detection=$zone_obj->getDetections(1)[0];
  // che if last detection is not oldest than 5 minutes
  if((time()-$last_detection->timestamp)<300){
   $zone_panel_body=api_icon("fa-thermometer-three-quarters")."&nbsp;".round($last_detection->temperature,1)."°C";
   $zone_panel_body.="&nbsp;&nbsp;".api_icon("fa-fire",api_text("locations_view-panel-heater-on"));
  }else{
   $zone_panel_body=api_text("locations_view-panel-offline");
  }

  $zone_panel_body=api_tag("center",api_tag("h3",$zone_panel_body));

  $trend_array=$zone_obj->getTrend();
  if(!is_array($trend_array)){$trend_array=array();}

  $zone_panel_body.=api_tag("span",implode(",",$trend_array),"peity-line");

  $html->addScript("$(\"span.peity-line\").peity(\"line\",{width:'100%',height:30,stroke:'#337AB7'});");

  $panel_link=api_link("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,api_icon("fa-arrow-circle-o-right",null,"hidden-link"));
  if($zone_obj->id==$selected_zone_obj->id){$panel_link=null;}

  // build zone panel
  $zone_panel=new cPanel($zone_obj->name." ".api_tag("span",$panel_link,"pull-right"),($zone_obj->id==$selected_zone_obj->id?"panel-primary":null));
  $zone_panel->SetBody($zone_panel_body);

  $zone_panels_array[]=$zone_panel;
 }


 // build grid object
 $grid=new cGrid();
 $grid->addRow();

 $panels_renderized=$location_panel->render();

 foreach($zone_panels_array as $index=>$zone_panel){$panels_renderized.=$zone_panel->render();}

 $grid->addCol($panels_renderized,"col-xs-12 col-sm-4");

 // selected zone
 if($selected_zone_obj->id){

  //$planning_body.=api_link("#",api_icon("fa-edit",api_text("locations_view-modalities-td-add"),"hidden-link"),null,"btn btn-default btn-xs");

  // build planning panel
  $planning_panel=new cPanel(api_link("?mod=air-conditioning&scr=locations_view&act=manage_plannings_manual&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id,api_icon("fa-clock-o",api_text("locations_view-planning-panel-manual"),"hidden-link"))." ".api_text("locations_view-planning-panel-title"));
  $planning_panel->SetBody(api_airConditioning_locationZonePlanningDayProgressBar($location_obj,$_REQUEST['idZone'],strtolower(date("l")))->render());

  // build detection panel
  $detection_panel=new cPanel(api_link("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id,api_icon("fa-refresh",api_text("locations_view-detection-panel-refresh"),"hidden-link"))." ".api_text("locations_view-detection-panel-title"));

  // get last detection
  $last_detection=$selected_zone_obj->getDetections(1)[0];

  // get current modality
  $current_modality_obj=new cAirConditioningLocationModality($selected_zone_obj->getCurrentStep()->fkModality);

  // get current step
  $current_temperature=$selected_zone_obj->getCurrentTemperature();
  if(!$current_temperature){$current_temperature=10;}
  /** @todo usare temperatura dai settings al posto della 10 fissa */

  // build temperature gauge
  $detection_gauge=new cGauge();
  $detection_gauge->options['value']=$last_detection->temperature;
  $detection_gauge->options['min']=8;
  $detection_gauge->options['max']=32;
  $detection_gauge->options['title']="Temperatura";
  $detection_gauge->options['label']="Rilevati";
  $detection_gauge->options['symbol']="°";
  $detection_gauge->options['decimals']=1;
  //$detection_gauge->options['levelColors']=array("#337ab7");
  // build humidity gauge
  $humidity_gauge=new cGauge();
  $humidity_gauge->options['value']=round($last_detection->humidity);
  $humidity_gauge->options['title']="Umidità";
  $humidity_gauge->options['label']="rilevata";
  $humidity_gauge->options['symbol']="%";
  //$humidity_gauge->options['levelColors']=array("#337ab7");
  $humidity_gauge->options['levelColors']=array("#ff0000","#a9d70b","#ff0000");
  // build temperature gauge
  $temperature_gauge=new cGauge();
  $temperature_gauge->options['value']=($last_detection->temperature-$current_temperature<0?$last_detection->temperature-$current_temperature:0);
  $temperature_gauge->options['min']=-($current_temperature);
  $temperature_gauge->options['max']=0;
  $temperature_gauge->options['hideMinMax']=true;
  $temperature_gauge->options['title']="Target";
  $temperature_gauge->options['label']="al target di ".$current_temperature."°C";
  $temperature_gauge->options['symbol']="°";
  $temperature_gauge->options['decimals']=1;
  $temperature_gauge->options['levelColors']=array($current_modality_obj->color);

  // build detection grid
  $detection_grid=new cGrid();
  $detection_grid->addRow();
  $detection_grid->addCol($detection_gauge->render(),"col-xs-4 col-sm-4");
  $detection_grid->addCol($humidity_gauge->render(),"col-xs-4 col-sm-4");
  $detection_grid->addCol($temperature_gauge->render(),"col-xs-4 col-sm-4");
  // check for manual mode
  if((time()<=$selected_zone_obj->manual_timestamp)){
   // add manual warning to detection grid
   $detection_grid->addRow();
   $detection_grid->addCol(api_tag("div",api_tag("small",api_text("locations_view-manual",$selected_zone_obj->manual_temperature)."°C ".api_icon("fa-exclamation-triangle")),"text-right"),"col-xs-12 col-sm-12");
  }
  // check for last detection timestamp
  if((time()-$last_detection->timestamp)>300){
   // make last detection difference
   if($last_detection->timestamp){$difference=api_text("locations_view-last_detection-ago",api_timestampDifferenceFormat(time()-$last_detection->timestamp,false));}
   else{$difference=api_text("locations_view-last_detection-never");}
   $last_detection=api_text("locations_view-last_detection").$difference." ".api_icon("fa-exclamation-triangle");
   // add difference warning to detection grid
   $detection_grid->addRow();
   $detection_grid->addCol(api_tag("div",api_tag("small",$last_detection),"text-right"),"col-xs-12 col-sm-12");
  }
  // add detection grid to detection panel
  $detection_panel->SetBody($detection_grid->render(false));
  // add gauges scripts
  $html->addScript($detection_gauge->getScript());
  $html->addScript($temperature_gauge->getScript());
  $html->addScript($humidity_gauge->getScript());

  // build trend panel
  $trend_panel=new cPanel("Ultime 24 ore");
  /*// get selected zone trend
  $trend_array=$selected_zone_obj->getTrend();
  if(!is_array($trend_array)){$trend_array=array();}
  $trend_panel->SetBody(api_tag("span",implode(",",$trend_array),"peity-trend"));
  $html->addScript("$(\"span.peity-trend\").peity(\"bar\",{width:'100%',height:80,fill:['#518DC1']});");*/


  $trend_array=$selected_zone_obj->getTrend();

  // make min and max by detection
  $gradi_min=100;
  $gradi_max=0;
  foreach($trend_array as $grado){
   if($grado<$gradi_min){$gradi_min=$grado;}
   if($grado>$gradi_max){$gradi_max=$grado;}
  }

  $gradi_min=round($gradi_min)-2;
  $gradi_max=round($gradi_max)+2;

  // make min and max by target
  $gradi_min=13;

  $trend_detection=implode(",",$trend_array);

$script=<<<EOT

var ctx = $("#myChart");

var chartData = {
 labels: [10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7,8,9],
 datasets: [/*{
     type: 'line',
     label: 'Temperatura target',
     borderColor: "#337AB7",
     backgroundColor: "#387CC9",
     pointBackgroundColor: "#387CC9",
     fill:false,
     data: [15,15,15,15,21,21,21,21,15,15,15,15,21,21,21,18,18,18,18,18,18,18,18,15]
 }, */{
     type: 'line',
     label: 'Temperatura rilevata',
     borderColor: "#42A5F5",
     backgroundColor: "#5EB2F6",
     fill:true,
     data: [{$trend_detection}]
 }]

};

var chart = new Chart(ctx, {
   type: 'line',
   data: chartData,
   options: {
       tooltips: {
           mode: 'index'
       },
       scales: {
          yAxes: [{
              ticks: {
                min: {$gradi_min},
                max: {$gradi_max},
                stepSize: 1
              }
          }]
      },
      legend: {
       display: false
      }
   }
});

EOT;



  // get selected zone trend
  $trend_array=$selected_zone_obj->getTrend();
  if(!is_array($trend_array)){$trend_array=array();}
  $trend_panel->SetBody("<canvas id='myChart' height='100'></canvas>");
  $html->addScript($script);

  // add planning, detection and trend panels to grid
  $grid->addCol($planning_panel->render().$detection_panel->render().$trend_panel->render(),"col-xs-12 col-sm-8");
 }

 // add content to html
 $html->addContent($grid->render());

 // include modal windows
 require_once("locations_view_modal_modalities.inc.php");
 require_once("locations_view_modal_plannings.inc.php");

 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($location_obj,"location object");}
?>