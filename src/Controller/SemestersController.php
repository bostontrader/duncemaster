<?php
namespace App\Controller;

class SemestersController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $semester = $this->Semesters->newEntity();
        if ($this->request->is('post')) {
            $semester = $this->Semesters->patchEntity($semester, $this->request->data);
            if ($this->Semesters->save($semester)) {
                //$this->Flash->success(__('The semester has been saved.'));
                return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The semester could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('semester'));
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $semester = $this->Semesters->get($id);
        if ($this->Semesters->delete($semester)) {
            //$this->Flash->success(__('The semester has been deleted.'));
            //} else {
            //$this->Flash->error(__('The semester could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $semester = $this->Semesters->get($id);
        if ($this->request->is(['put'])) {
            $semester = $this->Semesters->patchEntity($semester, $this->request->data);
            if ($this->Semesters->save($semester)) {
                //$this->Flash->success(__('The semester has been saved.'));
                return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The semester could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('semester'));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('semesters',$this->Semesters->find()->order(['year','seq']));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $semester = $this->Semesters->get($id);
        $this->set('semester', $semester);
    }
}
