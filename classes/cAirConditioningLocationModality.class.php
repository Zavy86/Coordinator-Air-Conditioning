<?php
/**
 * Air Conditioning - Location Modality
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */

/**
 * Air Conditioning Location Modality class
 */
class cAirConditioningLocationModality{

 /** Properties */
 protected $id;
 protected $name;
 protected $color;
 protected $temperature;
 protected $icon;
 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;

 /**
  * Debug
  *
  * @return object Air Conditioning Location Modality object
  */
 public function debug(){return $this;}

 /**
  * Air Conditioning Location Modality class
  *
  * @param integer $modality Air Conditioning Location Modality object or ID
  * @return boolean
  */
 public function __construct($modality){
  // get object
  if(is_numeric($modality)){$modality=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `air-conditioning_locations_modalities` WHERE `id`='".$modality."'");}
  if(!$modality->id){return FALSE;}
  // set properties
  $this->id=(int)$modality->id;
  $this->name=stripslashes($modality->name);
  $this->color=stripslashes($modality->color);
  $this->temperature=(double)$modality->temperature;
  $this->addTimestamp=(int)$modality->addTimestamp;
  $this->addFkUser=(int)$modality->addFkUser;
  $this->updTimestamp=(int)$modality->updTimestamp;
  $this->updFkUser=(int)$modality->updFkUser;
  // make icon
  $this->icon=api_icon("fa-square",$this->temperature."°C - ".$this->name,NULL,"color:".$this->color);
  return TRUE;
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