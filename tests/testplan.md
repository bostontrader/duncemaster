#Test Plan

Execute phpunit.  Feed it whatever args you want...
cd $STACK_ROOT/html/duncemaster
$STACK_ROOT/php/bin/php vendor/bin/phpunit tests/TestCase/Controller

##Fixture Data
Ordinary fixtures are hoplessly clumsy to use.  Any realistic app can be expected to have
many tables with their records intricately related.  Assembling a set of fixtures that provides
sufficient variation for testing is not easy and can be very fragile and difficult to debug.

I attempted to use a method I configure the app to use a db source named 'fixture' and then
use the app to create the myriad of variations required for testing.  I then modified the init
method of each fixture class to populate the fixture's records using the 'fixture' source,
instead of hardwired values in the Class.  This didn't work out as well as I had hoped.  So 
I'm back to the original method.

The basic problem with either method is that the blizzard of related records becomes too complicated
to properly manage.  How to solve this puzzle?





I have a db duncemaster-fixture.  I can configure the app to use duncemaster-fixture for
ordinary operation.  The test fixtures have been setup to copy the data from duncemaster-fixture
into duncemaster-test. This has proven to be reasonable method to provide good example data.
I can use the ordinary app to manipulate the various records and their relationships,
and then set appropriate constants in tests/Fixture/FixtureConstants to point to
the various records. I can then refer to said records by using these constants.
But be careful to ensure that the constants stay in sync with the duncemaster-fixture.

##Categories of Testing
There are several basic categories of testing that I'm interested in:

* Ordinary correct operation, such as CRUD operations.

	I will call every URL I can enumerate. I want to verify basic screen content.

* What happens with bad inputs ?  SQL injection?

	Can I trigger validation messages?

* After all of the above, what does code coverage look like?

* How do I know that routing works correctly?

	Do all possible routes go somewhere?

	Can some urls get past routing and invoke SkyNet?

* Run db test scenarios.

* Does the html output adhere to standards?

