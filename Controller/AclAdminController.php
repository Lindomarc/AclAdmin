<?php
/**
 * Admin AppController
 * @author Lu�s Fred G S <luis.fred.gs@gmail.com>
 * @category Controller
 * @package Plugin.Admin
 */

class AclAdminController  extends AclAdminAppController
{
	public function index()
	{
		$this->redirect(array('plugin' => 'AclAdmin', 'controller' => 'Permissions'));
	}
}