<?php
namespace App\Controller;

class InteractionsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $interaction = $this->Interactions->newEntity();
        if ($this->request->is('post')) {
            $interaction = $this->Interactions->patchEntity($interaction, $this->request->data);
            if ($this->Interactions->save($interaction)) {
                //$this->Flash->success(__('The interaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The interaction could not be saved. Please, try again.'));
            }
        }
        $clazzes = $this->Interactions->Clazzes->find('list');
        $students = $this->Interactions->Students->find('list');
        $itypes = $this->Interactions->Itypes->find('list');
        $this->set(compact('clazzes','interaction','itypes','students'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $interaction = $this->Interactions->get($id);
        if ($this->Interactions->delete($interaction)) {
            //$this->Flash->success(__('The interaction has been deleted.'));
            //} else {
            //$this->Flash->error(__('The interaction could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'post']);
        $interaction = $this->Interactions->get($id);
        if ($this->request->is(['post'])) {
            $interaction = $this->Interactions->patchEntity($interaction, $this->request->data);
            if ($this->Interactions->save($interaction)) {
                //$this->Flash->success(__('The interaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The interaction could not be saved. Please, try again.'));
            }
        }
        $clazzes = $this->Interactions->Clazzes->find('list');
        $itypes = $this->Interactions->Itypes->find('list');
        $students = $this->Interactions->Students->find('list');
        $this->set(compact('clazzes','interaction','itypes','students'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('interactions', $this->Interactions->find('all', ['contain' => ['Clazzes','Itypes','Students']]));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $interaction = $this->Interactions->get($id, ['contain' => ['Clazzes','Itypes','Students']]);
        $this->set('interaction', $interaction);
    }
}
