	## appController.php
    
    public $components = array(
        'Acl',
        'AclAdmin.AclPermissions',
        'Auth' => array(
            'authorize' => array(
                'Controller',
                'Actions' => array('actionPath' => 'controllers')
            ),
            'loginAction' => array(
                'controller' => 'Users',
                'action' => 'login'
            ),
            'loginRedirect' => array(
                'controller' => 'Dash',
                'action' => 'index'
            ),
            'logoutRedirect'    => array(
                'controller' => 'Users', 
                'action' => 'login'
            ),
            'authError'         => 'Você não está autorizado a acessar está página.',
            'flash' => array(
                'element' => 'warning'
            ),
            'authenticate' => array(
                'Form' => array(
                    'fields' => array(
                        'username' => 'email',
                        'password' => 'password'
                    ),
                    'scope' => array(
                        'User.ativo' => true
                    ),
                    'userModel' => 'User'
                )
            ),
            'loginRedirect' => array(
                'controller' => 'Dash',
                'action' => 'index'
            ),
        )
    );

    public function isAuthorized($user) {
        $this->Flash->info("Acesso não autorizado");
        $this->redirect($this->referer());
        return false;
    }


    public function beforeFilter(){
        $this->AclPermissions->sessionControl();
    }
    