<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Clazz Entity.
 *
 * @property int $id
 * @property int $section_id
 * @property \App\Model\Entity\Section $section
 * @property \Cake\I18n\Time $datetime
 */
class Clazz extends Entity {

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
        $s1 = $this->_properties['id'];
        return $s1;
    }

    // Compute 1-based "week" of a given semester that this class occurs in.
    // Yes, yes, I know... don't hardwire the semester starting date here.  I'll move this soon.
    protected function _getWeek() {
        $d1 = new \DateTime("2015-09-07"); // first day of the semester
        $d2 = new \DateTime($this->_properties['event_datetime']);
        $s1 = $d1->diff($d2)->days;
        $s2 = $s1 / 7;
        $s3 = round($s2)+1;
        return $s3;
    }

}
