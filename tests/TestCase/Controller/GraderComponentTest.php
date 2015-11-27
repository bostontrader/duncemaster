<?php

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\GraderComponent;
//use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;

class GraderComponentTest extends TestCase {

    public $fixtures = [
        'app.clazzes',
        'app.interactions',
        'app.sections',
        'app.students',
    ];

    public function setUp() {
        //parent::setUp();
        // Setup our component and fake test controller
        $request = new Request();
        $response = new Response();
        $this->controller = $this->getMock(
            'Cake\Controller\Controller',
            null,
            [$request, $response]
        );
        $registry = new ComponentRegistry($this->controller);
        $this->component = new GraderComponent($registry);
    }

    public function testAdjust() {
        $this->component->getGradeInfo();
    }

    public function tearDown() {
        //parent::tearDown();
        // Clean up after we're done
        //unset($this->component, $this->controller);
    }

}