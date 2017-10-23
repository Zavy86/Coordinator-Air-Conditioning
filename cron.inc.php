<?php
/**
 * Cron
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // definitions
 $logs=array();
 // stop manual expired plannings
 $GLOBALS['database']->queryExecute("UPDATE FROM `air-conditioning_locations_zones` SET `manual_timestamp`=NULL WHERE `manual_timestamp`>'".time()."'");
 // log
 $logs[]="Expired manual plannings stopped";
 // debug
 api_dump($logs,"air-conditioning");
?>