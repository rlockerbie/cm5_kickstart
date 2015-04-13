<?php
/**
 * ###MODULE_DB###Service class
 * 
 * @author ###AUTHOR###, ###DATE###
 */
class ###MODULE_DB###Service extends DbService {
	
	/** 
	 * @return an array of all undeleted ###MODULE_DB### records from the database
	 */
	function getAllData() {
		return $this->getObjects("###MODULE_DB###",array("is_deleted" => 0));
	}
	
	/**
	 * @param integer $id
	 * @return an ExampleData object for this id
	 */
	function getDataForId($id) {
		return $this->getObject("###MODULE_DB###",$id);
	}
	
	/**
	 * Generate a list of menu entries which will go into a drop down
	 * under the module name.
	 * 
	 * @param Web $w
	 * @param string $title (not in use)
	 * @param string $nav (not  in use)
	 * @return array of menu entries
	 */
	public function navigation(Web $w, $title = null, $nav = null) {
		$nav = array();
		if ($w->Auth->loggedIn()) {
			$w->menuLink("###MODULE_NAME###/index", "Home", $nav);
		}
		return $nav;
	}	
}