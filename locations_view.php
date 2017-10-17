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
 if($selected_zone_obj->id){$location_list->addElement(api_link("?mod=air-conditioning&scr=locations_view&act=manage_plannings&idLocation=".$location_obj->id."&idZone=".$selected_zone_obj->id,api_text("locations_view-panel-plannings")));}

 // build location panel
 $location_panel=new cPanel($location_obj->name,"panel-primary");
 $location_panel->SetBody($location_obj->description.$location_list->render());





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

  $trend_array=$zone_obj->getTrend();
  if(!is_array($trend_array)){$trend_array=array();}

  $zone_panel_body.=api_tag("span",implode(",",$trend_array),"peity-line");

  $html->addScript("$(\"span.peity-line\").peity(\"line\",{width:'100%',height:30,stroke:'#337AB7'});");

  $panel_link=api_link("?mod=air-conditioning&scr=locations_view&idLocation=".$location_obj->id."&idZone=".$zone_obj->id,api_icon("fa-arrow-circle-o-right",NULL,"hidden-link"));
  if($zone_obj->id==$selected_zone_obj->id){$panel_link=NULL;}

  // build zone panel
  $zone_panel=new cPanel($zone_obj->name." ".api_tag("span",$panel_link,"pull-right"),($zone_obj->id==$selected_zone_obj->id?"panel-primary":NULL));
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


  // build planning panel
  $planning_panel=new cPanel("Planning odierno");
  $planning_panel->SetBody(api_airConditioning_locationZonePlanningDayProgressBar($location_obj,$_REQUEST['idZone'],strtolower(date("l")))->render());

  // build detection panel
  $detection_panel=new cPanel("Rilevazione");

  // make last detection timestamp difference
  $last_detection=$selected_zone_obj->getDetections(1)[0];
  if($last_detection->timestamp){$difference=api_text("locations_view-last_detection-ago",api_timestampDifferenceFormat(time()-$last_detection->timestamp,FALSE));}
  else{$difference=api_text("locations_view-last_detection-never");}
  // get current step
  $current_modality=new cAirConditioningLocationModality($zone_obj->getCurrentStep()->fkModality);
  if($current_modality->id){$current_temperature=$current_modality->temperature;}else{$current_temperature=10;}
  /** @todo usare temperatura dai settings al posto della 10 fissa */
  // make detection body
  $detection_body=api_tag("span",$last_detection->temperature."/".$current_temperature,"peity-pie");
  $detection_body.=api_tag("span",$last_detection->humidity."/100","peity-pie");
  $detection_body.="<br><br>".api_tag("div",api_text("locations_view-last_detection").$difference,"text-right");
  $detection_panel->SetBody($detection_body);
  $html->addScript("$(\"span.peity-pie\").peity(\"pie\",{width:'50%',innerRadius:25,radius:50,fill:['#518DC1','#C6D9FD']});");

  $detection_body="<div><div id='justgage1' class='justgage' style='width:50%'></div>";
  $detection_body.="<div id='justgage2' class='justgage' style='width:50%'></div></div>";
  $detection_panel->SetBody($detection_body);

  $script=" var g = new JustGage({
    id:'justgage1',
    value: ".$last_detection->temperature.",
    min: 0,
    max: ".$current_temperature.",
    symbol: '°C',
    title: 'Temperatura',
    /*label: 'Temperatura',*/
    relativeGaugeSize: true,

    /* colore se lo toglie fa da verde a rosso */
    levelColorsGradient: false,
    gaugeColor: '#ffffff',
    /*levelColors: ['#0099FF'],*/
    levelColors: ['#337AB7'],

    titleFontColor: '#333333',
    valueFontColor: '#333333',
    labelFontColor: '#666666',

    pointer: true,
    pointerOptions: {
          toplength: -18,
          bottomlength: 9,
          bottomwidth: 9,
          color: '#666666',
          stroke: '#ffffff',
          stroke_width: 3,
          stroke_linecap: 'round'
          },

    gaugeWidthScale: 1,
    counter: true
  });";

  $script.=" var g = new JustGage({
    id:'justgage2',
    value: ".$last_detection->humidity.",
    min: 0,
    max: 100,
    symbol: '%',
    title: 'Umidità',
    /*label: 'Umidità',*/

    titleFontColor: '#333333',
    valueFontColor: '#333333',
    labelFontColor: '#666666',

    relativeGaugeSize: true,
    levelColorsGradient: false,
    gaugeColor: '#ffffff',
    levelColors: ['#337AB7'],


    pointer: true,
    pointerOptions: {
          toplength: -18,
          bottomlength: 9,
          bottomwidth: 9,
          color: '#666666',
          stroke: '#ffffff',
          stroke_width: 3,
          stroke_linecap: 'round'
          },
    gaugeWidthScale: 1,
    counter: true

  });";

  $html->addScript($script);

  // build trend panel
  $trend_panel=new cPanel("Ultime 24 ore");
  // get selected zone trend
  $trend_array=$selected_zone_obj->getTrend();
  if(!is_array($trend_array)){$trend_array=array();}
  $trend_panel->SetBody(api_tag("span",implode(",",$trend_array),"peity-trend"));
  $html->addScript("$(\"span.peity-trend\").peity(\"bar\",{width:'100%',height:80,fill:['#518DC1']});");

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