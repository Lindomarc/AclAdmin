<?php
App::uses('AclExtras', 'AclExtras.Lib');
class PermissionsController extends AclAdminAppController
{	
	public $components = array(		
		'AclAdmin.AclUtility'
	);

	public $uses = array(
		'AclAdmin.AclAco',
		'AclAdmin.AclAro',
		'AclAdmin.AclPermission',
		'Role'
	);

	public function beforeFilter()
	{
		parent::beforeFilter();
	}

	public function index()
	{
		
	}
	
	public function consents()
	{
		$acoConditions = array(
			'parent_id !=' => null,
			'foreign_key' => null,
			'alias !=' => null,
		);
		$acos  = $this->Acl->Aco->generateTreeList($acoConditions, '{n}.Aco.id', '{n}.Aco.alias', '#');
		$roles = $this->Role->find('list');
				
		$this->set(compact('acos', 'roles'));

		$rolesAros = $this->AclAro->find('all', array(
			'conditions' => array(
				'AclAro.model' => 'Role',
				'AclAro.foreign_key' => array_keys($roles),
				),
			));
		$rolesAros = Hash::combine($rolesAros, '{n}.AclAro.foreign_key', '{n}.AclAro.id');
		$permissions = array();
		foreach ($acos as $acoId => $acoAlias) {
			if (substr_count($acoAlias, '#') != 0) {
				$permission = array();
				foreach ($roles as $roleId => $roleTitle) {
					$hasAny = array(
						'aco_id'  => $acoId,
						'aro_id'  => $rolesAros[$roleId],
						'_create' => 1,
						'_read'   => 1,
						'_update' => 1,
						'_delete' => 1,
					);
					if ($this->AclPermission->hasAny($hasAny)) {
						$permission[$roleId] = 1;
					} else {
						$permission[$roleId] = 0;
					}
					$permissions[$acoId] = $permission;
				}
			}
		}
		$this->set(compact('rolesAros', 'permissions'));
		$plugins = CakePlugin::loaded();
		$controllers_plugins = array();
		if ( !empty( $plugins ) ) {			
			foreach ($plugins as $plugin) {
				$controllers_plugins[] = $this->AclUtility->getControllerList($plugin);
			}			
		}		
		$controllers = array_merge($this->AclUtility->getControllerList(), $this->AclUtility->getControllerList($plugin));
		$this->set(compact('controllers'));
	}

	public function change() {
		$this->autoRender = false;

		if (!$this->request->is('ajax')) {
			$this->redirect(array('action' => 'index'));
		}
		$acoId = $this->request->data['aco_id'];
		$aroId = $this->request->data['aro_id'];

		// ver se combinação acoId e aroId existe.
		$conditions = array(
			'AclPermission.aco_id' => $acoId,
			'AclPermission.aro_id' => $aroId,
		);
		if ($this->AclPermission->hasAny($conditions)) {
			$data = $this->AclPermission->find('first', array('conditions' => $conditions));
			if ($data['AclPermission']['_create'] == 1 &&
				$data['AclPermission']['_read'] == 1 &&
				$data['AclPermission']['_update'] == 1 &&
				$data['AclPermission']['_delete'] == 1) {
				// de 1 para 0
				$data['AclPermission']['_create'] = 0;
				$data['AclPermission']['_read'] = 0;
				$data['AclPermission']['_update'] = 0;
				$data['AclPermission']['_delete'] = 0;
				$permitted = 0;
			} else {
				// de 0 para 1
				$data['AclPermission']['_create'] = 1;
				$data['AclPermission']['_read'] = 1;
				$data['AclPermission']['_update'] = 1;
				$data['AclPermission']['_delete'] = 1;
				$permitted = 1;
			}
		} else {
			// create - CRUD com 1
			$data['AclPermission']['aco_id'] = $acoId;
			$data['AclPermission']['aro_id'] = $aroId;
			$data['AclPermission']['_create'] = 1;
			$data['AclPermission']['_read'] = 1;
			$data['AclPermission']['_update'] = 1;
			$data['AclPermission']['_delete'] = 1;
			$permitted = 1;
		}

		// salvar
		$return = 0;
		if ($this->AclPermission->save($data)) {
			$return = 1;
		}
		echo $return;
	}
	
	public function sync_acos()
	{
		if ( $this->AclUtility->aco_sync() ) {
			$this->Flash->success(__d('acl_admin', 'All Controllers was sincronized.'));
			$this->redirect(array('action' => 'index'));
		}
		$this->render(false);
	}
	public function aco_sync() {
		if($this->AclExtras->aco_sync($this->params)){
			$this->Flash->success(__d('acl_admin', "ok"));
			$this->redirect(array("action" => "index"));

		};
	}
	
	/**
	 * Delete Acos e Aros
	 */
	public function delete_acos_aros() {
		$this->Acl->Aco->deleteAll(array("1 = 1"));
		$this->Acl->Aro->deleteAll(array("1 = 1"));
		$this->Flash->success(__d('acl_admin', "Both ACOs and AROs have been dropped"));
		$this->redirect(array("action" => "index"));
	}
	
	/**
	 * Delete todas as permissões
	 */
	public function delete_permissions() {
		if ($this->Acl->Aro->Permission->deleteAll(array("1 = 1"))) {
			$this->Flash->success(__d('acl_admin', 'Permissions dropped'));
		} else {
			$this->Flash->error(__d('acl_admin', 'Error while trying to drop permissions'));
		}
		$this->redirect(array("action" => "index"));
	}

}
