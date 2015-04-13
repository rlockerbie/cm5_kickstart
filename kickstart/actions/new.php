<?php
/**
 * Display an edit form for either creating a new
 * record for ExampleData or edit an existing form.
 * 
 * Url:
 * 
 * /kickstart/edit/{id}
 * 
 * @param Web $w
 */
function new_GET(Web $w) {
	// parse the url into parameters
	$p = $w->pathMatch("id");
	
	// create the edit form
	$f = Html::form(array(
			array("New Module","section"),
			array("Module Name","text","module_name", ""),
			array("Module Author","text","module_author", ""),
			array("Module Title","text","module_title", ""),
			array("Actions - one per line","textarea","actions", "index", null, null, "basic"),
			array("SQL Structure","textarea","sql", "", null, null, "basic"),
	),$w->localUrl("/kickstart/new/".$p['id']),"POST"," Save");
	
	// circumvent the template and print straight into the layout
	$w->out($f);
}

/**
 * Receive post data from ExampleData edit form.
 * 
 * Url:
 * 
 * /example/edit/{id}
 * 
 * @param Web $w
 */
function new_POST(Web $w) {
	$p = $w->pathMatch("id");
	$module = $_POST['module_name'];
	$module = strtolower(trim($module));
	$module = str_replace('../', '-', $module);
	$modulesDirectory = ROOT_PATH . DIRECTORY_SEPARATOR . 'modules';
	$moduleDirectory = $modulesDirectory . DIRECTORY_SEPARATOR . $module;
	if(!is_dir($moduleDirectory)) {
		mkdir($moduleDirectory);
	}
	
	//Models
	$excludeFields = array(
		'id',
		'is_deleted',
		'dt_created',
		'creator_id',
		'dt_modified',
		'modifier_id'
	);
	$modelDirectory = $moduleDirectory . DIRECTORY_SEPARATOR . 'models';
	$modelTemplate = file_get_contents($modulesDirectory . DIRECTORY_SEPARATOR . 'kickstart' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'model.tpl.php');
	$serviceTemplate = file_get_contents($modulesDirectory . DIRECTORY_SEPARATOR . 'kickstart' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'service.tpl.php');
	if(!is_dir($modelDirectory)) {
		mkdir($modelDirectory);
	}
	$sql = $_POST['sql'];
	try {
		$w->db->sql(str_replace('IF NOT EXISTS', '', $_POST['sql']))->fetch_all();
	} catch(Exception $e) {
	}
	$sql = preg_replace('%^--.*$%m', '', $sql);
	$sql = preg_replace('%^--.*$%m', '', $sql);
	$sql = preg_replace('%\s{2,}%', '', $sql);
	$sql = trim($sql, "\n ;");
	$sql = explode(';', $sql);
	$indexService = null;
	$formFields = '';
	foreach($sql as $tableString) {
		preg_match('%create table.*?`(.*?)`(.*)%i', $tableString, $parts);
		$table = $parts[1];
		$fields = trim($parts[2]);
		$p = 0;
		$brOpen = 1;
		//Find closing matching bracket...
		while($p < strlen($fields)) {
			if($fields{$p} == '(') {
				$brOpen++;
			} else if($fields{$p} == ')') {
				$brOpen--;
				if($brOpen == 1) {
					break;
				}
			}
			$p++;
		}
		$fieldsStr = '';
		$fields = explode(',', substr($fields, 1, $p-1));
		$fill = false;
		if(empty($indexData)) {
			$indexData = array('header'=>array(), 'fields'=>'');
			$fill = true;
		}
		foreach($fields as $field) {
			if(preg_match('%^`(.*?)`%', $field, $parts)) {
				$field = $parts[1];
				if($field != 'id') {
					if($fill && !in_array($field, $excludeFields)) {
						$indexData['header'][] = ucfirst(str_replace('_', ' ', $field));
						$indexData['fields'] .= "\t\t\t\$row[] = \$d->$field;".PHP_EOL;
						$formFields .= "\t\t\t".'array("'.ucfirst(str_replace('_', ' ', $field)).'","text","'.$field.'", $data->'.$field.'),'.PHP_EOL;
					}
					$fieldsStr .= "\tpublic \$$field;".PHP_EOL;
				}
			}
		}
		$table = str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
		if(empty($indexService)) {
			$indexService = $table;
		}
		$markers = array(
			'###MODULE_NAME###' => $module,
			'###MODULE_DB###' => $table,
			'###MODULE_NAME_UC###' => ucfirst($module),
			'###FIELDS###' => $fieldsStr,
			'###AUTHOR###' => $_POST['module_author'],
			'###DATE###' => date('F Y'),
			'###INDEX_HEADERS###' => '"'.implode('", "', $indexData['header']).'"',
			'###INDEX_FIELDS###' => $indexData['fields']
		);
		$template = str_replace(array_keys($markers), array_values($markers), $modelTemplate);
		file_put_contents($modelDirectory . DIRECTORY_SEPARATOR . $table . '.php', $template);
		$template = str_replace(array_keys($markers), array_values($markers), $serviceTemplate);
		file_put_contents($modelDirectory . DIRECTORY_SEPARATOR . $table . 'Service.php', $template);
	}
	
	//Actions/templates
	$markers['###FORM_FIELDS###'] = $formFields;
	$markers['###MODULE_SERVICE###'] = $indexService;
	$markers['###MODULE_DB###'] = $indexService;
	$actions = explode("\n", $_POST['actions']);
	$actionTemplate = file_get_contents($modulesDirectory . DIRECTORY_SEPARATOR . 'kickstart' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'action.tpl.php');
	$actionIndexTemplate = file_get_contents($modulesDirectory . DIRECTORY_SEPARATOR . 'kickstart' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'action-index.tpl.php');
	$templateTemplate = file_get_contents($modulesDirectory . DIRECTORY_SEPARATOR . 'kickstart' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'template.tpl.php');
	$templateDirectory = $moduleDirectory . DIRECTORY_SEPARATOR . 'templates';
	if(!is_dir($templateDirectory)) {
		mkdir($templateDirectory);
	}
	foreach($actions as $action) {
		$action = strtolower(trim($action));
		$action = str_replace('../', '-', $action);
		$actionDirectory = $moduleDirectory . DIRECTORY_SEPARATOR . 'actions';
		if(!is_dir($actionDirectory)) {
			mkdir($actionDirectory, 0777, true);
		}
		$markers['###ACTION###'] = $action;
		if($action == 'index') {
			$template = str_replace(array_keys($markers), array_values($markers), $actionIndexTemplate);
			file_put_contents($actionDirectory . DIRECTORY_SEPARATOR . $action . '.php', $template);
			$template = str_replace(array_keys($markers), array_values($markers), $templateTemplate);
		} else {
			$template = str_replace(array_keys($markers), array_values($markers), $actionTemplate);
			file_put_contents($actionDirectory . DIRECTORY_SEPARATOR . $action . '.php', $template);
			$template = '<!-- Auto generated template - '.date('F Y').' -->'.PHP_EOL;
			$template .= '<h1>This template ('.$module.'/templates/'.$action.'.tpl.php) is a place holder</h1>';
		}
		file_put_contents($templateDirectory . DIRECTORY_SEPARATOR . $action . '.tpl.php', $template);
		
		
	}
	
	//Install
	$installDirectory = $moduleDirectory . DIRECTORY_SEPARATOR . 'install';
	if(!is_dir($installDirectory)) {
		mkdir($installDirectory);
	}
	file_put_contents($installDirectory . DIRECTORY_SEPARATOR . 'install.sql', $_POST['sql']);
	//Config
	$configTemplate = file_get_contents($modulesDirectory . DIRECTORY_SEPARATOR . 'kickstart' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'config.tpl.php');
	$template = str_replace(array_keys($markers), array_values($markers), $configTemplate);
	file_put_contents($moduleDirectory . DIRECTORY_SEPARATOR . 'config.php', $template);
	//Roles
	$rolesTemplate = file_get_contents($modulesDirectory . DIRECTORY_SEPARATOR . 'kickstart' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'roles.tpl.php');
	$template = str_replace(array_keys($markers), array_values($markers), $rolesTemplate);
	file_put_contents($moduleDirectory . DIRECTORY_SEPARATOR . $markers['###MODULE_NAME###'] . '.roles.php', $template);
	// go back to the list view
	$w->msg("Module kick started!","kickstart/index");
}