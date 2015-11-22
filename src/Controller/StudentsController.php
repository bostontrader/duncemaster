<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;

class StudentsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $student = $this->Students->newEntity();
        if ($this->request->is('post')) {
            $student = $this->Students->patchEntity($student, $this->request->data);
            if ($this->Students->save($student)) {
                //$this->Flash->success(__('The student has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The student could not be saved. Please, try again.'));
            }
        }
        $cohorts = $this->Students->Cohorts->find('list',['contain' => ['Majors']]);
        $users = $this->Students->Users->find('list');
        $this->set(compact('cohorts','student','users'));
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
        $this->request->allowMethod(['get', 'put']);
        $student = $this->Students->get($id);
        if ($this->request->is(['put'])) {
            $student = $this->Students->patchEntity($student, $this->request->data);
            if ($this->Students->save($student)) {
                //$this->Flash->success(__('The student has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The student could not be saved. Please, try again.'));
            }
        }
        $cohorts = $this->Students->Cohorts->find('list',['contain' => ['Majors']]);
        $users = $this->Students->Users->find('list');
        $this->set(compact('cohorts','student','users'));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('students', $this->Students->find('all', ['contain' => ['Cohorts.Majors','Users']]));
    }

    public function view($id = null) {

        $this->request->allowMethod(['get']);

        $student = $this->Students->get($id,['contain' => ['Cohorts.Majors','Users']]);

        $Sections = TableRegistry::get('Sections');
        $sections_list = $Sections->find('list');

        if(array_key_exists('section_id', $this->request->data)) {
            $this->loadComponent('Grader');
            $grade = $this->Grader->getGradeInfo(null, null);
            $this->set('grade', $grade);
            $section_id=$this->request->data['section_id'];
        } else {
            $section_id="";
        }

        $this->set('section_id', $section_id);
        $this->set('sections_list', $sections_list);
        $this->set('student', $student);
    }
}
