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
    // This function functions similarly to edit and participate.  That is, GET /interactions/attend
    // will produce an entry form, pre-populated with any existing relevant information,
    // and POST /interactions/attend will submit info to the controller that needs to be
    // written to the db.
    //
    // You might be tempted to combine the attend and participate forms. Resist the urge.
    // Doing so will lead you into a snakepit of needless complexity. Keep 'em separate and simple.
    public function attend() {

        $this->request->allowMethod(['get', 'post']);

        // Must have a clazz_id request parameter
        if(array_key_exists('clazz_id', $this->request->query)) {
            $clazz_id = $this->request->query['clazz_id'];

            if ($this->request->is(['post'])) {

                foreach($this->request->data['attend'] as $student_id=>$attend_value) {

                    // How many existing records, of $itype_id=ATTEND, are there for this student in this class?
                    /* @var \Cake\ORM\Query $query */
                    $query = $this->Interactions->find('all')
                        ->where(['student_id'=>$student_id])
                        ->where(['clazz_id'=>$clazz_id])
                        ->where(['itype_id'=>ItypesController::ATTEND]);

                    // There are no existing ATTEND records for this student,
                    // in this class.
                    switch($query->count()) {
                        case 0:
                            // If there is any value from the form, create a new ATTEND record
                            if($attend_value==1) {
                                $newInteraction = $this->Interactions->newEntity();
                                $newInteraction = $this->Interactions->patchEntity($newInteraction, [
                                    'student_id'=>$student_id,'clazz_id'=>$clazz_id,'itype_id'=>ItypesController::ATTEND
                                ]);
                                $this->Interactions->save($newInteraction);
                            } // else do nothing
                            break;

                        // There is a single existing record.
                        case 1:

                            $interaction=$query->first();
                            if($attend_value==1) {
                                // The mere existence of this record indicates attendance
                                // Don't worry... be happy
                            } else {
                                $this->Interactions->delete($interaction);
                            }

                        // There should only be zero or one records.
                        default:
                            // Max fubar error. Jettison the warp core and run!
                    }
                } // for each request data

                // If a section_id is passed, then redirect back to the clazzes for that section
                if(array_key_exists('section_id', $this->request->query)) {
                    $section_id = $this->request->query['section_id'];
                    return $this->redirect(['controller' => 'clazzes', 'action' => 'index', 'section_id' => $section_id ]);
                }

            } // if reqest is post

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
                " order by sort";

            $attendResults = $connection->execute($query)->fetchAll('assoc');
        } else {
            // no class_id specified
            $attendResults=[];
        }

        $this->set('attendResults',$attendResults);
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

    //
    // This function functions similarly to edit and attend.  That is, GET /interactions/participate
    // will produce an entry form, pre-populated with any existing relevant information,
    // and POST /interactions/participate will submit info to the controller that needs to be
    // written to the db.
    //
    // You might be tempted to combine the attend and participate forms. Resist the urge.
    // Doing so will lead you into a snakepit of needless complexity. Keep 'em separate and simple.
    public function participate() {

        $this->request->allowMethod(['get', 'post']);

        // Must have a clazz_id request parameter
        if(array_key_exists('clazz_id', $this->request->query)) {
            $clazz_id = $this->request->query['clazz_id'];

            if ($this->request->is(['post'])) {

                foreach($this->request->data['participate'] as $student_id=>$participate_value) {

                    // How many existing records, of $itype_id=PARTICIPATE, are there for this student in this class?
                    /* @var \Cake\ORM\Query $query */
                    $query = $this->Interactions->find('all')
                        ->where(['student_id'=>$student_id])
                        ->where(['clazz_id'=>$clazz_id])
                        ->where(['itype_id'=>ItypesController::PARTICIPATE]);

                    switch($query->count()) {
                        // There are no existing PARTICIPATE records for this student,
                        // in this class.
                        case 0:
                            // If there is any value from the form, create a new PARTICIPATE record
                            if($participate_value!='') {
                                $newInteraction = $this->Interactions->newEntity();
                                $newInteraction = $this->Interactions->patchEntity($newInteraction, [
                                    'student_id'=>$student_id,'clazz_id'=>$clazz_id,'itype_id'=>ItypesController::PARTICIPATE,'participate'=>$participate_value
                                ]);
                                $this->Interactions->save($newInteraction);
                            } // else do nothing
                            break;

                        // There is a single existing record.
                        case 1:
                            // If there is no value from the form then delete the record.
                            // Else update the record with the new value.
                            $interaction=$query->first();

                            if($participate_value=='') {
                                $this->Interactions->delete($interaction);
                            } else {
                                $interaction = $this->Interactions->patchEntity($interaction, [
                                    'participate'=>$participate_value
                                ]);
                                $this->Interactions->save($interaction);
                            }
                            break;

                        // There should only be zero or one records.
                        default:
                            // Max fubar error. Jettison the warp core and run!
                    }
                } // foreach request data

                // If a section_id is passed, then redirect back to the clazzes for that section
                if(array_key_exists('section_id', $this->request->query)) {
                    $section_id = $this->request->query['section_id'];
                    return $this->redirect(['controller' => 'clazzes', 'action' => 'index', 'section_id' => $section_id ]);
                }

            } // if request is post

            // The participation form is fairly complicated. Listen closely...
            //
            // 1. Given a clazz_id, who are the students? This can be found by tracing through
            // clazzes, sections, cohorts, and thence to students.
            //
            // 2. Left join this to any interactions for this class, with Itype=Participate,
            //
            // I spent way too much time futily trying to get this to work using the ORM.
            // Fuck it. Use a direct connection.
            //
            /* @var \Cake\Database\Connection $connection */
            $connection = ConnectionManager::get('default');
            $query = "select students.id as student_id, students.sort, students.sid, students.giv_name, students.fam_name, students.phonetic_name, interactions.itype_id, interactions.participate, cohorts.id as cohort_id, sections.id as section_id, clazzes.id as clazz_id
                from students
                left join cohorts on students.cohort_id = cohorts.id
                left join sections on sections.cohort_id = cohorts.id
                left join clazzes on clazzes.section_id = sections.id
                left join interactions on interactions.clazz_id=clazzes.id and interactions.student_id=students.id and interactions.itype_id=".ItypesController::PARTICIPATE."  where clazzes.id=".$clazz_id.
                " order by sort";

            $participationResults = $connection->execute($query)->fetchAll('assoc');
        } else {
            // no class_id specified
            $participationResults=[];
        }

        $this->set('participationResults',$participationResults);
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $interaction = $this->Interactions->get($id, ['contain' => ['Clazzes','Itypes','Students']]);
        $this->set('interaction', $interaction);
    }
}
