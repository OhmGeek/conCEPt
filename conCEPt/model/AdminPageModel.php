<?php
namespace Concept\Model;
class AdminPageModel
{

	public function getStaffID() {
		return $_SERVER['REMOTE_USER']; 
	}

}
