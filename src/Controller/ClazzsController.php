<?php
namespace App\Controller;

class ClazzsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $clazz = $this->Clazzs->newEntity();
        if ($this->request->is('post')) {
            $clazz = $this->Clazzs->patchEntity($clazz, $this->request->data);
            if ($this->Clazzs->save($clazz)) {
                $this->Flash->success(__('The clazz has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The clazz could not be saved. Please, try again.'));
            }
        }
        $sections = "";
        $this->set(compact('sections'));
        $this->set(compact('clazz'));
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $clazz = $this->Clazzs->get($id);
        if ($this->Clazzs->delete($clazz)) {
            //$this->Flash->success(__('The clazz has been deleted.'));
            //} else {
            //$this->Flash->error(__('The clazz could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'post']);
        $clazz = $this->Clazzs->get($id);
        if ($this->request->is(['post'])) {
            $clazz = $this->Clazzs->patchEntity($clazz, $this->request->data);
            if ($this->Clazzs->save($clazz)) {
                $this->Flash->success(__('The clazz has been saved.'));
                //return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The clazz could not be saved. Please, try again.'));
            }
        }
        $sections = "";
        $this->set(compact('sections'));
        $this->set(compact('clazz', 'sections'));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('clazzs', $this->Clazzs->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $clazz = $this->Clazzs->get($id);
        $this->set('clazz', $clazz);
    }
}
