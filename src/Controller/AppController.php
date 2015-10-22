<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\I18n;


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
                ]
            ]
        );

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

    /**
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event) {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
        $username = $this->Auth->user('username');
        $this->set('currentUser',$username);
    }
}
