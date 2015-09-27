<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Herds Controller
 *
 * @property \App\Model\Table\HerdsTable $Herds
 */
class HerdsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Majors']
        ];
        $this->set('herds', $this->paginate($this->Herds));
        $this->set('_serialize', ['herds']);
    }

    /**
     * View method
     *
     * @param string|null $id Herd id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $herd = $this->Herds->get($id, [
            'contain' => ['Majors', 'Sections']
        ]);
        $this->set('herd', $herd);
        $this->set('_serialize', ['herd']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $herd = $this->Herds->newEntity();
        if ($this->request->is('post')) {
            $herd = $this->Herds->patchEntity($herd, $this->request->data);
            if ($this->Herds->save($herd)) {
                $this->Flash->success(__('The herd has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The herd could not be saved. Please, try again.'));
            }
        }
        $majors = $this->Herds->Majors->find('list', ['limit' => 200]);
        $this->set(compact('herd', 'majors'));
        $this->set('_serialize', ['herd']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Herd id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $herd = $this->Herds->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $herd = $this->Herds->patchEntity($herd, $this->request->data);
            if ($this->Herds->save($herd)) {
                $this->Flash->success(__('The herd has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The herd could not be saved. Please, try again.'));
            }
        }
        $majors = $this->Herds->Majors->find('list', ['limit' => 200]);
        $this->set(compact('herd', 'majors'));
        $this->set('_serialize', ['herd']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Herd id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $herd = $this->Herds->get($id);
        if ($this->Herds->delete($herd)) {
            $this->Flash->success(__('The herd has been deleted.'));
        } else {
            $this->Flash->error(__('The herd could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
