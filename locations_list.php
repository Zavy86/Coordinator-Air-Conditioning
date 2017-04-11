<?php
/**
 * Air Conditioning - Locations List
 *
 * @package Coordinator\Modules\Air-Conditioning
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="air-conditioning-locations_manage";
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("locations_list"));
 // build grid object
 $table=new cTable(api_text("locations_list-tr-unvalued"));
 $table->addHeader("&nbsp;",NULL,16);
 $table->addHeader(api_text("locations_list-th-name"),"nowrap");
 $table->addHeader(api_text("locations_list-th-description"),NULL,"100%");
 $table->addHeader("&nbsp;",NULL,16);
 // cycle all locations
 foreach(api_airConditioning_locations(TRUE) as $location_obj){
  // build operation button
  $ob=new cOperationsButton();
  $ob->addElement("?mod=air-conditioning&scr=locations_edit&idLocation=".$location_obj->id,"fa-pencil",api_text("locations_list-td-edit"));
  if($location_obj->deleted){$ob->addElement("?mod=air-conditioning&scr=submit&act=location_undelete&idLocation=".$location_obj->id,"fa-trash-o",api_text("locations_list-td-undelete"),true,api_text("locations_list-td-undelete-confirm"));}
  else{$ob->addElement("?mod=air-conditioning&scr=submit&act=location_delete&idLocation=".$location_obj->id,"fa-trash",api_text("locations_list-td-delete"),true,api_text("locations_list-td-delete-confirm"));}
  // check deleted
  if($location_obj->deleted){$tr_class="deleted";}else{$tr_class=NULL;}
  // make location row
  $table->addRow($tr_class);
  $table->addRowField(api_link("?mod=air-conditioning&scr=locations_manage&idLocation=".$location_obj->id,api_icon("fa-search",NULL,"hidden-link"),api_text("locations_list-td-view")));
  $table->addRowField($location_obj->name,"nowrap");
  $table->addRowField($location_obj->description,"truncate-ellipsis");
  $table->addRowField($ob->render(),"text-right");
 }
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
?>