<?php
namespace App\Controller;

class CohortsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $cohort = $this->Cohorts->newEntity();
        if ($this->request->is('post')) {
            $cohort = $this->Cohorts->patchEntity($cohort, $this->request->data);
            if ($this->Cohorts->save($cohort)) {
                //$this->Flash->success(__('The cohort has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The cohort could not be saved. Please, try again.'));
            }
        }
        $majors = $this->Cohorts->Majors->find('list');
        $this->set(compact('cohort', 'majors'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $cohort = $this->Cohorts->get($id);
        if ($this->Cohorts->delete($cohort)) {
            //$this->Flash->success(__('The cohort has been deleted.'));
            //} else {
            //$this->Flash->error(__('The cohort could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $cohort = $this->Cohorts->get($id);
        if ($this->request->is(['put'])) {
            $cohort = $this->Cohorts->patchEntity($cohort, $this->request->data);
            if ($this->Cohorts->save($cohort)) {
                //$this->Flash->success(__('The cohort has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The cohort could not be saved. Please, try again.'));
            }
        }
        $majors = $this->Cohorts->Majors->find('list');
        $this->set(compact('cohort', 'majors'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('cohorts', $this->Cohorts->find()->contain(['Majors'])->order(['start_year','Majors.sdesc','seq']));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $cohort = $this->Cohorts->get($id, ['contain' => ['Majors']]);
        $this->set('cohort', $cohort);
    }
}
