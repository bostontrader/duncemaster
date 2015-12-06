<?php
namespace App\Controller;

class TeachersController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $teacher = $this->Teachers->newEntity();
        if ($this->request->is('post')) {
            $teacher = $this->Teachers->patchEntity($teacher, $this->request->data);
            if ($this->Teachers->save($teacher)) {
                //$this->Flash->success(__('The teacher has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The teacher could not be saved. Please, try again.'));
            }
        }
        $users = $this->Teachers->Users->find('list');
        $this->set(compact('teacher','users'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $teacher = $this->Teachers->get($id);
        if ($this->Teachers->delete($teacher)) {
            //$this->Flash->success(__('The teacher has been deleted.'));
            //} else {
            //$this->Flash->error(__('The teacher could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $teacher = $this->Teachers->get($id);
        if ($this->request->is(['put'])) {
            $teacher = $this->Teachers->patchEntity($teacher, $this->request->data);
            if ($this->Teachers->save($teacher)) {
                //$this->Flash->success(__('The teacher has been saved.'));
                return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The teacher could not be saved. Please, try again.'));
            }
        }
        $users = $this->Teachers->Users->find('list');
        $this->set(compact('teacher','users'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('teachers', $this->Teachers->find()->contain(['Users']));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $teacher = $this->Teachers->get($id, ['contain' => ['Users']]);
        $this->set('teacher', $teacher);
    }
}
