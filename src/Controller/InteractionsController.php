<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Interactions Controller
 *
 * @property \App\Model\Table\InteractionsTable $Interactions
 */
class InteractionsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('interactions', $this->paginate($this->Interactions));
        $this->set('_serialize', ['interactions']);
    }

    /**
     * View method
     *
     * @param string|null $id Interaction id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $interaction = $this->Interactions->get($id, [
            'contain' => []
        ]);
        $this->set('interaction', $interaction);
        $this->set('_serialize', ['interaction']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $interaction = $this->Interactions->newEntity();
        if ($this->request->is('post')) {
            $interaction = $this->Interactions->patchEntity($interaction, $this->request->data);
            if ($this->Interactions->save($interaction)) {
                $this->Flash->success(__('The interaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The interaction could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('interaction'));
        $this->set('_serialize', ['interaction']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Interaction id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $interaction = $this->Interactions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $interaction = $this->Interactions->patchEntity($interaction, $this->request->data);
            if ($this->Interactions->save($interaction)) {
                $this->Flash->success(__('The interaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The interaction could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('interaction'));
        $this->set('_serialize', ['interaction']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Interaction id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $interaction = $this->Interactions->get($id);
        if ($this->Interactions->delete($interaction)) {
            $this->Flash->success(__('The interaction has been deleted.'));
        } else {
            $this->Flash->error(__('The interaction could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
