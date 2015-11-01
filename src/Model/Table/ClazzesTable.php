<?php
namespace App\Model\Table;

//use App\Model\Entity\Clazze;
//use Cake\ORM\Query;
//use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
//use Cake\Validation\Validator;

/**
 * Clazzes Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Sections
 */
class ClazzesTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        //$this->table('clazzs');
        //$this->displayField('id');
        //$this->primaryKey('id');

        $this->belongsTo('Sections', [
            //'foreignKey' => 'section_id',
            //'joinType' => 'INNER'
        ]);

        $this->hasMany('Interactions', [
            //'foreignKey' => 'clazz_id'
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
            //->add('datetime', 'valid', ['rule' => 'datetime'])
            //->requirePresence('datetime', 'create')
            //->notEmpty('datetime');

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
        //$rules->add($rules->existsIn(['section_id'], 'Sections'));
        //return $rules;
    //}
}
