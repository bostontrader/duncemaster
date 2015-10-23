<?php
namespace App\Model\Table;

//use App\Model\Entity\Semester;
//use Cake\ORM\Query;
//use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
//use Cake\Validation\Validator;

/**
 * Semesters Model
 *
 */
class SemestersTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        //$this->table('semesters');
        $this->displayField('nickname');
        //$this->primaryKey('id');
        $this->hasMany('Sections', [
            'foreignKey' => 'section_id'
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
            //->add('year', 'valid', ['rule' => 'numeric'])
            //->requirePresence('year', 'create')
            //->notEmpty('year');

        //$validator
            //->add('seq', 'valid', ['rule' => 'numeric'])
            //->requirePresence('seq', 'create')
            //->notEmpty('seq');

        //return $validator;
    //}
}
