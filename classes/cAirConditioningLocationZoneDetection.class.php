<?php
/**
 * Air Conditioning - Location Zone Detection
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */

/**
 * Air Conditioning Location Zone Detection class
 */
class cAirConditioningLocationZoneDetection{

 /** Properties */
 protected $id;
 protected $timestamp;
 protected $temperature;
 protected $humidity;
 protected $heater_status;
 protected $cooler_status;
 protected $dehumidifier_status;
 protected $humidifier_status;

 /**
  * Debug
  *
  * @return object Air Conditioning Location Zone Detection object
  */
 public function debug(){return $this;}

 /**
  * Air Conditioning Location Zone class
  *
  * @param integer $detection Air Conditioning Location Zone Detection object or ID
  * @return boolean
  */
 public function __construct($detection){
  // get object
  if(is_numeric($detection)){$detection=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `air-conditioning_locations_zones_detections` WHERE `id`='".$detection."'");}
  if(!$detection->id){return false;}
  // set properties
  $this->id=(int)$detection->id;
  $this->timestamp=(int)$detection->timestamp;
  $this->temperature=(double)$detection->temperature;
  $this->humidity=(double)$detection->humidity;
  $this->heater_status=(boolean)$detection->heater_status;
  $this->cooler_status=(boolean)$detection->cooler_status;
  $this->dehumidifier_status=(boolean)$detection->dehumidifier_status;
  $this->humidifier_status=(boolean)$detection->humidifier_status;
  return true;
 }

 /**
  * Get
  *
  * @param string $property Property name
  * @return string Property value
  */
 public function __get($property){return $this->$property;}

}

?>