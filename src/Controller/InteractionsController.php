<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;

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

        // Must have a clazz_id request parameter
        if(array_key_exists('clazz_id', $this->request->query)) {
            $clazz_id = $this->request->query['clazz_id'];

            if ($this->request->is(['post'])) {


                // We will simultaneously iterate over request->data['attend'] and request->data['participate']
                // Make sure that these two arrays have the same quantity of elements and have
                // the same keys.
                // Each step corresponds to attendance and participation for a particular student
                // in the relevant class.
                // Although tempting to try this using MultipleIterator, that does not work.
                // We apparently cannot get the key values!
                foreach($this->request->data['attend'] as $student_id=>$attend_value) {

                    $participate_value=$this->request->data['participate'][$student_id];

                    // Make sure that the Interactions table jives with the attendance value from the form
                    $this->makeInteractionsJiveWithForm(ItypesController::ATTEND, $attend_value, $student_id, $clazz_id);

                    // Do the same with the participation values
                    $this->makeInteractionsJiveWithForm(ItypesController::PARTICIPATE, $participate_value, $student_id, $clazz_id);
                }

            }

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
            /* @var \Cake\Database\Connection $connection */
            $connection = ConnectionManager::get('default');
            $query = "select students.id as student_id, students.sort, students.sid, students.giv_name, students.fam_name, students.phonetic_name, interactions.itype_id, cohorts.id as cohort_id, sections.id as section_id, clazzes.id as clazz_id
                from students
                left join cohorts on students.cohort_id = cohorts.id
                left join sections on sections.cohort_id = cohorts.id
                left join clazzes on clazzes.section_id = sections.id
                left join interactions on interactions.clazz_id=clazzes.id and interactions.student_id=students.id and interactions.itype_id=".ItypesController::ATTEND." where clazzes.id=".$clazz_id.
                " order by sort"
            ;

            $studentsResults = $connection->execute($query)->fetchAll('assoc');
        } else {
            // no class_id specified
            $studentsResults=[];
        }

        $this->set('studentsResults',$studentsResults);
    }

    private function makeInteractionsJiveWithForm($itype_id, $form_value, $student_id, $clazz_id) {
        // How many existing records, of $itype_id, are there for this student in this class?
        /* @var \Cake\ORM\Query $query */
        $query = $this->Interactions->find('all')
            ->where(['student_id'=>$student_id])
            ->where(['clazz_id'=>$clazz_id])
            ->where(['itype_id'=>$itype_id]);

        switch($query->count()) {
            case 0:
                // There are no existing records for this student
                // in this class.
                // If there is a form_value then create a new record
                if($form_value==1) {
                    $newInteraction = $this->Interactions->newEntity();
                    $newInteraction = $this->Interactions->patchEntity($newInteraction, [
                        'student_id'=>$student_id,'clazz_id'=>$clazz_id,'itype_id'=>$itype_id
                    ]);

                    $this->Interactions->save($newInteraction);
                } // else do nothing
                break;
            case 1:
                // There is an existing record.
                // If form_value is the same, then we're happy, do nothing
                // else if form_value is different then change or delete the record
                if($form_value==1) {
                    // Don't worry... be happy
                } else {
                    $interaction=$query->first();
                    $this->Interactions->delete($interaction);
                }
                break;
            default:
                // Max fubar error. Jettison the warp core and run!
        }
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
        $this->request->allowMethod(['get', 'put']);
        $interaction = $this->Interactions->get($id);
        if ($this->request->is(['put'])) {
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
