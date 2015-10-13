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
                //} else {
                //$this->Flash->error(__('The teacher could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('teacher'));
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
        $this->request->allowMethod(['get', 'post']);
        $teacher = $this->Teachers->get($id);
        if ($this->request->is(['post'])) {
            $teacher = $this->Teachers->patchEntity($teacher, $this->request->data);
            if ($this->Teachers->save($teacher)) {
                //$this->Flash->success(__('The teacher has been saved.'));
                //return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The teacher could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('teacher'));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('teachers', $this->paginate($this->Teachers));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $teacher = $this->Teachers->get($id);
        $this->set('teacher', $teacher);
    }
}
