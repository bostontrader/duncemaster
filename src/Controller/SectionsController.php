<?php
namespace App\Controller;

class SectionsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $section = $this->Sections->newEntity();
        if ($this->request->is('post')) {
            $section = $this->Sections->patchEntity($section, $this->request->data);
            if ($this->Sections->save($section)) {
                //$this->Flash->success(__('The section has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The section could not be saved. Please, try again.'));
            }
        }
        $cohorts = $this->Sections->Cohorts->find('list',['contain' => ['Majors']]);
        $semesters = $this->Sections->Semesters->find('list');
        $subjects = $this->Sections->Subjects->find('list');
        $tplans = $this->Sections->Tplans->find('list');
        $this->set(compact('cohorts','section','semesters','subjects','tplans'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $section = $this->Sections->get($id);
        if ($this->Sections->delete($section)) {
            //$this->Flash->success(__('The section has been deleted.'));
            //} else {
            //$this->Flash->error(__('The section could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $section = $this->Sections->get($id);
        if ($this->request->is(['put'])) {
            $section = $this->Sections->patchEntity($section, $this->request->data);
            if ($this->Sections->save($section)) {
                //$this->Flash->success(__('The section has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The section could not be saved. Please, try again.'));
            }
        }
        $cohorts = $this->Sections->Cohorts->find('list',['contain' => ['Majors']]);
        $semesters = $this->Sections->Semesters->find('list');
        $subjects = $this->Sections->Subjects->find('list');
        $tplans = $this->Sections->Tplans->find('list');
        $this->set(compact('cohorts','section','semesters','subjects','tplans'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('sections', $this->Sections->find('all', ['contain' => ['Cohorts.Majors','Semesters','Subjects','Tplans']]));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $section = $this->Sections->get($id,['contain'=>['Cohorts.Majors','Semesters','Subjects','Tplans']]);
        $this->set('section', $section);
    }
}
