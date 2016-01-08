<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;

class ClazzesController extends AppController {

    public function add() {

        $this->request->allowMethod(['get', 'post']);
        $clazz = $this->Clazzes->newEntity();

        if(array_key_exists('section_id', $this->request->query))
            $clazz->section_id=$this->request->query['section_id'];

        if ($this->request->is('post')) {
            $clazz = $this->Clazzes->patchEntity($clazz, $this->request->data);
            if ($this->Clazzes->save($clazz)) {
                //$this->Flash->success(__('The clazz has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The clazz could not be saved. Please, try again.'));
            }
        }
        $sections = $this->Clazzes->Sections->find('list');
        $this->set(compact('clazz','sections'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $clazz = $this->Clazzes->get($id);
        if ($this->Clazzes->delete($clazz)) {
            //$this->Flash->success(__('The clazz has been deleted.'));
            //} else {
            //$this->Flash->error(__('The clazz could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {

        $this->request->allowMethod(['get', 'put']);
        $clazz = $this->Clazzes->get($id);
        if ($this->request->is(['put'])) {
            $clazz = $this->Clazzes->patchEntity($clazz, $this->request->data);
            if ($this->Clazzes->save($clazz)) {
                //$this->Flash->success(__('The clazz has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The clazz could not be saved. Please, try again.'));
            }
        }
        $sections = $this->Clazzes->Sections->find('list');
        $this->set(compact('clazz', 'sections'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $section_id=null;

        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');

        $query = "select clazzes.id, event_datetime, comments, count(if(itype_id=1,1,null)) as attend_cnt, count(if(itype_id=4,1,null)) as participate_cnt
            from clazzes left join interactions on clazzes.id=interactions.clazz_id";

        if(array_key_exists('section_id', $this->request->query)) {
            $section_id=$this->request->query['section_id'];
            $query .= " where section_id=$section_id";
        }
        $query .= " group by clazzes.id";
        $results = $connection->execute($query)->fetchAll('assoc');

        $this->set('clazzes', $results);
        $this->set('section_id', $section_id);
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $clazz = $this->Clazzes->get($id, ['contain'=>'Sections']);
        $this->set('clazz', $clazz);
    }
}
