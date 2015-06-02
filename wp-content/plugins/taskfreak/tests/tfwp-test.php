<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

Automated tests
Require Firefox, Selenium2, and PHPUnit 3.6 (at least)

Run with (example, on Linux)
	
	java -jar selenium-server-standalone-2.35.0.jar
	# or
	DISPLAY=:99 xvfb-run java -jar selenium-server-standalone-2.35.0.jar
	
	# then (requires PHPUnit >= 3.6)
	phpunit tfwp-test.php
	
Selenium Server 2 available at http://docs.seleniumhq.org/download/

up-to-date PHPUnit available with :
sudo pear channel-discover pear.phpunit.de
sudo pear channel-discover pear.symfony-project.com
sudo pear channel-discover components.ez.no
sudo pear update-channels
sudo pear upgrade-all
sudo pear install --alldeps phpunit/PHPUnit  
*/

class TestTFWP extends PHPUnit_Extensions_Selenium2TestCase {

	public function setUp() {
		$this->setHost('localhost');
		$this->setPort(4444);
		$this->setBrowser('firefox');
		$this->setBrowserUrl('http://taskfreak.pro/tfwp-test/');
	}
	
	private function connectAs($who) {
		$name = array(
				'subscriber' 	=> 'sub scriber',
				'contributor' 	=> 'contrib utor',
				'author'		=> 'au thor',
				'editor'		=> 'ed itor',
				'admin'			=> 'admin',
		);
		// $this->currentWindow()->maximize();
		$this->url('wp-login.php');
		$form = $this->byCssSelector('form');
		$action = $form->attribute('action');
		$this->assertEquals('http://taskfreak.pro/tfwp-test/wp-login.php', $action);
		$this->byId('user_login')->value($who);
		sleep(5);
		$this->byId('user_pass')->value('test'.($who == 'admin' ? 'test' : ''));
		$form->submit();
		$welcome = $this->byCssSelector('#wp-admin-bar-my-account a')->text();
		$this->assertRegExp('/howdy, '.$name[$who].'/i', $welcome);
	}

	/*
	public function tearDown() {
		if ($this->getStatus() != 0) {
			echo "=======================================================================\n";
			echo $this->getStatusMessage()."\n";
			echo "============================= SOURCE ==================================\n";
			echo $this->source()."\n";
		}
	}*/
	
	/**
	 * @group preliminary tests
	 */
	public function testHasLoginForm() {
		// $this->currentWindow()->maximize();
		$this->url('wp-login.php');
		
		$username = $this->byId('user_login');
		$password = $this->byId('user_pass');
		
		$this->assertEquals('', $username->value());
		$this->assertEquals('', $password->value());
	}
	
	/**
	 * @group preliminary tests
	 */
	public function testLoginFormSubmitsToAdmin() {
		// $this->currentWindow()->maximize();
		$this->url('wp-login.php');
	
		$form = $this->byCssSelector('form');
		$action = $form->attribute('action');
		
		$this->assertEquals('http://taskfreak.pro/tfwp-test/wp-login.php', $action);
		
		$this->byId('user_login')->value('admin');
		$this->byId('user_pass')->value('testtest');
		$form->submit();
		
		$welcome = $this->byCssSelector('#wp-admin-bar-my-account a')->text();
		
		$this->assertRegExp('/howdy, admin/i', $welcome);
	}

	/**
	 * @group project list
	 */
	public function testProjectListLoggedOut() {
		// $this->currentWindow()->maximize();
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4');
		$this->assertContains('Any visitor has access to this project', $this->byCssSelector('.widefat')->text()); 
		$this->assertNotContains('Project access level : subscribers and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : contributors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : authors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : editors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : admin', $this->byCssSelector('.widefat')->text());
	}

	/**
	 * @group project list
	 */
	public function testProjectListLoggedInAsSubscriber() {
		$this->connectAs('subscriber');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4');
		$this->assertContains('Any visitor has access to this project', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : subscribers and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : contributors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : authors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : editors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : admin', $this->byCssSelector('.widefat')->text());
	}

	/**
	 * @group project list
	 */
	public function testProjectListLoggedInAsContributor() {
		$this->connectAs('contributor');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4');
		$this->assertContains('Any visitor has access to this project', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : subscribers and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : contributors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : authors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : editors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : admin', $this->byCssSelector('.widefat')->text());
	}
	
	/**
	 * @group project list
	 */
	public function testProjectListLoggedInAsAuthor() {
		$this->connectAs('author');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4');
		$this->assertContains('Any visitor has access to this project', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : subscribers and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : contributors and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : authors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : editors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : admin', $this->byCssSelector('.widefat')->text());
	}
	
	/**
	 * @group project list
	 */
	public function testProjectListLoggedInAsEditor() {
		$this->connectAs('editor');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4');
		$this->assertContains('Any visitor has access to this project', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : subscribers and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : contributors and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : authors and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : editors and above', $this->byCssSelector('.widefat')->text());
		$this->assertNotContains('Project access level : admin', $this->byCssSelector('.widefat')->text());
	}
	
	/**
	 * @group project list
	 */
	public function testProjectListLoggedInAsAdmin() {
		$this->connectAs('admin');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4');
		$this->assertContains('Any visitor has access to this project', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : subscribers and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : contributors and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : authors and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : editors and above', $this->byCssSelector('.widefat')->text());
		$this->assertContains('Project access level : admin', $this->byCssSelector('.widefat')->text());
	}
	
	/**
	 * @group task list 1
	 */
	public function testTaskListLoggedOut() {
		// $this->currentWindow()->maximize();
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100');
		$this->assertContains('Any visitor can read this task', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Any visitor can read this closed task', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but admin can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but Au Thor can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to contributors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to authors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to editors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to admin', $this->byId('tfk_tasksheet')->text());
		
		$this->url($this->byCssSelector('#tfk_tasksheet #tfk_sts1-3')->attribute('href'));
		$this->assertContains('You can\'t change this status', $this->byClassName('entry-content')->text());
		
		// access without nonce
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100&edit=3&status=60&js=1');
		$this->assertContains('Sorry, request forbidden for security reasons', $this->byClassName('entry-content')->text());
	}
	
	/**
	 * @group task list
	 */
	public function testTaskListFilteredLoggedOut() {
		// $this->currentWindow()->maximize();
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=60&npg=100');
		$this->assertNotContains('Any visitor can read this task', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Any visitor can read this closed task', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but admin can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but Au Thor can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to contributors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to authors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to editors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to admin', $this->byId('tfk_tasksheet')->text());
	}
	
	/**
	 * @group task list
	 */
	public function testTaskListLoggedInAsSubscriber() {
		$this->connectAs('subscriber');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100');
		$this->assertContains('Any visitor can read this task', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Any visitor can read this closed task', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but admin can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but Au Thor can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to contributors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to authors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to editors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to admin', $this->byId('tfk_tasksheet')->text());
		
		$this->url($this->byCssSelector('#tfk_tasksheet #tfk_sts1-3')->attribute('href'));
		$this->assertContains('You can\'t change this status', $this->byClassName('entry-content')->text());
				
		// access without nonce
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100&edit=3&status=60&js=1');
		$this->assertContains('Sorry, request forbidden for security reasons', $this->byClassName('entry-content')->text());
	}
	
	/**
	 * @group task list
	 */
	public function testTaskListFilteredLoggedInAsSubscriber() {
		$this->connectAs('subscriber');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=60&npg=100');
		$this->assertNotContains('Any visitor can read this task', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Any visitor can read this closed task', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but admin can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but Au Thor can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to contributors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to authors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to editors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to admin', $this->byId('tfk_tasksheet')->text());
	}
	
	/**
	 * @group task list
	 */
	public function testTaskListLoggedInAsContributor() {
		$this->connectAs('contributor');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100');
		$this->assertContains('Any visitor can read this task', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Any visitor can read this closed task', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but admin can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but Au Thor can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to contributors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to authors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to editors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to admin', $this->byId('tfk_tasksheet')->text());
		
		$this->url($this->byCssSelector('#tfk_tasksheet #tfk_sts1-3')->attribute('href'));
		$this->assertContains('You can\'t change this status', $this->byClassName('entry-content')->text());
		
		// access without nonce
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100&edit=3&status=60&js=1');
		$this->assertContains('Sorry, request forbidden for security reasons', $this->byClassName('entry-content')->text());
	}
	
	/**
	 * @group task list
	 */
	public function testTaskListLoggedInAsAuthor() {
		$this->connectAs('author');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100');
		$this->assertContains('Any visitor can read this task', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Any visitor can read this closed task', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but admin can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Nobody but Au Thor can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to contributors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to authors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to editors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to admin', $this->byId('tfk_tasksheet')->text());
		
		$this->url($this->byCssSelector('#tfk_tasksheet #tfk_sts1-3')->attribute('href'));
		$this->assertContains('You can\'t change this status', $this->byClassName('entry-content')->text());
		
		// access without nonce
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100&edit=3&status=60&js=1');
		$this->assertContains('Sorry, request forbidden for security reasons', $this->byClassName('entry-content')->text());
	}

	/**
	 * @group task list
	 */
	public function testTaskListLoggedInAsEditor() {
		$this->connectAs('editor');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100');
		$this->assertContains('Any visitor can read this task', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Any visitor can read this closed task', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but admin can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but Au Thor can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to contributors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to authors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to editors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Access to this task is restricted to admin', $this->byId('tfk_tasksheet')->text());
		
		$this->url($this->byCssSelector('#tfk_tasksheet #tfk_sts2-3')->attribute('href'));
		$this->assertContains('OK ! Task has been set to Suspended', $this->byClassName('entry-content')->text());
				
		// access without nonce
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100&edit=3&status=60&js=1');
		$this->assertContains('Sorry, request forbidden for security reasons', $this->byClassName('entry-content')->text());
	}
	
	/**
	 * @group task list
	 */
	public function testTaskListLoggedInAsAdmin() {
		$this->connectAs('admin');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100');
		$this->assertContains('Any visitor can read this task', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Nobody but admin can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertNotContains('Nobody but Au Thor can read this draft', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to contributors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to authors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to editors and above', $this->byId('tfk_tasksheet')->text());
		$this->assertContains('Access to this task is restricted to admin', $this->byId('tfk_tasksheet')->text());
		
		$this->url($this->byCssSelector('#tfk_tasksheet #tfk_sts3-3')->attribute('href'));
		$this->assertContains('OK ! Task has been set to Closed', $this->byClassName('entry-content')->text());
		
		// access without nonce
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&mode=tasks&filter_task=all&npg=100&edit=3&status=60&js=1');
		$this->assertContains('Sorry, request forbidden for security reasons', $this->byClassName('entry-content')->text());
	}
	
	/**
	 * @group task view
	 */
	public function testTaskViewLoggedOut() {
		// $this->currentWindow()->maximize();
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=1');
		$this->assertContains('Any visitor can read this task', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
		
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=4');
		$this->assertNotContains('Access to this task is restricted to subscribers and above', $this->source());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertContains('Sorry, you can\'t read this task', $this->byClassName('tfk_err')->text());
	}
	
	/**
	 * @group task view
	 */
	public function testTaskViewLoggedInAsSubscriber() {
		$this->connectAs('subscriber');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=1');
		$this->assertContains('Any visitor can read this task', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
		
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=4');
		$this->assertContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
		
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=5');
		$this->assertNotContains('Access to this task is restricted to contributors and above', $this->source());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertContains('Sorry, you can\'t read this task', $this->byClassName('tfk_err')->text());
	}
	
	/**
	 * @group task view
	 */
	public function testTaskViewLoggedInAsContributor() {
		$this->connectAs('contributor');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=4');
		$this->assertContains('Access to this task is restricted to subscribers and above', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
	
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=5');
		$this->assertContains('Access to this task is restricted to contributors and above', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
		
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=6');
		$this->assertNotContains('Access to this task is restricted to authors and above', $this->source());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertContains('Sorry, you can\'t read this task', $this->byClassName('tfk_err')->text());
	}
	
	/**
	 * @group task view
	 */
	public function testTaskViewLoggedInAsAuthor() {
		$this->connectAs('author');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=5');
		$this->assertContains('Access to this task is restricted to contributors and above', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
	
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=6');
		$this->assertContains('Access to this task is restricted to authors and above', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
		
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=7');
		$this->assertNotContains('Access to this task is restricted to editors and above', $this->source());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertContains('Sorry, you can\'t read this task', $this->byClassName('tfk_err')->text());
	}
	
	/**
	 * @group task view
	 */
	public function testTaskViewLoggedInAsEditor() {
		$this->connectAs('editor');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=6');
		$this->assertContains('Access to this task is restricted to authors and above', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
	
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=7');
		$this->assertContains('Access to this task is restricted to editors and above', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
		
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=8');
		$this->assertNotContains('Access to this task is restricted to admin', $this->source());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertContains('Sorry, you can\'t read this task', $this->byClassName('tfk_err')->text());
	}
	
	/**
	 * @group task view
	 */
	public function testTaskViewLoggedInAsAdmin() {
		$this->connectAs('admin');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=7');
		$this->assertContains('Access to this task is restricted to editors and above', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
	
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=8');
		$this->assertContains('Access to this task is restricted to admin', $this->byId('tfk_task_link')->text());
		$this->assertNotContains('Sorry, item not found', $this->source());
		$this->assertNotContains('Sorry, you can\'t read this task', $this->source());
	}
	
	/**
	 * @group task edit
	 */
	private function forcePostingTask($project_id) {
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit');
		$this->execute(array(
				'script' =>    "if (document.getElementById('tfk_project')) {
									document.getElementById('tfk_project').innerHTML = '<option value=\"$project_id\">forged parameter test !</option>';
								} else {
									var i = document.createElement('input');
									i.setAttribute('type', 'hidden');
									i.setAttribute('name', 'project_id');
									i.setAttribute('value', '$project_id');
									document.getElementById('tfk_edit_task_form').appendChild(i);
								}",
				'args' => array()));
				$this->byCssSelector('button[type="submit"]')->click();
				$this->assertContains("Sorry, you can't post in this project", $this->byClassName('tfk_err')->text());
	}
	
	/**
	 * @group task edit
	 */
	public function testTaskEditLoggedOut() {
		// $this->currentWindow()->maximize();
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit=1');
		$this->assertContains("Sorry, you can't edit tasks when logged out", $this->byClassName('tfk_err')->text());
		
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit');
		$this->assertContains("Sorry, you can't edit tasks when logged out", $this->byClassName('tfk_err')->text());
		
		$this->execute(array(
				'script' =>    "var f = document.createElement('form');
								f.setAttribute('id','forged_form');
								f.setAttribute('action','/tfwp-test/?page_id=4&edit&noheader=1');
								f.setAttribute('method', 'post');
								var i = document.createElement('input');
								i.setAttribute('type', 'hidden');
								i.setAttribute('name', 'edit');
								i.setAttribute('value', '');
								f.appendChild(i);
								var i = document.createElement('input');
								i.setAttribute('type', 'hidden');
								i.setAttribute('name', 'project_id');
								i.setAttribute('value', '1');
								f.appendChild(i);
								document.getElementById('post-4').appendChild(f);", 
				'args' => array()));
		$this->byId('forged_form')->submit();
		$this->assertContains("Sorry, you can't edit tasks when logged out", $this->byClassName('tfk_err')->text());
	}

	/**
	 * @group task edit
	 */
	public function testTaskEditLoggedInAsSubscriber() {
		$this->connectAs('subscriber');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit=1');
		// this task belongs to (was created by) admin AND subscribers can't manage the project
		$this->assertContains('Sorry, you can\'t edit this task', $this->byClassName('tfk_err')->text());
	
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit');
		$this->byCssSelector('button[type="submit"]')->click();
		$this->assertContains('Unavailable project', $this->byClassName('tfk_err')->text());
		
		$this->forcePostingTask(1);
	}

	/**
	 * @group task edit
	 */
	public function testTaskEditLoggedInAsContributor() {
		$this->connectAs('contributor');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit=1');
		// this task belongs to (was created by) admin AND contributors can't manage the project
		$this->assertContains('Sorry, you can\'t edit this task', $this->byClassName('tfk_err')->text());
	
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit');
		$this->byCssSelector('button[type="submit"]')->click();
		$this->assertContains('Unavailable project', $this->byClassName('tfk_err')->text());
		
		$this->forcePostingTask(2);
	}
	
	/**
	 * @group task edit
	 */
	public function testTaskEditLoggedInAsAuthor() {
		$this->connectAs('author');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit=1');
		// this task belongs to (was created by) admin AND authors can't manage the project
		$this->assertContains('Sorry, you can\'t edit this task', $this->byClassName('tfk_err')->text());
		
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit');
		// author is min. level required to create tasks
		$this->assertContains('Project access level : subscribers and above', $this->byId('tfk_project')->text());
		$this->assertContains('Any visitor has access to this project', $this->byId('tfk_project')->text());
		$this->byCssSelector('button[type="submit"]')->click();
		$this->assertContains('Title should not be blank', $this->byClassName('tfk_err')->text());
		
		$this->forcePostingTask(3);
	}
		
	/**
	 * @group task edit
	 */
	public function testTaskEditLoggedInAsEditor() {
		$this->connectAs('editor');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit=1');
		// this task belongs to (was created by) admin BUT editors CAN manage the project, thus can edit this task
		$this->assertNotContains('Sorry, you can\'t edit this task', $this->source());
		$this->byId('tfk_edit_task_form')->submit();
		$this->assertContains('Changes saved', $this->byClassName('tfk_ok')->text());
		// XXX works with WP 3.6 default theme, but what about next versions ?
	
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit');
		// author is min. level required to create tasks
		$this->assertContains('Project access level : contributors and above', $this->byId('tfk_project')->text());
		$this->assertContains('Project access level : subscribers and above', $this->byId('tfk_project')->text());
		$this->assertContains('Any visitor has access to this project', $this->byId('tfk_project')->text());
		$this->byCssSelector('button[type="submit"]')->click();
		$this->assertContains('Title should not be blank', $this->byClassName('tfk_err')->text());

		// modify task...
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit=1');
		$this->byId('tfk_title')->value(' automated test modification');
		$this->byId('tfk_edit_task_form')->submit();
		sleep(10);
		// ...and check history
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=1');
		$this->byId('tfk_task_history_toggle_show')->click();
        sleep(20);
		$dateMod = $this->byCssSelector('#tfk_task_history tr:last-child td:first-child')->text();
		if (preg_match(';(\d{2})/(\d{2})/(\d{4}) (\d{2}:\d{2});', $dateMod, $m)) {
			$timeMod = strtotime($m[3]."-".$m[2]."-".$m[1]." ".$m[4]); // European date format
		} else {
			$timeMod = strtotime($dateMod);
		}
		if (!$timeMod) {
			$this->fail('Last modification date not found in "'.$dateMod.'"');
		} else {
			$this->assertLessThan(120, abs($timeMod - date('U'))); // XXX ensure that WP timezone setting matches your local setting
			$this->assertContains('Ed Itor', $this->byCssSelector('#tfk_task_history tr:last-child td:nth-child(2)')->text());
			$this->assertContains('Modified (title)', $this->byCssSelector('#tfk_task_history tr:last-child td:last-child')->text());
		}
		
		$this->forcePostingTask(5);
	}
	
	/**
	 * @group task edit
	 */
	public function testTaskEditLoggedInAsAdmin() {
		$this->connectAs('admin');
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit=1');
		$this->assertNotContains('Sorry, you can\'t edit this task', $this->source());
		$this->byId('tfk_edit_task_form')->submit();
		$this->assertContains('Changes saved', $this->byClassName('tfk_ok')->text());
		// XXX works with WP 3.6 default theme, but what about next versions ?
		
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit');
		// author is min. level required to create tasks
		$this->assertContains('Project access level : admin', $this->byId('tfk_project')->text());
		$this->assertContains('Project access level : editors and above', $this->byId('tfk_project')->text());
		$this->assertContains('Project access level : authors and above', $this->byId('tfk_project')->text());
		$this->assertContains('Project access level : contributors and above', $this->byId('tfk_project')->text());
		$this->assertContains('Project access level : subscribers and above', $this->byId('tfk_project')->text());
		$this->assertContains('Any visitor has access to this project', $this->byId('tfk_project')->text());
		$this->byCssSelector('button[type="submit"]')->click();
		$this->assertContains('Title should not be blank', $this->byClassName('tfk_err')->text());
		
		// modify task (remove Ed Itor's change)
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&edit=1');
		$this->byId('tfk_title')->clear();
		$this->byId('tfk_title')->value('Any visitor can read this task');
		$this->byId('tfk_edit_task_form')->submit();
		sleep(10);
		// ...and check history
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=1');
		$this->byId('tfk_task_history_toggle_show')->click();
        sleep(20);
		$dateMod = $this->byCssSelector('#tfk_task_history tr:last-child td:first-child')->text();
		if (preg_match(';(\d{2})/(\d{2})/(\d{4}) (\d{2}:\d{2});', $dateMod, $m)) {
			$timeMod = strtotime($m[3]."-".$m[2]."-".$m[1]." ".$m[4]); // European date format
		} else {
			$timeMod = strtotime($dateMod);
		}
		if (!$timeMod) {
			$this->fail('Last modification date not found in "'.$dateMod.'"');
		} else {
			$this->assertLessThan(120, abs($timeMod - date('U'))); // XXX ensure that WP timezone setting matches your local setting
			$this->assertContains('admin', $this->byCssSelector('#tfk_task_history tr:last-child td:nth-child(2)')->text());
			$this->assertContains('Modified (title)', $this->byCssSelector('#tfk_task_history tr:last-child td:last-child')->text());
		}
	}
	
	/**
	 * @group task comment
	 */
	private function forcePostingComment($task_id) {
		$this->execute(array(
				'script' =>    "var f = document.createElement('form');
								f.setAttribute('id','forged_form');
								f.setAttribute('action','/tfwp-test/?page_id=4&view=$task_id&noheader=1');
								f.setAttribute('method', 'post');
								var i = document.createElement('input');
								i.setAttribute('type', 'hidden');
								i.setAttribute('name', 'edit');
								i.setAttribute('value', '$task_id');
								f.appendChild(i);
								document.getElementById('tfk_task_description').appendChild(f);", 
				'args' => array()));
		$this->byId('forged_form')->submit();
		$this->assertContains("Sorry, you can't comment tasks in this project", $this->byClassName('tfk_err')->text());
	}
	
	/**
	 * @group task comment
	 */
	private function postEmptyComment($task_id) {
		$this->url("http://taskfreak.pro/tfwp-test/?page_id=4&view=$task_id");
		$this->byId('tfk_comment_form')->submit();
		$this->assertContains('Empty comment not allowed', $this->byClassName('tfk_err')->text());
	}
	
	/**
	 * @group task comment
	 */
	private function postComment($task_id) {
		$this->url("http://taskfreak.pro/tfwp-test/?page_id=4&view=$task_id");
		$identifier = 'Automated test '.rand(10000000000000000, 99999999999999999).' '.date('r');
		$this->execute(array( // enter some text in TinyMCE's iframe
				'script' => "document.getElementById('body_ifr').contentDocument.body.innerHTML = '$identifier';",
				'args' => array()));
		$this->byId('tfk_comment_form')->submit();
		$this->assertContains($identifier, $this->source());
	}
	
	/**
	 * @group task comment
	 */
	public function testTaskCommentLoggedOut() {
		// $this->currentWindow()->maximize();
		$this->url('http://taskfreak.pro/tfwp-test/?page_id=4&view=1');
		$this->assertNotContains('<form id="tfk_comment_form"', $this->source());		
		$this->forcePostingComment(1);
	}

	/**
	 * @group task comment
	 */
	public function testTaskCommentLoggedInAsSubscriber() {
		$this->connectAs('subscriber');
		$this->postEmptyComment(1);
		$this->postComment(1);
		$this->forcePostingComment(4);
	}

	/**
	 * @group task comment
	 */
	public function testTaskCommentLoggedInAsContributor() {
		$this->connectAs('contributor');
		$this->postEmptyComment(4);
		$this->postComment(4);
		$this->forcePostingComment(5);
	}
	
	/**
	 * @group task comment
	 */
	public function testTaskCommentLoggedInAsAuthor() {
		$this->connectAs('author');
		$this->postEmptyComment(5);
		$this->postComment(5);
		$this->forcePostingComment(6);
	}
	
	/**
	 * @group task comment
	 */
	public function testTaskCommentLoggedInAsEditor() {
		$this->connectAs('editor');
		$this->postEmptyComment(6);
		$this->postComment(6);
		$this->forcePostingComment(7);
	}

	/**
	 * @group task comment
	 */
	public function testTaskCommentLoggedInAsAdmin() {
		$this->connectAs('admin');
		$this->postEmptyComment(7);
		$this->postComment(7);
	}
	
	/**
	 * @group dashboard
	 */
	public function testDashboardLoggedInAsSubscriber() {
		$this->connectAs('subscriber');
		$this->byCssSelector('#toplevel_page_taskfreak a')->click();
		
		// Please keep these numbers in sync with actual dataset 

		$this->assertEquals('My Tasks', $this->byCssSelector('table.tfk_stats tr:nth-child(1) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(1) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(1) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(1) td:nth-child(4)')->text());
		
		$this->assertEquals('All Tasks', $this->byCssSelector('table.tfk_stats tr:nth-child(2) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(7, $this->byCssSelector('table.tfk_stats tr:nth-child(2) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(2) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(2) td:nth-child(4)')->text());
		
		$this->assertEquals('My Projects', $this->byCssSelector('table.tfk_stats tr:nth-child(3) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(3, $this->byCssSelector('table.tfk_stats tr:nth-child(3) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(3) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(3) td:nth-child(4)')->text());
		
		$this->assertEquals('All Projects', $this->byCssSelector('table.tfk_stats tr:nth-child(4) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(7, $this->byCssSelector('table.tfk_stats tr:nth-child(4) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(4) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(4) td:nth-child(4)')->text());
	}
	
	/**
	 * @group dashboard
	 */
	public function testDashboardLoggedInAsEditor() {
		$this->connectAs('editor');
		$this->byCssSelector('#toplevel_page_taskfreak a')->click();
	
		// Please keep these numbers in sync with actual dataset
	
		$this->assertEquals('My Tasks', $this->byCssSelector('table.tfk_stats tr:nth-child(1) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(1) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(1) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(1) td:nth-child(4)')->text());
	
		$this->assertEquals('All Tasks', $this->byCssSelector('table.tfk_stats tr:nth-child(2) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(7, $this->byCssSelector('table.tfk_stats tr:nth-child(2) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(2) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(2) td:nth-child(4)')->text());
	
		$this->assertEquals('My Projects', $this->byCssSelector('table.tfk_stats tr:nth-child(3) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(6, $this->byCssSelector('table.tfk_stats tr:nth-child(3) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(3) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(3) td:nth-child(4)')->text());
	
		$this->assertEquals('All Projects', $this->byCssSelector('table.tfk_stats tr:nth-child(4) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(7, $this->byCssSelector('table.tfk_stats tr:nth-child(4) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(4) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(4) td:nth-child(4)')->text());
	}
	/**
	 * @group dashboard
	 */
	public function testDashboardLoggedInAsAdmin() {
		$this->connectAs('admin');
		$this->byCssSelector('#toplevel_page_taskfreak a')->click();
	
		// Please keep these numbers in sync with actual dataset
	
		$this->assertEquals('My Tasks', $this->byCssSelector('table.tfk_stats tr:nth-child(1) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(1) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(1) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(1) td:nth-child(4)')->text());
	
		$this->assertEquals('All Tasks', $this->byCssSelector('table.tfk_stats tr:nth-child(2) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(7, $this->byCssSelector('table.tfk_stats tr:nth-child(2) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(2) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(2) td:nth-child(4)')->text());
	
		$this->assertEquals('My Projects', $this->byCssSelector('table.tfk_stats tr:nth-child(3) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(7, $this->byCssSelector('table.tfk_stats tr:nth-child(3) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(3) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(3) td:nth-child(4)')->text());
	
		$this->assertEquals('All Projects', $this->byCssSelector('table.tfk_stats tr:nth-child(4) th:nth-child(1)')->text());
		// In Progress
		$this->assertEquals(7, $this->byCssSelector('table.tfk_stats tr:nth-child(4) td:nth-child(2)')->text());
		// Suspended
		$this->assertEquals(1, $this->byCssSelector('table.tfk_stats tr:nth-child(4) td:nth-child(3)')->text());
		// Closed
		$this->assertEquals(0, $this->byCssSelector('table.tfk_stats tr:nth-child(4) td:nth-child(4)')->text());
	}
}
