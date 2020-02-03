<?php
class AclPermissionsComponent extends Component
{
	protected $controller = null;

	/**
	 *  após beforeFilter mas antes de executar action.
	 */
	public function startup(Controller $controller)
	{
		$this->controller = $controller;
	}

	/**
	 * 	antes beforeFilter do controller. 
	 */
	public function initialize(Controller $controller)
	{
		$this->controller = $controller;
	}

	/**
	 * controle de acesso para todas as seções no aplicativo
	 **/
	public function sessionControl()
	{
		$this->controller->Auth->authenticate = array(
			'all' => array(
				'userModel' => 'User',
				'fields' => array(
					'username' => 'email',
					'password' => 'password',
				),
				'scope' => array(
					'User.ativo' => 1,
				),
			),
			'Form',
		);

		$this->controller->Auth->authorize = array(
			AuthComponent::ALL => array('actionPath' => 'controllers/'),
			'Actions',
			'Controller'
		);

		$this->controller->Auth->loginAction = array(
			'controller' => 'users',
			'action' => 'login',
		);

		$this->controller->Auth->logoutRedirect = array(
			'controller' => 'users',
			'action' => 'login',
		);

		$this->controller->Auth->loginRedirect = array(
			'controller' => 'dash',
			'action' => 'index',
		);

		$this->controller->Auth->unauthorizedRedirect = array(
			'controller' => 'users',
			'action' => 'login',
		);

		$this->controller->Auth->flash = array('element' => 'warning');
		$this->controller->Auth->authError = __d('admin', 'You have no authority to access this router.');

		if ($this->controller->Auth->user()) {
			// se não é admin role_id = 1
			if ($this->controller->Auth->user('role_id') != 1) {

				if ($this->controller->Auth->user()) {
					$roleID = $this->controller->Auth->user('role_id');
				} else {
					$id = ClassRegistry::init('Role')->field('id', array('alias' => 'public'));
					ClassRegistry::init('Role')->id = $id;
					$roleID =  ClassRegistry::init('Role')->exists() ? $id : 3;
				}

				$aro = $this->controller->Acl->Aro->find('first', array(
					'conditions' => array(
						'Aro.model' => 'Role',
						'Aro.foreign_key' => $roleID
					)
				));

				$aroId = $aro['Aro']['id'];

				$thisControllerNode = $this->controller->Acl->Aco->node('controllers' . $this->controller->name);
				$this->log($thisControllerNode);
				if ($thisControllerNode) {
					$thisControllerActions = $this->controller->Acl->Aco->find('list', array(
						'conditions' => array(
							'Aco.parent_id' => $thisControllerNode['0']['Aco']['id']
						),
						'fields' => array(
							'Aco.id',
							'Aco.alias'
						),
						'recursive' => '-1'
					));


					$thisControllerActionsIds = array_keys($thisControllerActions);

					$allowedActions = $this->controller->Acl->Aco->Permission->find('list', array(
						'conditions' => array(
							'Permission.aro_id' => $aroId,
							'Permission.aco_id' => $thisControllerActionsIds,
							'Permission._create' => 1,
							'Permission._read' => 1,
							'Permission._update' => 1,
							'Permission._delete' => 1,
						),
						'fields' => array(
							'id',
							'aco_id'
						),
						'recursive' => '-1'
					));

					$allowedActionsIds = array_values($allowedActions);
				}
				$allow = array();

				if (isset($allowedActionsIds) && is_array($allowedActionsIds) && count($allowedActionsIds)) {
					foreach ($allowedActionsIds as $i => $aId) {
						$allow[] = $thisControllerActions[$aId];
					}
				}

				$this->controller->Auth->allowedActions = $allow;
			}

			// admin tem permição total
			if ($this->controller->Auth->user('role_id') == '1') {
				$this->controller->Auth->allow();
			}
		}
	}
}
