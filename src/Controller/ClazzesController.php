<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;

class ClazzesController extends AppController {

    // Nothing is authorized unless a controller says so.
    // Admin and teachers are always authorized. It is the responsibility
    // of a particular action to restrict access to info for a teacher
    // to only his information and no other teacher.
    public function isAuthorized($userArray) {

        $users = TableRegistry::get('Users');
        $user=$users->get($userArray['id'], ['contain' => ['Roles']]);
        $this->isAdmin = false;
        $this->isTeacher = false;
        foreach($user->roles as $role) {
            if($role->title=='admin') $this->isAdmin=true;
            if($role->title=='teacher') $this->isTeacher=true;
        }
        return $this->isAdmin || $this->isTeacher;
    }

    /**
     * GET /clazzes/add?section_id=n
     *
     * Returns a form for the creation of a new clazz. Said form includes a select-list of
     * sections with section_id pre-selected.
     *
     * The section_id parameter is mandatory because...
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
     * Security:
     * Only authenticated users with role=admin or role=teacher can see this form.
     *
     * We don't want to leak _any_ information to users who are not properly authorized. This
     * form's select list contains a list of sections for a particular teacher.  Admins can
     * see this and so can the specific teacher. But nobody else. Hence, the following error
     * conditions and responses:
     *
     * 1. If no section_id is specified, throw a BadRequestException (400).
     * 2. If the authenticated user is not an admin, but _is_ a teacher, and said teacher is not the
     *    teacher associated with the section id, throw a BadRequestException (400).
     *
     * These errors should only occur if somebody is directly fiddling with the URL, so fuck 'em, they
     * don't need any user-friendly error reporting.
     *
     * That said, the rules:
     * 1. The form will include a select list of candidate sections and by default no option will be selected.
     *    If (the section_id param matches an available choice) then
     *      the value of that param will be used by the form to set the initial selection in the select list.
     * 2. The avail choices for the select list are:
     *    all sections from the same semester, with the same teacher, as the section specified by the request,
     *
     */
    public function add() {

        $this->request->allowMethod(['get', 'post']);

        $clazz = $this->Clazzes->newEntity();

        // 2. POST or GET?
        if ($this->request->is('post')) {

            $clazz = $this->Clazzes->patchEntity($clazz, $this->request->data);
            if ($this->Clazzes->save($clazz)) {
                //$this->Flash->success(__('The clazz has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The clazz could not be saved. Please, try again.'));
            }
        } else { // assume GET

            // 1. Must have a section_id request parameter
            if(array_key_exists('section_id', $this->request->query)) {
                $section_id = $this->request->query['section_id'];
                $clazz->section_id = $section_id;
            } else {
                throw new BadRequestException("You need to include a 'section_id' parameter");
            }

            // 2.1 Retrieve the single section record from $section_id. We might want to use ->get()
            // but that throws an exception that I cannot seem to catch. So do it this way.
            // This is very similar to code in .edit. Can we factor this out?
            $query=$this->Clazzes->Sections
                ->find()
                ->where(['id'=>$section_id]);

            switch($query->count()) {
                case 0:
                    // This means that the given section doesn't exist. Does it matter if we leak that info?
                    // For now, assume it doesn't matter. But this should only happen if the user is fiddling
                    // with the URL, so no need for any user-friendly error messages.
                    throw new BadRequestException("That does not compute");
                    break;
                case 1:
                    // We found this section. Now can we get all sections with the same teacher_id and semester_id?
                    $section=$query->first();
                    $teacher_id=$this->Auth->user('teacher_id');
                    $semester_id=$section['semester_id'];
                    if ($section['teacher_id']==$this->Auth->user('teacher_id') || $this->isAdmin) {
                        $sections = $this->Clazzes->Sections
                            ->find('list')
                            ->where(['teacher_id'=>$section['teacher_id'],'semester_id'=>$section['semester_id']]);
                        $c=$sections->count();
                    } else {
                        throw new BadRequestException("You're not the teacher implied by the specified section_id, assuming this section even exists");
                    }
                    $this->set(compact('clazz', 'sections'));
                    return null;
                    break;
                default:
                    // max fubar error. How could the above query _ever_ not be 0 or 1?
            }
        }
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

        // Retrieve the single section record associated with this clazz. We might want to use ->get()
        // but that throws an exception that I cannot seem to catch. So do it this way.
        // This is very similar to code in .add. Can we factor this out?
        $query=$this->Clazzes->Sections
            ->find()
            ->where(['id'=>$clazz['section_id']]);

        switch($query->count()) {
            case 0:
                // This means that the given section doesn't exist. This should
                // never happen.
                throw new BadRequestException("That does not compute");
                break;
            case 1:
                $section=$query->first();
                $teacher_id=$this->Auth->user('teacher_id');
                $semester_id=$section['semester_id'];
                if ($section['teacher_id']==$this->Auth->user('teacher_id') || $this->isAdmin) {
                    $sections = $this->Clazzes->Sections
                        ->find('list')
                        ->where(['teacher_id'=>$section['teacher_id'],'semester_id'=>$section['semester_id']]);
                    $c=$sections->count();
                } else {
                    throw new BadRequestException("You're not the teacher implied by the specified section_id, assuming this section even exists");
                }
                $this->set(compact('clazz', 'sections'));
                return null;
                break;
            default:
                // max fubar error. How could the above query _ever_ not be 0 or 1?
        }


        //$this->set(compact('clazz', 'sections'));
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
        $query .= " order by event_datetime";
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
