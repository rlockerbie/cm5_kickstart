<?php
function role_###MODULE_NAME###_admin_allowed($w, $path) {
	return startsWith($path, "###MODULE_NAME###");
}

function role_###MODULE_NAME###_view_allowed($w, $path) {
	$actions = "/###MODULE_NAME###\/(index";
    $actions .= "|view";
    $actions .= ")/";
    return preg_match($actions, $path);
}