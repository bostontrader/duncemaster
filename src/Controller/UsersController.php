<?php
namespace App\Controller;

use Cake\Event\Event;

class UsersController extends AppController {

    public function isAuthorized($user) {
        $action = $this->request->params['action'];
        // The login action is always allowed.
        if ($action == 'login') {
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
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('Invalid username or password, try again'));
        }
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
