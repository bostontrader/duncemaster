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
                $user = $this->findAssociatedStudentOrTeacher($user, 'teacher');

                // Is the user a student?
                $user = $this->findAssociatedStudentOrTeacher($user, 'student');

                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            //$this->Flash->error(__('Invalid username or password, try again'));
        }
        return null;
    }

    private function findAssociatedStudentOrTeacher($user, $type) {

        /* @var \Cake\ORM\Query $query */
        $query=$this->Users->find('all');
        $query->select(['Users.id','Users.username','roles.title'])
            ->leftJoin('roles_users','roles_users.user_id=Users.id')
            ->leftJoin('roles'      ,'roles_users.role_id=roles.id')
            ->where(['roles.title'=>$type,'Users.id'=>$user['id']]);

        // Is there a $type connected to this user?
        switch($query->count()) {
            case 0:
                // It's cool. Do nothing. This user does not have a role of $type.
                break;
            case 1:
                // Ding! We got a winner! This user has role of $type.
                $user[$type.'_id']=null;

                // But... even though the user has a role of $type...
                // Are any $type assigned this user?
                $query->select([$type.'s.id']);
                $query->leftJoin($type.'s',$type.'s.user_id=Users.id');
                //$n=$query->execute()->fetchAll('assoc');
                //$c=$query->count();
                switch($query->count()) {
                    case 0:
                        // It's ok, do nothing more. This user has a role of teacher or student,
                        // but does not have a teacher or student associated with it.
                        break;
                    case 1:
                        // Ding! Another winner. This user has a role of $type _and_
                        // there is an associated $type as well.
                        $n=$query->execute()->fetchAll('assoc');
                        $user[$type.'_id']=$n[0][$type.'s__id'];
                        break;
                    default:
                        // A single user id should not have more than 1 $type pointing to it.
                        //$this->assertFail('MaxFubar Error');
                }
                break;
            default:
                // A single user id should not have more than 1 $type role associated with it.
                //$this->assertFail('MaxFubar Error');
        }

        return $user;
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
