<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;

class ClazzesController extends AppController {

    // Nothing is authorized unless a controller says so.
    // Admin is always authorized.
    public function isAuthorized($userArray) {
        //$users = TableRegistry::get('Users');
        //$user=$users->get($userArray['id'], ['contain' => ['Roles']]);
        //foreach($user->roles as $role) {
            //if($role->title=='admin') return true;
        //}
        return true;
    }

    /**
     * GET /clazzes/add?section_id=n
     * Returns the new clazz form.
     *
     * The section_id parameter is mandatory. Because...
     * A clazz needs an associated section. The entry form can easily enough present a select-list
     * of section choices. But if the request _does not_ specify a section then which sections should
     * appear in the select list? Including all of them will involve a long and cumbersome list
     * because it's filled with sections from semesters past. But pruning that list is a remarkably
     * slippery and needlessly tedious issue best left as an exercise for the reader.
     *
     * As a practical matter, this is a non-issue. The creation of a new clazz should be in the
     * context of a section (and its teacher and semester) that has already been determined.
     * We'll still use a select-list, but now we can populate it with only those sections from
     * the same semester, with the same teacher, as the section specified by the request.
     *
     * 1. The new class form can only be seen by an admin or the teacher of the specified section.
     *    We don't want to leak _any_ information to users who are not properly authorized. This
     *    form will contain a list of sections, so redirect to /clazzes/index if not properly authorized.
     * 2. A class must have an associated section.
     * 3. The form will present a select list of candidate sections and by default no option will be selected.
     *    If (the section_id param matches an available choice) then
     *      the value of that param will be used by the form to set the initial selection in the select list.
     * 4. The avail choices for the select list are:
     *    all sections from the same semester, with the same teacher, as the section specified by the request.
     */
    public function add() {

        $this->request->allowMethod(['get', 'post']);
        $clazz = $this->Clazzes->newEntity();

        if ($this->request->is('post')) {
            $clazz = $this->Clazzes->patchEntity($clazz, $this->request->data);
            if ($this->Clazzes->save($clazz)) {
                //$this->Flash->success(__('The clazz has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The clazz could not be saved. Please, try again.'));
            }
        }

        // Must have a section_id request parameter
        $n=(array_key_exists('section_id', $this->request->query));
        if(array_key_exists('section_id', $this->request->query)) {
            $clazz->section_id = $this->request->query['section_id'];
        } else {
            return $this->redirect(['action' => 'index']);
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
