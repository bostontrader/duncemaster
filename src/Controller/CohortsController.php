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
        //$n = $majors->execute();
        //$majors = ['1'=>'Tourist English','2'=>'Aviation','3'=>'Hotel'];
        $this->set(compact('cohort', 'majors'));
        //$this->set('cohort', $cohort);
        //$this->set('majors',$majors);
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
        $this->request->allowMethod(['get', 'post']);
        $cohort = $this->Cohorts->get($id);
        if ($this->request->is(['post'])) {
            $cohort = $this->Cohorts->patchEntity($cohort, $this->request->data);
            if ($this->Cohorts->save($cohort)) {
                //$this->Flash->success(__('The cohort has been saved.'));
                return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The cohort could not be saved. Please, try again.'));
            }
        }
        $majors = $this->Cohorts->Majors->find('list');
        //$query = $this->Cohorts->Majors->find();
        //$majors = $this->Cohorts->Majors->findList($query,['fields'=>['id','title']]);
        //$majors = ['1'=>'Tourist English','2'=>'Aviation','3'=>'Hotel'];
        $this->set(compact('cohort', 'majors'));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('cohorts', $this->Cohorts->find()->contain(['Majors']));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $cohort = $this->Cohorts->get($id);
        $this->set('cohort', $cohort);
    }
}
