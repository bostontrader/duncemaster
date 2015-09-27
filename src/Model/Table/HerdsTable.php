<?php
namespace App\Model\Table;

use App\Model\Entity\Herd;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Herds Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Majors
 * @property \Cake\ORM\Association\HasMany $Sections
 */
class HerdsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('herds');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Majors', [
            'foreignKey' => 'major_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Sections', [
            'foreignKey' => 'herd_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->add('start_year', 'valid', ['rule' => 'numeric'])
            ->requirePresence('start_year', 'create')
            ->notEmpty('start_year');

        $validator
            ->add('seq', 'valid', ['rule' => 'numeric'])
            ->requirePresence('seq', 'create')
            ->notEmpty('seq');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['major_id'], 'Majors'));
        return $rules;
    }
}
