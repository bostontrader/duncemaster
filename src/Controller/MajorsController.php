<?php
namespace App\Controller;

class MajorsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $major = $this->Majors->newEntity();
        if ($this->request->is('post')) {
            $major = $this->Majors->patchEntity($major, $this->request->data);
            if ($this->Majors->save($major)) {
                // $this->Flash->success(__('The major has been saved.'));
                return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The major could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('major'));
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $major = $this->Majors->get($id);
        if ($this->Majors->delete($major)) {
            //$this->Flash->success(__('The major has been deleted.'));
            //} else {
            //$this->Flash->error(__('The major could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'post']);

        $major = $this->Majors->get($id);
        if ($this->request->is(['post'])) {
            $major = $this->Majors->patchEntity($major, $this->request->data);
            if ($this->Majors->save($major)) {
                //$this->Flash->success(__('The major has been saved.'));
                //return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The major could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('major'));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('majors', $this->Majors->find());
    }

    public function view($id = null) {
        $major = $this->Majors->get($id);
        $this->set('major', $major);
    }
}
