<?php
namespace App\Controller;

class ItypesController extends AppController {

    const ATTEND=1;
    const EJECT=2;

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $itype = $this->Itypes->newEntity();
        if ($this->request->is('post')) {
            $itype = $this->Itypes->patchEntity($itype, $this->request->data);
            if ($this->Itypes->save($itype)) {
                // $this->Flash->success(__('The itype has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The itype could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('itype'));
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $itype = $this->Itypes->get($id);
        if ($this->Itypes->delete($itype)) {
            //$this->Flash->success(__('The itype has been deleted.'));
            //} else {
            //$this->Flash->error(__('The itype could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $itype = $this->Itypes->get($id);
        if ($this->request->is(['put'])) {
            $itype = $this->Itypes->patchEntity($itype, $this->request->data);
            if ($this->Itypes->save($itype)) {
                //$this->Flash->success(__('The itype has been saved.'));
                return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The itype could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('itype'));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('itypes', $this->Itypes->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $itype = $this->Itypes->get($id);
        $this->set('itype', $itype);
    }
}
