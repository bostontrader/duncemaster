<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\I18n\I18n;

class I18nController extends AppController {

	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
		$this->Auth->allow(['chi','eng','pin']);
	}

	function chi() {
        $this->autoRender = false;
		$this->request->session()->write('Config.language', 'zh_CN');
		$this->redirect($this->referer());
	}

	function eng() {
        $this->autoRender = false;
		$this->request->session()->write('Config.language', 'en_US');
		$this->redirect($this->referer());
	}

    // Pinyin
	//function pin() {
        //$this->autoRender = false;
		//$this->redirect($this->referer());
	//}

}
?>