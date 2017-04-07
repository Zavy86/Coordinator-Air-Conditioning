<?php
/**
 * Air Conditioning - Location Zone Appliance
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */

/**
 * Air Conditioning Location Zone Appliance class
 */
class cAirConditioningLocationZoneAppliance{

 /** Properties */
 protected $id;
 protected $appliance;
 protected $relay;

 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;
 protected $deleted;

 /**
  * Debug
  *
  * @return object Air Conditioning Location Zone object
  */
 public function debug(){return $this;}

 /**
  * Air Conditioning Location Zone class
  *
  * @param integer $appliance Air Conditioning Location Zone object or ID
  * @return boolean
  */
 public function __construct($appliance){
  // get object
  if(is_numeric($appliance)){$appliance=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `air-conditioning_locations_zones_appliances` WHERE `id`='".$appliance."'");}
  if(!$appliance->id){return FALSE;}
  // set properties
  $this->id=(int)$appliance->id;
  $this->appliance=stripslashes($appliance->appliance);
  $this->relay=(int)$appliance->relay;

  $this->addTimestamp=(int)$appliance->addTimestamp;
  $this->addFkUser=(int)$appliance->addFkUser;
  $this->updTimestamp=(int)$appliance->updTimestamp;
  $this->updFkUser=(int)$appliance->updFkUser;
  $this->deleted=(int)$appliance->deleted;
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
  * Get Appliance
  *
  * @param boolean $showIcon show icon
  * @param boolean $showText show text
  * @return string appliance text and icon
  */
 public function getAppliance($showIcon=TRUE,$showText=TRUE){
  // switch gender
  switch($this->appliance){
   case "heater":$icon=api_icon("fa-sun-o",api_text("appliance-heater"));$text=api_text("appliance-heater");break;
   case "cooler":$icon=api_icon("fa-snowflake-o",api_text("appliance-cooler"));$text=api_text("appliance-cooler");break;
   case "humidifier":$icon=api_icon("fa-tint",api_text("appliance-humidifier"));$text=api_text("appliance-humidifier");break;
   case "dehumidifier":$icon=api_icon("fa-cloud",api_text("appliance-dehumidifier"));$text=api_text("appliance-dehumidifier");break;
   default:return NULL;
  }
  // return
  if($showIcon){if($showText){$return.=$icon." ".$text;}else{$return=$icon;}}else{$return=$text;}
  return $return;
 }

}

?>