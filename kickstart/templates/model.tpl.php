<?php
/**
 * ###MODULE_DB### class
 * 
 * @author ###AUTHOR###, ###DATE###
 */
class ###MODULE_DB### extends DbObject {
	
	// object properties
	
###FIELDS###
	
	// this makes it searchable
	
	public $_searchable;
	
	// functions for how to behave when displayed in search results
	
	public function printSearchTitle() {
		return $this->title;
	}
	
	public function printSearchListing() {
		return $this->data;
	}
	
	public function printSearchUrl() {
		return "###MODULE_NAME###/show/".$this->id;
	}		
	
	// functions for implementing access restrictions, these are optional

	public function canList(User $user) {
		return $user !== null && $user->hasAnyRole(array("###MODULE_NAME###_admin"));
	}
	
	public function canView(User $user) {
		return $user !== null && $user->hasAnyRole(array("###MODULE_NAME###_admin"));
	}
	
	public function canEdit(User $user) {
		return $user !== null && $user->hasAnyRole(array("###MODULE_NAME###_admin"));
	}
	
	public function canDelete(User $user) {
		return $user !== null && $user->hasAnyRole(array("###MODULE_NAME###_admin"));
	}	
	
	// functions for how to display inside a dropdown, these are optional
	
	public function getSelectOptionTitle() {
		return $this->title;
	}
	
	public function getSelectOptionValue() {
		return $this->id;
	}
	
	// override this function to add stuff to the search index
	// DO NOT CALL $this->getIndexContent() within this function
	// or you will create an endless loop which will destroy the universe!	

	function addToIndex() {
		return null;
	}	
	
	// you could override these functions, but only if you must, 
	// otherwise just delete them from this class
	
	public function update($force_nullvalues = false, $force_validation = false ) {
		parent::update($force_nullvalues, $force_validation);
	}

	public function insert($force_validation = false ) {
		parent::insert($force_validation);
	}
	
	public function delete($force = false ) {
		parent::delete($force);
	}
	
}