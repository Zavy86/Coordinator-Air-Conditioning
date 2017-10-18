<?php
/**
 * Air Conditioning - Location
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */

/**
 * Air Conditioning Location class
 */
class cAirConditioningLocation{

 /** Properties */
 protected $id;
 protected $name;
 protected $description;
 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;
 protected $deleted;
 protected $modalities_array;
 protected $zones_array;

 /**
  * Debug
  *
  * @return object Air Conditioning Location object
  */
 public function debug(){return $this;}

 /**
  * Air Conditioning Location class
  *
  * @param integer $location Air Conditioning Location object or ID
  * @return boolean
  */
 public function __construct($location){
  // get object
  if(is_numeric($location)){$location=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `air-conditioning_locations` WHERE `id`='".$location."'");}
  if(!$location->id){return false;}
  // set properties
  $this->id=(int)$location->id;
  $this->name=stripslashes($location->name);
  $this->description=stripslashes($location->description);
  $this->addTimestamp=(int)$location->addTimestamp;
  $this->addFkUser=(int)$location->addFkUser;
  $this->updTimestamp=(int)$location->updTimestamp;
  $this->updFkUser=(int)$location->updFkUser;
  $this->deleted=(int)$location->deleted;
  // get modalities
  $this->modalities_array=array();
  $modalities_results=$GLOBALS['database']->queryObjects("SELECT * FROM `air-conditioning_locations_modalities` WHERE `fkLocation`='".$this->id."' ORDER BY `temperature`");
  foreach($modalities_results as $modality){$this->modalities_array[$modality->id]=new cAirConditioningLocationModality($modality);}
  // get zones
  $this->zones_array=array();
  $zones_results=$GLOBALS['database']->queryObjects("SELECT * FROM `air-conditioning_locations_zones` WHERE `fkLocation`='".$this->id."' ORDER BY `order`");
  foreach($zones_results as $zone){$this->zones_array[$zone->id]=new cAirConditioningLocationZone($zone);}
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