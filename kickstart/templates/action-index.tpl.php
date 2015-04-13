<?php
/**
 * @param Web $w
 */
function index_ALL(Web $w) {
	// adding data to the template context
	$w->ctx("message","###MODULE_DB### List");
	
	// get the list of data objects
	$listdata = $w->###MODULE_SERVICE###->getAllData();
	
	// prepare table data
	$t[]=array(###INDEX_HEADERS###, "Actions"); // table header
	if (!empty($listdata)) {
		foreach ($listdata as $d) {
			$row = array();
###INDEX_FIELDS###
			
			// prepare action buttons for each row
			$actions = array();
			if ($d->canEdit($w->Auth->user())) {
				$actions[] = Html::box("/###MODULE_NAME###/edit/".$d->id, "Edit", true);
			}
			if ($d->canDelete($w->Auth->user())) {
				$actions[] = Html::b("/###MODULE_NAME###/delete/".$d->id, "Delete", "Really delete?");
			}
			
			$row[] = implode(" ",$actions);
			$t[] = $row;
		}
	}
	
	// create the html table and put into template context
	$w->ctx("table",Html::table($t,"table","tablesorter",true));
}
