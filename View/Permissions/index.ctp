<div class="x_panel">
	<div class="x_title">
		<h2>Permissões</h2>
		<div class="clearfix"></div>
	</div>
	<div class="x_content">
		<?php
			echo $this->Html->link(
				__d('acl_admin', 'Permissions'),
				array(
					'plugin' => 'acl_admin',
					'controller' => 'permissions',
					'action' => 'consents'
				),
				array('class' => 'btn btn-warning')
			);
			echo $this->Html->link(
				__d('acl_admin', 'Sincronize %s', array('Acos')),
				array(
					'plugin' => 'acl_admin', 
					'controller' => 'permissions', 
					'action' => 'sync_acos'
				),
				array('class' => 'btn btn-dark')
			);
			echo $this->Html->link(
				__d('acl_admin', 'Reset Permissions'),
				array(
					'plugin' => 'acl_admin', 
					'controller' => 'permissions', 
					'action' => 'delete_permissions'
				),
				array(
					'class' => 'btn btn-danger',
					'confirm'  => __d('acl_admin', 'Delete all permitions, it is irreversible')
				)
			);
			echo $this->Html->link(
				__d('acl_admin', 'Reset Acos/Aros'),
				array(
					'plugin' => 'acl_admin', 
					'controller' => 'permissions', 
					'action' => 'delete_acos_aros'
				),
				array(
					'class' => 'btn btn-danger',
					'confirm'  => __d('acl_admin', 'Delete all Acos and Aros, it is irreversible')
				)
			);

		?>
	</div>
</div>