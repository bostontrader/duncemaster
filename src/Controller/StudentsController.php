<?php
namespace App\Controller;

class StudentsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $student = $this->Students->newEntity();
        if ($this->request->is('post')) {
            $student = $this->Students->patchEntity($student, $this->request->data);
            if ($this->Students->save($student)) {
                //$this->Flash->success(__('The student has been saved.'));
                return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The student could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('student'));
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $student = $this->Students->get($id);
        if ($this->Students->delete($student)) {
            //$this->Flash->success(__('The student has been deleted.'));
            //} else {
            //$this->Flash->error(__('The student could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'post']);
        $student = $this->Students->get($id);
        if ($this->request->is(['post'])) {
            $student = $this->Students->patchEntity($student, $this->request->data);
            if ($this->Students->save($student)) {
                //$this->Flash->success(__('The student has been saved.'));
                //return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The student could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('student'));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        // We need Cohorts.Majors because the Cohort entity needs this for the nickname virtual field
        $this->set('students', $this->Students->find('all', ['contain' => ['Cohorts.Majors']]));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $student = $this->Students->get($id);
        $this->set('student', $student);
    }
}
