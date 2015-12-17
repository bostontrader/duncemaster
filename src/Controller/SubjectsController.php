<?php
namespace App\Controller;

class SubjectsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $subject = $this->Subjects->newEntity();
        if ($this->request->is('post')) {
            $subject = $this->Subjects->patchEntity($subject, $this->request->data);
            if ($this->Subjects->save($subject)) {
                $this->Flash->success(__('The subject has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The subject could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('subject'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $subject = $this->Subjects->get($id);
        if ($this->Subjects->delete($subject)) {
            //$this->Flash->success(__('The subject has been deleted.'));
            //} else {
            //$this->Flash->error(__('The subject could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $subject = $this->Subjects->get($id);
        if ($this->request->is(['put'])) {
            $subject = $this->Subjects->patchEntity($subject, $this->request->data);
            if ($this->Subjects->save($subject)) {
                //$this->Flash->success(__('The subject has been saved.'));
                return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The subject could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('subject'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('subjects', $this->Subjects->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $subject = $this->Subjects->get($id);
        $this->set('subject', $subject);
    }
}
