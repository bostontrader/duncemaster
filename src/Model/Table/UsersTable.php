<?php
namespace App\Model\Table;

//use App\Model\Entity\User;
//use Cake\ORM\Query;
//use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
//use Cake\Validation\Validator;

class UsersTable extends Table {

    public function initialize(array $config) {
        parent::initialize($config);

        $this->belongsToMany('Roles',
            [
                //'targetForeignKey' => 'role_id',
                //'foreignKey' => 'user_id',
                //'joinTable' => 'roles_users',

                'through' => 'RolesUsers',
                'alias' => 'Roles',
                'foreignKey' => 'user_id',
                'joinTable' => 'roles_users',
                'targetForeignKey' => 'role_id'
            ]);
        //$this->table('users');
        //$this->displayField('id');
        //$this->primaryKey('id');

        //$this->addBehavior('Timestamp');

    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    //public function validationDefault(Validator $validator) {


        //$validator
            //->notEmpty('username', 'A username is required')
            //->notEmpty('password', 'A password is required')
            //->notEmpty('role', 'A role is required')
            //->add('role', 'inList', [
                //'rule' => ['inList', ['admin', 'author']],
                //'message' => 'Please enter a valid role'
            //]);

        //return $validator;
    //}

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    //public function buildRules(RulesChecker $rules) {
        //$rules->add($rules->isUnique(['email']));
        //return $rules;
    //}

}
