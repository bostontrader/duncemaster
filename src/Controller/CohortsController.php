<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Cohorts Controller
 *
 * @property \App\Model\Table\CohortsTable $Cohorts
 */
class CohortsController extends AppController
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

        $this->set('cohorts', $this->paginate($this->Cohorts));
        $this->set('_serialize', ['cohorts']);
    }

    /**
     * View method
     *
     * @param string|null $id Cohort id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cohort = $this->Cohorts->get($id, [
            'contain' => ['Majors', 'Sections', 'Students']
        ]);
        $this->set('cohort', $cohort);
        //$this->set('_serialize', ['cohort']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cohort = $this->Cohorts->newEntity();
        if ($this->request->is('post')) {
            $cohort = $this->Cohorts->patchEntity($cohort, $this->request->data);
            if ($this->Cohorts->save($cohort)) {
                $this->Flash->success(__('The cohort has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The cohort could not be saved. Please, try again.'));
            }
        }
        //$majors = $this->Cohorts->Majors->find('list', ['limit' => 200]);
        $majors = ['1'=>'Tourist English','2'=>'Aviation','3'=>'Hotel'];
        $this->set(compact('cohort', 'majors'));
        $this->set('_serialize', ['cohort']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Cohort id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cohort = $this->Cohorts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cohort = $this->Cohorts->patchEntity($cohort, $this->request->data);
            if ($this->Cohorts->save($cohort)) {
                $this->Flash->success(__('The cohort has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The cohort could not be saved. Please, try again.'));
            }
        }
        //$majors = $this->Cohorts->Majors->find('list', ['limit' => 200,'fields'=>['id','title']]);
        //$query = $this->Cohorts->Majors->find();
        //$majors = $this->Cohorts->Majors->findList($query,['fields'=>['id','title']]);
        $majors = ['1'=>'Tourist English','2'=>'Aviation','3'=>'Hotel'];
        $this->set(compact('cohort', 'majors'));
        $this->set('_serialize', ['cohort']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Cohort id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cohort = $this->Cohorts->get($id);
        if ($this->Cohorts->delete($cohort)) {
            $this->Flash->success(__('The cohort has been deleted.'));
        } else {
            $this->Flash->error(__('The cohort could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
