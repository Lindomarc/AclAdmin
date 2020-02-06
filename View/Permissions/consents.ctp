<div class="x_panel ">
	<div class="x_title">
		<h2>Permissões</h2>
		<div class="clearfix"></div>
	</div>
	<div class="table-responsive">
		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-striped jambo_table bulk_action">
						<?php
						$roleTitles = array_values($roles);
						$roleIds   = array_keys($roles);

						$tableHeaders = array(
							__d('acl_admin', 'Section')
						);
						$tableHeaders = array_merge($tableHeaders, $roleTitles);
						$tableHeaders =  $this->Html->tag('thead', $this->Html->tableHeaders($tableHeaders));
						echo $tableHeaders;

						$currentController = '';
						foreach ($controllers as $controller) {
							$controllerName[] = preg_replace('/Controller$/', '', $controller);
						}

						foreach ($acos as $id => $alias) {
							$class = '';
							if (substr($alias, 0, 1) == '#') {
								$level = 1;
								$class .= 'level-' . $level;
								$oddOptions = array('class' => 'controller-' . $currentController);
								$evenOptions = array('class' => 'controller-' . $currentController);
								$alias = substr_replace($alias, '', 0, 1);
								if (substr($alias, 0, 1) == '#') {
									$alias = substr_replace($alias, '', 0, 1);
									$alias = $this->Html->tag('span', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&rarr;  ', array('class' => 'bulet')) . preg_replace('/\_/', ' ', ucfirst($alias));
								} else {
									if (in_array($alias, $controllerName)) {
										$alias = $this->Html->tag('div', '&nbsp;&nbsp;  ' . preg_replace('/\_/', ' ', ucfirst($alias)), array('class' => 'bold'));
									} else {
										$alias = $this->Html->tag('span', '&nbsp;&nbsp;&rarr;  ', array('class' => 'bulet')) . preg_replace('/\_/', ' ', ucfirst($alias));
									}
								}
							} else {
								$level = 0;
								$class .= ' controller expand bold';
								$oddOptions = array();
								$evenOptions = array();
								$currentController =  $alias;
							}

							$row = array(
								$this->Html->div($class, $alias),
							);

							foreach ($roles as $roleId => $roleTitle) {
								if ($level != 0) {
									if ($roleId != 1) {
										if ($permissions[$id][$roleId] === 1) {
											$row[] = $this->Html->tag('span', __d('acl_admin', 'allowed'), array(
												'class' => 'label label-success permission-toggle',
												'data-aco_id' => $id,
												'data-aro_id' => $rolesAros[$roleId],
												'data-toggle' => 'tooltip',
												'data-placement' => 'left',
												'data-original-title' => $roleTitle
											));
										} else {
											$row[] = $this->Html->tag('span', __d('acl_admin', 'denied'), array(
												'class' => 'label label-danger permission-toggle',
												'data-aco_id' => $id,
												'data-aro_id' => $rolesAros[$roleId],
												'data-toggle' => 'tooltip',
												'data-placement' => 'left',
												'data-original-title' => $roleTitle
											));
										}
									} else {
										$row[] = $this->Html->tag('span', __d('acl_admin', 'allowed'), array('class' => 'permission-disabled label label-default'));
									}
								} else {
									$row[] = '';
								}
							}

							echo $this->Html->tableCells(array($row), $oddOptions, $evenOptions);
						}
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$app = array();
$app['basePath'] = Router::url('/');
$app['params'] = array(
	'controller' => $this->params['controller'],
	'action' => $this->params['action'],
	'named' => $this->params['named'],
);
echo $this->Html->scriptBlock('var App = ' . $this->Js->object($app) . ';');
echo $this->Html->script('/acl_admin/js/acl.js', array('block' => 'scriptBottom'));

