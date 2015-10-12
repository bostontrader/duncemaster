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
                //} else {
                //$this->Flash->error(__('The section could not be saved. Please, try again.'));
            }
        }
        //$cohorts = $this->Sections->Cohorts->find('list', ['limit' => 200])->select(['id', 'start_year']);
        //$subjects = $this->Sections->Subjects->find('list', ['limit' => 200]);
        //$list = $articles->find('list')->select(['id', 'title']);
        //$this->set(compact('section', 'cohorts', 'subjects'));
        $this->set(compact('sections'));
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
        $this->request->allowMethod(['get', 'post']);

        $section = $this->Sections->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $section = $this->Sections->patchEntity($section, $this->request->data);
            if ($this->Sections->save($section)) {
                //$this->Flash->success(__('The section has been saved.'));
                //return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The section could not be saved. Please, try again.'));
            }
        }
        //$cohorts = $this->Sections->Cohorts->find('list', ['limit' => 200]);
        //$subjects = $this->Sections->Subjects->find('list', ['limit' => 200]);
        //$this->set(compact('section', 'cohorts', 'subjects'));
        $this->set(compact('section'));
    }
    public function index() {
        $this->request->allowMethod(['get']);
        //$this->set('sections', $this->Sections->find('all', ['contain' => ['Cohorts.Majors','Subjects']]));
        $this->set('sections', $this->Sections->find('');
    }


    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $section = $this->Sections->get($id);
        $this->set('section', $section);
    }



}
