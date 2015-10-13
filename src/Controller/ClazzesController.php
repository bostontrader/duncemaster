<?php
namespace App\Controller;

class ClazzesController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $clazze = $this->Clazzes->newEntity();
        if ($this->request->is('post')) {
            $clazz = $this->Clazzes->patchEntity($clazze, $this->request->data);
            if ($this->Clazzes->save($clazz)) {
                //$this->Flash->success(__('The clazze has been saved.'));
                return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The clazze could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('clazz'));
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $clazz = $this->Clazzes->get($id);
        if ($this->Clazzes->delete($clazz)) {
            //$this->Flash->success(__('The clazze has been deleted.'));
            //} else {
            //$this->Flash->error(__('The clazze could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'post']);
        $clazz = $this->Clazzes->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clazz = $this->Clazzes->patchEntity($clazz, $this->request->data);
            if ($this->Clazzes->save($clazz)) {
                //$this->Flash->success(__('The clazze has been saved.'));
                //return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The clazze could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('clazze', 'sections'));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('clazzes', $this->Clazzes->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $clazz = $this->Clazzes->get($id);
        $this->set('clazz', $clazz);
    }
}
