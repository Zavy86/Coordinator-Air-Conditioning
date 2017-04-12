<?php
/**
 * Air Conditioning - Location Zone
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */

/**
 * Air Conditioning Location Zone class
 */
class cAirConditioningLocationZone{

 /** Properties */
 protected $id;
 protected $order;
 protected $name;
 protected $description;
 protected $token;
 protected $heater_relay;
 protected $cooler_relay;
 protected $dehumidifier_relay;
 protected $humidifier_relay;

 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;

 protected $appliances_array;

 /**
  * Debug
  *
  * @return object Air Conditioning Location Zone object
  */
 public function debug(){return $this;}

 /**
  * Air Conditioning Location Zone class
  *
  * @param integer $zone Air Conditioning Location Zone object or ID
  * @return boolean
  */
 public function __construct($zone){
  // get object
  if(is_numeric($zone)){$zone=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `air-conditioning_locations_zones` WHERE `id`='".$zone."'");}
  elseif(is_string($zone) && strlen($zone)==32){$zone=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `air-conditioning_locations_zones` WHERE `token`='".$zone."'");}
  if(!$zone->id){return FALSE;}
  // set properties
  $this->id=(int)$zone->id;
  $this->order=(int)$zone->order;
  $this->name=stripslashes($zone->name);
  $this->description=stripslashes($zone->description);
  $this->token=stripslashes($zone->token);
  $this->heater_relay=(int)$zone->heater_relay;
  $this->cooler_relay=(int)$zone->cooler_relay;
  $this->dehumidifier_relay=(int)$zone->dehumidifier_relay;
  $this->humidifier_relay=(int)$zone->humidifier_relay;

  $this->addTimestamp=(int)$zone->addTimestamp;
  $this->addFkUser=(int)$zone->addFkUser;
  $this->updTimestamp=(int)$zone->updTimestamp;
  $this->updFkUser=(int)$zone->updFkUser;
  // set appliances
  $this->appliances_array=array();
  if($this->heater_relay){$this->appliances_array['heater']=$this->buildAppliance("heater");}
  if($this->cooler_relay){$this->appliances_array['cooler']=$this->buildAppliance("cooler");}
  if($this->dehumidifier_relay){$this->appliances_array['dehumidifier']=$this->buildAppliance("dehumidifier");}
  if($this->humidifier_relay){$this->appliances_array['humidifier']=$this->buildAppliance("humidifier");}
  return TRUE;
 }

 /**
  * Get
  *
  * @param string $property Property name
  * @return string Property value
  */
 public function __get($property){return $this->$property;}

 /**
  * Get Appliances
  *
  * @param boolean $showIcon Show icon
  * @param boolean $showText Show text
  * @return string Appliances list
  */
 public function getAppliances($glue=", ",$showIcon=TRUE,$showText=TRUE){
  // definitions
  $appliances_array=array();
  // cycle all appliances
  foreach($this->appliances_array as $appliance_obj){
   if($showIcon){
    if($showText){
     $appliances_array[]=$appliance_obj->appliance;
    }else{
     $appliances_array[]=$appliance_obj->icon;
    }
   }else{
    $appliances_array[]=$appliance_obj->text;
   }
  }
  // return
  $return=implode($glue,$appliances_array);
  return $return;
 }

 /**
  * Get Detections
  *
  * @param integer $limit Limit extraction
  * @param integer $timestamp Oldest timestamp to extract
  * @return array Detections
  */
 public function getDetections($limit=NULL,$timestamp=NULL){
  // check for parameters
  if($limit==NULL && $timestamp==NULL){return FALSE;}
  // definitions
  $detections_array=array();
  // check for limit
  if($limit){$query_limit=" LIMIT 0,".$limit;}
  // check for timestamp
  if($timestamp){$query_where=" AND `timestamp`>='".$timestamp."'";}
  // get detections
  $detections_results=$GLOBALS['database']->queryObjects("SELECT * FROM `air-conditioning_locations_zones_detections` WHERE `fkZone`='".$this->id."'".$query_where." ORDER BY `timestamp` DESC".$query_limit);
  foreach($detections_results as $detection){$detections_array[]=new cAirConditioningLocationZoneDetection($detection);}
  // return
  return $detections_array;
 }

 /**
  * Get Trend
  *
  * @return array Last 24 hours trend
  */
 public function getTrend(){
  // definitions
  $now=time();
  $timestamps_array=array();
  $detections_avg_array=array();
  // build last 24 hours array
  for($h=0;$h<24;$h++){
   $timestamp=new stdClass();
   $timestamp->from=api_timestampDifferenceFrom($now,"-".($h+1)." hour");
   $timestamp->to=api_timestampDifferenceFrom($now,"-".($h)." hour");
   $timestamps_array[]=$timestamp;
  }
  // cycle all hours
  foreach(array_reverse($timestamps_array) as $timestamp){
   // definitions
   $detections_array=array();
   // make query where
   $query_where="( `timestamp`>='".$timestamp->from."' AND `timestamp`<'".$timestamp->to."' )";
   // get detections of this hour
   $detections_results=$GLOBALS['database']->queryObjects("SELECT * FROM `air-conditioning_locations_zones_detections` WHERE `fkZone`='".$this->id."' AND ".$query_where." ORDER BY `timestamp` DESC");
   foreach($detections_results as $detection){$detections_array[]=new cAirConditioningLocationZoneDetection($detection);}
   // calculate temperature average for this hour
   $temperature_sum=0;
   foreach($detections_array as $detection){$temperature_sum+=$detection->temperature;}
   if($temperature_sum>0){$temperature_avg=$temperature_sum/count($detections_array);}else{$temperature_avg=0;}
   // store temperature average
   $detections_avg_array[]=round($temperature_avg,1);
  }
  // return
  return $detections_avg_array;
 }

 /**
  * Build Appliance
  *
  * @return object Appliance object
  */
 private function buildAppliance($appliance){
  // definitions
  $return=new stdClass();
  // switch gender
  switch($appliance){
   case "heater":$icon=api_icon("fa-fire",api_text("appliance-heater"));$text=api_text("appliance-heater");break;
   case "cooler":$icon=api_icon("fa-snowflake-o",api_text("appliance-cooler"));$text=api_text("appliance-cooler");break;
   case "humidifier":$icon=api_icon("fa-tint",api_text("appliance-humidifier"));$text=api_text("appliance-humidifier");break;
   case "dehumidifier":$icon=api_icon("fa-cloud",api_text("appliance-dehumidifier"));$text=api_text("appliance-dehumidifier");break;
   default:return NULL;
  }
  // return
  $return->appliance=$icon." ".$text;
  $return->name=$text;
  $return->icon=$icon;
  return $return;
 }

}

?>