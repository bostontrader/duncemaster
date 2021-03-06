<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Semester Entity.
 *
 * @property int $id
 * @property int $year
 * @property int $seq
 */
class Semester extends Entity {

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
        $s1 = $this->_properties['year'];
        $s2 = $this->_properties['seq'];
        return $s1 . '-' . $s2;
    }
}
