<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;

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
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('cohorts', $this->Cohorts->find()->contain(['Majors']));
    }

    // Only admin users can work with cohorts
    public function isAuthorized($userArray) {
        $users = TableRegistry::get('Users');
        $user=$users->get($userArray['id'], ['contain' => ['Roles']]);
        foreach($user->roles as $role) {
            if($role->title=='admin') return true;
        }
        return false;
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $cohort = $this->Cohorts->get($id, ['contain' => ['Majors']]);
        $this->set('cohort', $cohort);
    }
}
