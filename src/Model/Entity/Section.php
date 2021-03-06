<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Section Entity.
 *
 * @property int $id
 * @property int $cohort_id
 * @property \App\Model\Entity\Cohort $cohort
 * @property int $subject_id
 * @property \App\Model\Entity\Subject $subject
 * @property int $weekday
 * @property \Cake\I18n\Time $time
 */
class Section extends Entity {

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    protected function _getNickname() {
        $s1 = $this->_properties['weekday'];
        $s2 = $this->_properties['start_time'];
        return $s1 . '-' . $s2;
    }}
