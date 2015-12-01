<?php
namespace App\Controller;

class TplanElementsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $tplan_element = $this->TplanElements->newEntity();
        if ($this->request->is('post')) {
            $tplan_element = $this->TplanElements->patchEntity($tplan_element, $this->request->data);
            if ($this->TplanElements->save($tplan_element)) {
                //$this->Flash->success(__('The tplan_element has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The tplan_element could not be saved. Please, try again.'));
            }
        }
        $tplans = $this->TplanElements->Tplans->find('list');
        $this->set(compact('tplan_element','tplans'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $tplan_element = $this->TplanElements->get($id);
        if ($this->TplanElements->delete($tplan_element)) {
            //$this->Flash->success(__('The tplan_element has been deleted.'));
            //} else {
            //$this->Flash->error(__('The tplan_element could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $tplan_element = $this->TplanElements->get($id);
        if ($this->request->is(['put'])) {
            $tplan_element = $this->TplanElements->patchEntity($tplan_element, $this->request->data);
            if ($this->TplanElements->save($tplan_element)) {
                //$this->Flash->success(__('The tplan_element has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The tplan_element could not be saved. Please, try again.'));
            }
        }
        $tplans = $this->TplanElements->Tplans->find('list');
        $this->set(compact('tplan_element','tplans'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('tplan_elements', $this->TplanElements->find()->contain(['Tplans']));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $tplan_element = $this->TplanElements->get($id, ['contain' => ['Tplans']]);
        $this->set('tplan_element', $tplan_element);
    }
}
