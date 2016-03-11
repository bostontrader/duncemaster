<?php
namespace App\Controller;

class TplanElementsController extends AppController {

    // Nothing is authorized unless a controller says so.
    // Admin and teachers are always authorized. It is the responsibility
    // of this controller to restrict access to info for a teacher
    // to only his information and no other teacher.
    public function isAuthorized($userArray) {
        return $this->isAdmin || $this->isTeacher;
    }

    public function add() {

        $this->request->allowMethod(['get', 'post']);

        // Get the tplan and tplan_id.
        $tplan_id=$this->get_tplan_id($this->request->params);
        $tplan=$this->TplanElements->Tplans->get($tplan_id);

        $tplan_element = $this->TplanElements->newEntity(['contain'=>'tplans']);
        if ($this->request->is('post')) {
            $tplan_element = $this->TplanElements->patchEntity($tplan_element, $this->request->data);
            if ($this->TplanElements->save($tplan_element)) {
                //$this->Flash->success(__(self::ACCOUNT_SAVED));
                return $this->redirect(['action' => 'index','tplan_id' => $tplan_id,'_method'=>'GET']);
            } else {
                //$this->Flash->error(__(self::ACCOUNT_NOT_SAVED));
            }
        }
        $this->set(compact('tplan_element','tplan'));
        return null;
    }

    //public function delete($id = null) {
        //$this->request->allowMethod(['post', 'delete']);
        //$tplan_element = $this->TplanElements->get($id);
        //if ($this->TplanElements->delete($tplan_element)) {
            //$this->Flash->success(__('The tplan_element has been deleted.'));
            //} else {
            //$this->Flash->error(__('The tplan_element could not be deleted. Please, try again.'));
        //}
        //return $this->redirect(['action' => 'index']);
    //}

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);

        // Get the tplan and tplan_id.
        $tplan_id=$this->get_tplan_id($this->request->params);
        $tplan=$this->TplanElements->Tplans->get($tplan_id);

        $tplan_element = $this->TplanElements->get($id);
        if ($this->request->is(['put'])) {
            $tplan_element = $this->TplanElements->patchEntity($tplan_element, $this->request->data);
            if ($this->TplanElements->save($tplan_element)) {
                //$this->Flash->success(__(self::ACCOUNT_SAVED));
                return $this->redirect(['action' => 'index','tplan_id' => $tplan_id,'_method'=>'GET']);
            } else {
                //$this->Flash->error(__(self::ACCOUNT_NOT_SAVED));
            }
        }
        $this->set(compact('tplan_element','tplan'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $tplan_id=$this->get_tplan_id($this->request->params);
        $tplan=$this->TplanElements->Tplans->get($tplan_id);

        $this->set(
            'tplan_elements', $this->TplanElements->find()
            //->contain(['Books','Categories'])
            ->where(['tplan_id'=>$tplan_id])
            ->order(['start_thour'])
        );
        $this->set(compact('tplan'));

        //$this->set('tplan_elements', $this->TplanElements->find()->contain(['Tplans']));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $tplan_element = $this->TplanElements->get($id, ['contain' => ['Tplans']]);
        $this->set('tplan_element', $tplan_element);
    }

    // The actions in this controller should only be accessible in the context of a tplan,
    // as passed by appropriate routing.
    private function get_tplan_id($params) {
        if (array_key_exists('tplan_id', $params)) return $params['tplan_id'];
        throw new BadRequestException(self::DNC);
    }
}
