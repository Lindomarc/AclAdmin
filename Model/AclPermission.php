<?php
class AclPermission extends AclAdminAppModel {

	public $useTable = 'aros_acos';

	public $belongsTo = array(
		'AclAro' => array(
			'className' => 'AclAdmin.AclAro',
			'foreignKey' => 'aro_id',
		),
		'AclAco' => array(
			'className' => 'AclAdmin.AclAco',
			'foreignKey' => 'aco_id',
		),
	);

}
