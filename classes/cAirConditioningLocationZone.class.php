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
 protected $name;
 protected $description;
 protected $token;

 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;
 protected $deleted;

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
  if(!$zone->id){return FALSE;}
  // set properties
  $this->id=(int)$zone->id;
  $this->name=stripslashes($zone->name);
  $this->description=stripslashes($zone->description);
  $this->token=stripslashes($zone->token);

  $this->addTimestamp=(int)$zone->addTimestamp;
  $this->addFkUser=(int)$zone->addFkUser;
  $this->updTimestamp=(int)$zone->updTimestamp;
  $this->updFkUser=(int)$zone->updFkUser;
  $this->deleted=(int)$zone->deleted;
  // get appliances
  $this->appliances_array=array();
  $appliances_results=$GLOBALS['database']->queryObjects("SELECT * FROM `air-conditioning_locations_zones_appliances` WHERE `fkZone`='".$this->id."' ORDER BY `appliance`"); /** @todo order? */
  foreach($appliances_results as $appliance){$this->appliances_array[$appliance->id]=new cAirConditioningLocationZoneAppliance($appliance);}
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
  * @param boolean $showIcon show icon
  * @param boolean $showText show text
  * @return string gender text and icon
  */
 public function getAppliances($glue=", ",$showIcon=TRUE,$showText=TRUE){

  $appliances_array=array();

  foreach($this->appliances_array as $appliance_obj){
   $appliances_array[]=$appliance_obj->getAppliance($showIcon,$showText);
  }

  $return=implode($glue,$appliances_array);

  return $return;
 }

}

?>