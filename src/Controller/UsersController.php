<?php
namespace App\Controller;

use Cake\Event\Event;

class UsersController extends AppController {

    public function isAuthorized($user) {

        if(parent::isAuthorized($user)) return true;

        $action = $this->request->params['action'];
        // The login action is always allowed.
        if ($action == 'login') {
            return true;
        }

        // The logout action is always allowed.
        if ($action == 'logout') {
            return true;
        }

        return false;
    }

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                //$this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $roles = $this->Users->Roles->find('list');
        $this->set(compact('user', 'roles'));
        return null;
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow([]);
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            //$this->Flash->success(__('The user has been deleted.'));
            //} else {
            //$this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {

        $this->request->allowMethod(['get', 'put']);
        $user = $this->Users->find()->where(['id'=>$id])->contain('Roles')->first();

        if ($this->request->is(['put'])) {
            $this->Users->patchEntity($user, $this->request->data(), ['associated'=>['Roles']]);
            if ($result = $this->Users->save($user, ['associated'=>['Roles']])) {
                //$this->Flash->success(__('The user has been updated.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('Unable to update the user.'));
            }
        }

        $roles = $this->Users->Roles->find('list');
        $this->set(compact('user', 'roles'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('users', $this->Users->find());
    }

    public function login() {
        $this->request->allowMethod(['get','post']);
        $this->viewBuilder()->layout('login');
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();

            if ($user) {
                // Is the user a teacher?
                //$user['teacher_id']=$teacher_id;
                //$query="SELECT users.id, users.username, roles.title from users
                    //left join roles_users on roles_users.user_id=users.id
                    //left join roles       on roles_users.role_id=roles.id
                    //where roles.title='teacher' and users.username='TommyTeacher'";
                /* @var \Cake\ORM\Query $query */
                $query=$this->Users->find('all');
                    //->leftJoin('','semesters.id = sections.semester_id');
                $query->select(['users.id','users.username','roles.title'])
                    ->leftJoin('roles_users','roles_users.user_id=users.id')
                    ->leftJoin('roles'      ,'roles_users.role_id=roles.id')
                    ->where(['roles.title'=>'teacher','users.id'=>$user['id']]);
                $n=$query->execute()->fetchAll('assoc');
                $c=$query->count();


                // Is there a teacher connected to this user?
                switch($c) {
                    case 0:
                        // It's cool. Do nothing. This user does not have a role of teacher.
                        break;
                    case 1:
                        // Ding! We got a winner! This user has role of teacher.
                        $user['teacher']=null;
                        // But... even though the user has a role of teacher...
                        // Are any teachers assigned this user?
                        $query->select(['teachers.id as teacher_id']);
                        $query->leftJoin('teachers'      ,'teachers.user_id=users.id');
                        $n=$query->execute()->fetchAll('assoc');
                        $c=$query->count();
                        switch($c) {
                            case 0:
                                // It's ok, do nothing more. This user has a role of teacher,
                                // but does not have a teacher associated with it.
                                break;
                            case 1:
                                // Ding! Another winner. This user has a role of teacher _and_
                                // there is an associated teacher.
                                $user['teacher']=$n[0]['teacher_id'];
                                break;
                            default:
                                // A single user id should not have more than 1 teacher pointing to it.
                                $this->assertFail('MaxFubar Error');
                        }
                        break;
                    default:
                        // A single user id should not have more than 1 teacher role associated with it.
                        $this->assertFail('MaxFubar Error');
                }

                // Is the user a student?
                //$user['student_id']=student_id;
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            //$this->Flash->error(__('Invalid username or password, try again'));
        }
        return null;
    }

    public function logout() {
        $this->request->allowMethod(['post']);
        return $this->redirect($this->Auth->logout());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $user = $this->Users->get($id);
        $this->set('user', $user);
    }

}
