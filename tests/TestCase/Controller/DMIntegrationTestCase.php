<?php

namespace App\Test\TestCase\Controller;

use App\Test\Fixture\UsersFixture;
use Cake\TestSuite\IntegrationTestCase;

require_once 'simple_html_dom.php';

/**
 *
 * In these tests I generally want to test that:
 *
 * 1. A controller method exists...
 *
 * 2. Said method returns the correct response code.
 *
 * 3. Said method does or does not redirect.  If it redirects, then where to?
 *
 * 4. A bare minimum of html structure required to reasonably verify correct operation
 *    and to facilitate TDD.  For example, the add method should return a form with certain fields,
 *    and particular <A> tag should exist.
 *
 * 5. Verify that the db has changed as expected, if applicable.
 *
 * I do not want to test:
 *
 * 1. Whether or not Auth prevents/allows access to a method.
 *
 * 2. How the method responds to badly formed requests, such as trying to submit a DELETE to the add method.
 *
 * 3. Any html structure, formatting, css, scripts, tags, krakens, or whatever, beyond the bare minimum
 *    listed above.
 *
 * 4. Whether or not following an <A> tag actually works as expected.
 *
 * These items should be tested elsewhere.
 *
 * Although tempting to test for viewVars, resist the urge.  If they are not set correctly then
 * there will be actual consequences that the testing will catch.  At best looking for viewVars
 * is a debugging aid.  At worst, we'll eat a lot of time picking them apart.  Just say No.
 */

class DMIntegrationTestCase extends IntegrationTestCase {

    protected $usersFixture;

    // Hack the session to make it look as if we're properly logged in.
    protected function fakeLogin($userId) {

        if($userId==null) return; // anonymous user, not logged in

        // Set session data
        $username = $this->usersFixture->get($userId)['username'];
        $this->session(
            [
                'Auth' => [
                    'User' => [
                        'id' => $userId,
                        'username' => $username
                    ]
                ]
            ]
        );
    }

    // Many forms have a hidden input for various reasons, such as for tunneling various http verbs using POST,
    // or for implementing multi-select lists.
    // Look for the first one of these present.  If found, return true, else fail.
    // simple_html_dom $form - the form that contains the select
    // String $name - the name attribute of the input
    // String $value - the value of the input
    protected function lookForHiddenInput($form, $name='_method', $value='POST') {
        foreach($form->find('input[type=hidden]') as $input) {
            if($input->value == $value && $input->name == $name)
                return true;
        }
        $this->fail();
    }

    // Look for a particular select input and ensure that:
    // The selection is what is expected and that the selection control
    // has the correct quantity of choices.  If the control passes, return true, else fail.
    //
    // In order to do this, we'll need:
    // simple_html_dom $form - the form that contains the select
    // string $selectID - the html id of the select of interest
    // string $vvName - the name of the view var that contains the into to populate the select
    protected function lookForSelect($form, $selectID, $vvName) {
        $option = $form->find('select#'.$selectID.' option[selected]', 0);
        $this->assertNull($option);
        $option_cnt = count($form->find('select#'.$selectID. ' option'));
        $record_cnt = $this->viewVariable($vvName)->count();
        $this->assertEquals($record_cnt + 1, $option_cnt);
        return true;
    }

    public function setUp() {
        parent::setUp();
        $this->usersFixture = new UsersFixture();
    }

    // There are many tests that try to submit an html request to a controller method,
    // where the user is not authorized to access said page. This method will submit the
    // request and assert redirection to the login page.
    protected function tstUnauthorizedRequest($verb, $url) {

        if($verb=='get')
            $this->get($url);
        else if($verb=='post')
            $this->post($url);
        else if($verb=='put')
            $this->put($url);

        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/users/login' );
    }

}