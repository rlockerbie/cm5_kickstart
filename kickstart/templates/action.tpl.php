<?php
/**
 * 
 * Url:
 * 
 * /###MODULE_NAME###/###ACTION###/{id}
 * 
 * @param Web $w
 */
function ###ACTION###_GET(Web $w) {
	// parse the url into parameters
	$p = $w->pathMatch("id");
	
	// create either a new or existing object
	if (isset($p['id'])) {
		$data = $w->###MODULE_SERVICE###->getDataForId($p['id']);
	} else {
		$data = new ###MODULE_DB###($w);
	}
	
	// create the edit form
	$f = Html::form(array(
			array("Edit ###MODULE_NAME_UC###","section"),
###FORM_FIELDS###
	),$w->localUrl("/###MODULE_NAME###/###ACTION###/".$p['id']),"POST"," Save ");
	
	// circumvent the template and print straight into the layout
	$w->out($f);
}

/**
 * Receive post data from ###MODULE_DB### ###ACTION### form.
 * 
 * Url:
 * 
 * /###MODULE_NAME###/###ACTION###/{id}
 * 
 * @param Web $w
 */
function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	if (isset($p['id'])) {
		$data = $w->###MODULE_SERVICE###->getDataForId($p['id']);
	} else {
		$data = new ###MODULE_DB###($w);
	}
	
	$data->fill($_POST);
	// fill in validation step!
	
	$data->insertOrUpdate();
	
	// go back to the list view
	$w->msg("###MODULE_DB### updated","###MODULE_NAME###/index");
}