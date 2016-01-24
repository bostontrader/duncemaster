<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;

class AppController extends Controller {

    public function initialize() {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent(
            'Auth', [
                'logoutRedirect' => [
                    'controller' => 'Pages',
                    'action' => 'display',
                    'home'
                ],
                'authorize' => 'Controller',
                'unauthorizedRedirect' => '/'  // authenticated, but unauthorized
            ]
        );

        // Now set role flags
        $user_id=$this->Auth->user('id');
        if(!is_null($user_id)) {
            $users = TableRegistry::get('Users');
            $user=$users->get($user_id, ['contain' => ['Roles']]);
            $this->isAdmin = false;
            $this->isTeacher = false;
            foreach($user->roles as $role) {
                if($role->title=='admin') $this->isAdmin=true;
                if($role->title=='teacher') $this->isTeacher=true;
            }
        }

        // The language is initially set in bootstrap.php.  If we want to change the language on-the-fly,
        // such as in clicking the little flags, then we need to store the desired language in the session,
        // retrieve that value here, and use that value to override the default language.  Even though the
        // default in bootstrap.php is en_EN, we still need to set it here.
        $lang=$this->request->session()->read('Config.language');
        switch($lang) {
            case "zh_CN":
                I18n::locale('zh_CN');
                break;
            case "zh_PN":
                I18n::locale('zh_PN');
                break;
            case "en_US":
                I18n::locale('en_US');
                break;
        }
    }

    // Nothing is authorized unless a controller says so.
    // Admin is always authorized.
    public function isAuthorized($userArray) {
        $users = TableRegistry::get('Users');
        $user=$users->get($userArray['id'], ['contain' => ['Roles']]);
        foreach($user->roles as $role) {
            if($role->title=='admin') return true;
        }
        return false;
    }

    /**
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event) {
        $this->set('currentUser',$this->Auth->user());
        $this->set('isAdmin',$this->isAdmin);
        $this->set('isTeacher',$this->isTeacher);
    }
}
