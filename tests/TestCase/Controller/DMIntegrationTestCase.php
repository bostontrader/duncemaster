<?php

namespace App\Test\TestCase\Controller;

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
 *    and to facilitate TDD.  For example, the add method should return a form with certain fields.
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
 * These items should be tested elsewhere.
 *
 * Although tempting to test for viewVars, resist the urge.  If they are not set correctly then
 * there will be actual consequences that the testing will catch.  At best looking for viewVars
 * is a debugging aid.  At worst, we'll eat a lot of time picking them apart.  Just say No.
 */

class DMIntegrationTestCase extends IntegrationTestCase {

    // Hack the session to make it look as if we're properly logged in.
    protected function fakeLogin() {
        // Set session data
        $this->session(
            [
                'Auth' => [
                    'User' => [
                        'id' => 1,
                        'username' => 'testing',
                    ]
                ]
            ]
        );
    }

    // Many forms have a hidden input for tunneling various http verbs using POST.
    // Look for the first one.  If found, return true, else fail.
    //
    protected function lookForHiddenPOST($form) {
        $input = $form->find('input[type=hidden]', 0);
        if ($input == NULL) {
            $this->fail();
        } else {
            $this->assertEquals($input->value, 'POST');
            $this->assertEquals($input->name, '_method');
            return true;
        }
    }

    // Look for a particular select input and ensure that:
    // The selection is what is expected and that the selection control
    // has the correct quantity of choices.  If the control passes, return true, else fail.
    protected function lookForSelect($form, $selectID, $vvName) {
        $option = $form->find('select#'.$selectID.' option[selected]', 0);
        $this->assertNull($option);
        $option_cnt = count($form->find('select#'.$selectID. ' option'));
        $record_cnt = $this->viewVariable($vvName)->count();
        $this->assertEquals($record_cnt + 1, $option_cnt);
        return true;
    }
}