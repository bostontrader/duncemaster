<?php
namespace App\Model\Table;

//use App\Model\Entity\Section;
//use Cake\ORM\Query;
//use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
//use Cake\Validation\Validator;

/**
 * Sections Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Cohorts
 * @property \Cake\ORM\Association\BelongsTo $Subjects
 */
class SectionsTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        //$this->table('sections');
        //$this->displayField('id');
        //$this->primaryKey('id');

        $this->hasMany('Clazzs', [
            //'foreignKey' => 'clazz_id'
        ]);

        $this->belongsTo('Cohorts', [
            //'foreignKey' => 'cohort_id',
            //'joinType' => 'INNER'
        ]);

        $this->belongsTo('Semesters', [
            //'foreignKey' => 'semester_id',
            //'joinType' => 'INNER'
        ]);

        $this->belongsTo('Subjects', [
            //'foreignKey' => 'subject_id',
            //'joinType' => 'INNER'
        ]);

        //$this->belongsTo('Teachers', [
            //'foreignKey' => 'teacher_id',
            //'joinType' => 'INNER'
        //]);
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
            //->add('weekday', 'valid', ['rule' => 'numeric'])
            //->requirePresence('weekday', 'create')
            //->notEmpty('weekday');

        //$validator
            //->add('time', 'valid', ['rule' => 'time'])
            //->requirePresence('time', 'create')
            //->notEmpty('time');

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
        //$rules->add($rules->existsIn(['cohort_id'], 'Cohorts'));
        //$rules->add($rules->existsIn(['subject_id'], 'Subjects'));
        //return $rules;
    //}
}
