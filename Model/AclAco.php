<?php
class AclAco extends AclAdminAppModel 
{
	public $useTable = 'acos';
	public $actsAs = array('Tree');
}
