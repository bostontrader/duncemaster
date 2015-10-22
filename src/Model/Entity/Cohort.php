<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cohort Entity.
 *
 * @property int $id
 * @property int $start_year
 * @property int $major_id
 * @property \App\Model\Entity\Major $major
 * @property int $seq
 * @property \App\Model\Entity\Section[] $sections
 */
class Cohort extends Entity {

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
        $s1 = substr($this->_properties['start_year'],-2);
        $s2 = $this->major->_properties['sdesc'];
        $s3 = $this->_properties['seq'];
        return $s1 . $s2 . $s3;
        //return substr($this->_properties['start_year'],-2) . $this->major->sdesc . $this->_properties['seq'];
    }
}
