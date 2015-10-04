<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\I18n;

	class I18nController extends AppController {

		function chi() {
            $this->autoRender = false;
			I18n::locale('zh_CN');
			I18n::clear();
			$this->redirect($this->referer());
		}

		function eng() {
            $this->autoRender = false;
			I18n::locale('en_US');
			$this->request->session()->write('Config.language', 'en_US');
			I18n::clear();
			$this->redirect($this->referer());
		}

        // Pinyin
		function pin() {
            $this->autoRender = false;
			$this->redirect($this->referer());
		}

	}
?>