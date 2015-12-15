<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

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

    //
    // This function functions similarly to edit.  That is, GET /interactions/attend
    // will produce an entry form, pre-populated with any existing relevant information,
    // and POST /interactions/attend will submit info to the controller that needs to be
    // written to the db.
    public function attend() {

        $this->request->allowMethod(['get', 'post']);

        // Do we need this for GET and POST?
        if(array_key_exists('clazz_id', $this->request->query)) {


            if ($this->request->is(['post'])) {
                //$interaction = $this->Interactions->patchEntity($interaction, $this->request->data);
                //if ($this->Interactions->save($interaction)) {
                //$this->Flash->success(__('The interaction has been saved.'));
                //return $this->redirect(['action' => 'index']);
                //} else {
                //$this->Flash->error(__('The interaction could not be saved. Please, try again.'));
                //}
            } else {

                // The attendance form is fairly complicated. Listen closely...
                //
                // 1. Given a clazz_id, who are the students? This can be found by tracing through
                // clazzes, sections, cohorts, and thence to students.
                //
                // 2. Left join this to any interactions for this class, with Itype=Attend,
                // so we know who's already marked as present.
                //
                // I spent way too much time futily trying to get this to work using the ORM.
                // Fuck it. Use a direct connection.
                //
                $clazz_id = $this->request->query['clazz_id'];
                $connection = ConnectionManager::get('default');
                $query = "select students.sid, students.giv_name, students.fam_name, cohorts.id, sections.id, clazzes.id
                    from students
                    left join cohorts on students.cohort_id = cohorts.id
                    left join sections on sections.cohort_id = cohorts.id
                    left join clazzes on clazzes.section_id = sections.id
                    left join interactions on interactions.clazz_id=clazzes.id
                    where clazzes.id=".$clazz_id ;

                $studentsResults = $connection->execute($query)->fetchAll('assoc');
            }

        } else {
            // no class_id specified
        }



        $n=count($studentsResults);
        $this->set('studentsResults',$studentsResults);

        $this->set('interactions', $this->Interactions->find());

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
