<?php
namespace App\Model\Table;

//use App\Model\Entity\Major;
//use Cake\ORM\Query;
//use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
//use Cake\Validation\Validator;

class MajorsTable extends Table {

    public function initialize(array $config) {
        parent::initialize($config);

        //$this->table('majors');
        $this->displayField('title');
        //$this->primaryKey('id');

        $this->hasMany('Cohorts', [
            //'foreignKey' => 'major_id'
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
            //->add('id', 'valid', ['rule' => 'numeric'])
            //->allowEmpty('id', 'create');

        //$validator
            //->requirePresence('title', 'create')
            //->notEmpty('desc');

        //$validator
            //->requirePresence('sdesc', 'create')
            //->notEmpty('sdesc');

        //return $validator;
    //}
}
