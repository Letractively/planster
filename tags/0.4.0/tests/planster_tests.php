<?php
require "phpunit.php";

error_reporting(E_ALL);

require_once (realpath (dirname (__FILE__) . '/../config/config.inc.php'));
require_once (src_path . '/Event.inc.php');
require_once (src_path . '/Invitation.inc.php');

class SelfTestResult extends TextTestResult {
    /* Specialize result class for use in self-tests, to handle
       special situation where many tests are expected to fail. */
    function SelfTestResult() {
	$this->TextTestResult();
	echo '<table class="details">';
	echo '<tr><th>Test name</th><th>Result</th><th>Meta-result</th></tr>';
    }

    function _startTest($test) {
	print('<tr><td>');
	if (phpversion() > '4') {
	    printf("%s - %s ", get_class($test), $test->name());
	} else {
	    printf("%s ", $test->name());
	}
	print('</td>');
	flush();
    }

    function _endTest($test) {
	/* Report both the test result and, for this special situation
	   where some tests are expected to fail, a "meta" test result
	   which indicates whether the test result matches the
	   expected result. */
	$expect_failure = preg_match('/fail/i', $test->name());
	$test_passed = ($test->failed() == 0);

	if ($test->errored())
	    $outcome = "<span class=\"Error\">ERROR</span>";
	else if ($test->failed())
	    $outcome = "<span class=\"Failure\">FAIL</span>";
	else
	    $outcome = "<span class=\"Pass\">OK</span>";

	if ($test->errored())
	    $meta_outcome = '<span class="Unknown">unknown</span>';
	else
	    $meta_outcome = ($expect_failure xor $test_passed)
		? '<span class="Expected">as expected</span>'
		: '<span class="Unexpected">UNEXPECTED</span>';

	printf("<td>$outcome</td><td>$meta_outcome</td></tr>\n");
	flush();
    }
}

class PLANTests extends TestCase
{
	var $_people;

	function setUp ()
	{
		$this->plan = new Event ();
		$this->_people = array ('a', 'b', 'c', 'd', 'e');
	}

	function tearDown ()
	{
		$this->plan->erase ();
	}

	function _init ()
	{
		$this->plan->setOwner ('foo@bar.com');
		$this->plan->setName ('fooPlan');
		$this->plan->save ();
	}

	function _initPeople ()
	{
		$id = $this->plan->getId ();

		foreach ($this->_people as $name)
		{
			$mail = $name . '@mail.com';
			$inv = new Invitation ($name, $mail, false, $id);
			$this->plan->invite ($inv);
		}
	}

	function _testPeople ($event)
	{
		$got = $event->getPeople ();

		$this->assertEquals (count ($this->_people), count ($got), 'Wrong amount of people');

		foreach ($got as $person)
		{
			$name = $person->getName ();
			$this->assert (in_array ($name, $this->_people), 'Invalid person ' . $person->getName ());
		}
	}

	function testOwner ()
	{
		$this->plan->setOwner ('some@body.com');
		$this->assertEquals ('some@body.com', $this->plan->getOwner (), 'Wrong owner');
	}

	function testSave ()
	{
		$this->assert (!$this->plan->save (), 'Should not be saved (1)');
		$this->plan->setName ('fooPlan');
		$this->assert (!$this->plan->save (), 'Should not be saved (2)');
		$this->plan->setOwner ('foo@bar.cc');
		$this->assert ($this->plan->save (), 'Should be saved');
	}

	function testLoadAndSave ()
	{
		$this->_init ();
		$this->assert ($this->plan->getId () != NULL, 'Illegal ID');

		$this->assert ($this->plan->load (), 'Could not reload PLAN');
		$this->assertEquals ('foo@bar.com', $this->plan->getOwner (),
						'Problem reloading owner');
		$this->assertEquals ('fooPlan', $this->plan->getName (),
						'Problem reloading name');

		$other = new Event ($this->plan->getId ());
		$other->load ();
		$this->assertEquals ($this->plan->getOwner (), $other->getOwner (),
						'Problem loading/saving owner');
		$this->assertEquals ($this->plan->getName (), $other->getName (),
						'Problem loading/saving name');
	}

	function testErase ()
	{
		$this->_init ();

		$other = new Event ($this->plan->getId ());
		$this->assert ($other->load (), 'PLAN could not be loaded');
		$this->plan->erase ();
		$this->assert (!$other->load (), 'Erased PLAN could be loaded');
	}

	function testInvite ()
	{
		$this->_init ();
		$this->_initPeople ();
		$this->_testPeople ($this->plan);
	}

	function testClone ()
	{
		$ev = new Event ('demo00000001');
		$this->assert ($ev->load (), 'Could not load demo event');

		$cl = $ev->cloneIt (1, true, true, true);
		$cl = new Event ($cl->getId ());

		$orPeople = $ev->getPeople ();
		$clPeople = $cl->getPeople ();

		$this->assert (count ($orPeople) > 1, 'Too few entries');
		$this->assertEquals (count ($orPeople), count ($clPeople),
					'Number of people does not match');

		for ($i = 0; $i < count ($orPeople); $i++)
		{
			$this->assertEquals ($orPeople [$i]->getName (),
						$clPeople [$i]->getName ());
		}

		$orGroups = $ev->getGroups ();
		$clGroups = $cl->getGroups ();

		for ($i = 0; $i < count ($orGroups); $i++)
		{
			$this->assertEquals (	$orGroups [$i]->getName (),
						$clGroups [$i]->getName (),
						'Group does not match');

			$orDates = $orGroups [$i]->getChildren ();
			$clDates = $clGroups [$i]->getChildren ();

			for ($j = 0; $j < count ($orDates); $j++)
			{
				$orName = $orDates [$j]->getDate ();
				$clName = $clDates [$j]->getDate ();
				$this->assertEquals ($orName, $clName,
						'Date does not match');
			}
		}

		for ($i = 0; $i < count ($orPeople); $i++)
		{
			$orPerson = $orPeople [$i];
			$clPerson = $clPeople [$i];

			for ($j = 0; $j < count ($orGroups); $j++)
			{
				$orDates = $orGroups [$j]->getChildren ();
				$clDates = $clGroups [$j]->getChildren ();

				for ($k = 0; $k < count ($orDates); $k++)
				{
					$orDate = $orDates [$k];
					$clDate = $clDates [$k];

					$orStat = $orPerson->getStatus
							($ev->getId(), $orDate);
					$clStat = $clPerson->getStatus
							($cl->getId(), $clDate);
					$this->assertEquals ($orStat, $clStat,
								'Wrong status');
				}
			}
		}
	}

	function testGetGroups ()
	{
		$this->assert (false, 'TODO');
	}

	function testValidID ()
	{
		$this->assert (! Event :: validId (''), 1);
		$this->assert (! Event :: validId ('foo'), 2);
		$this->assert (Event :: validId ('abcdefghijkl'), 3);
		$this->assert (! Event :: validId ('abcdefghijklm'), 4);
		$this->assert (! Event :: validId ('abcdefghijk'), 5);
	}
}

$suite = new TestSuite ();
$suite->addTest (new TestSuite ('PLANTests'));
?>
