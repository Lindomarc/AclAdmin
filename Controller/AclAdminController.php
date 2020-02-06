<?php
class AclAdminController  extends AclAdminAppController
{
	public function index()
	{
		$this->redirect(array('plugin' => 'AclAdmin', 'controller' => 'Permissions'));
	}
}