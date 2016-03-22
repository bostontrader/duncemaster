#Test Plan

WARNING: The testing for this app uses shark/simple_html_dom which appears to have a bizarre 
error when reading a tbody tag.  Search the source, find the single occurrence of 'tbody', and
comment out that line. Then everything will then work as expected.

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

##Fixture Data

Much testing involves the db. This is a notoriously difficult thing to test.  Here I describe my
approach.  But first a quick review:

We are using PHPUnit and IntegrationTestCase from CakePHP.  This requires a 'test' db configuration
in app.config.  When this runs, it will drop all the tables, if any, presently in the test db,
and create new ones, based on the fixtures specified in the test case.  This presents two obvious
issues: 1) Where to obtain/specify the schema for the required tables, and 2) where to actually
get the data.

If we specify the schema in the fixtures we would have to manually keep it in sync with everything
else and it definitely doesn't participate in the Cake migration process.  It is however easy 
to tell Cake testing to use the schema from another db, such as perhaps from myapp-dev,
by setting the $import member in a fixture class.  Hopefully this schema is more elegantly kept in synch
with everything else and would be a good source to copy. This will give us the schema, but not the data.

Getting the data is the hard part.  Here we have two basic choices: 1) Hard-wire example data in 
the fixture files, or 2) import from another db (as with the schema).  These choices are confounded
by the fact that realistic example data will include many records that are all intricately
wired together via their keys.  

We need to carefully ensure that there is sufficient variation
in order to exercise all the nooks and crannies of our code. For example, a list of classes for one
teacher should not display classes for another. To make sure this is not happening, we need to
have records referring to both teachers, and ensure that only the proper subset is used. In fact
I suggest that explicitly testing for this variation is desirable.  So for example, our list 
of records in one screen is known to be the desired subset, filtered from other records, instead
of all records, that just don't have sufficient variation.

We need some method of obtaining bad records.  We'll need something to trigger the various exceptions
and validations.

During testing we eventually need a fairly large db-style capacity for dealing with the
data.  For example, we need WHERE and ORDER clauses, we need to do joins, and we need to use
aggregate functions.  This is not real easy to do using hard-wired fixture data and looks quite
a lot like re-inventing wheels.

All that hand-wringing said, here's my approach:

1. Most of the fixture data lives in a db named myapp-fixture.  This is a showcase for perfect
data only.  Nothing in this db should violate any validation or constraint.



##Cheat Sheet
Execute phpunit.  Feed it whatever args you want...
cd $STACK_ROOT/html/duncemaster
$STACK_ROOT/php/bin/php vendor/bin/phpunit tests/TestCase/Controller

http://localhost/myapp?XDEBUG_SESSION_START=n
var_dump($n);
export XDEBUG_CONFIG="idekey=PHPSTORM"