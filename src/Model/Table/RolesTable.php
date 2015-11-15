<?php
namespace App\Model\Table;

//use App\Model\Entity\Role;
//use Cake\ORM\Query;
//use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
//use Cake\Validation\Validator;

/**
 * Roles Model
 *
 */
class RolesTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);
        $this->belongsToMany('Users',
            [
                //'targetForeignKey' => 'user_id',
                //'foreignKey' => 'role_id',
                //'joinTable' => 'roles_users',

                'through' => 'RolesUsers',
                'alias' => 'Users',
                'foreignKey' => 'role_id',
                'joinTable' => 'roles_users',
                'targetForeignKey' => 'user_id'

            ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    //public function validationDefault(Validator $validator) {


        //$validator
            //->notEmpty('rolename', 'A rolename is required')
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
