<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Student Entity.
 *
 * @property int $id
 * @property string $sid
 * @property string $fam_name
 * @property string $giv_name
 */
class Student extends Entity {

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

    protected function _getFullname() {
        $s1 = $this->_properties['fam_name'];
        $s2 = $this->_properties['giv_name'];
        return $s1 . $s2;
    }
}
