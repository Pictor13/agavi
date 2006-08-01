<?php
require_once(dirname(__FILE__) . '/ConfigHandlerTestBase.php');

class FCHTestBase
{
	public	$context,
					$params;
	public function initialize($ctx, $params = array())
	{
		$this->context = $ctx;
		$this->params = $params;
	}
}

class FCHTestActionStack			extends FCHTestBase {}
class FCHTestController				extends FCHTestBase {}
class FCHTestDispatchFilter		extends FCHTestBase implements AgaviIGlobalFilter {
	public function execute(AgaviFilterChain $filterChain, AgaviResponse $response) {}
	public function getContext() {}
	public function initialize(AgaviContext $context, $parameters = array()) {}
}

class FCHTestExecutionFilter	extends FCHTestBase implements AgaviIActionFilter {
	public function execute(AgaviFilterChain $filterChain, AgaviResponse $response) {}
	public function getContext() {}
	public function initialize(AgaviContext $context, $parameters = array()) {}
}

class FCHTestFilterChain			extends FCHTestBase {}
class FCHTestLoggerManager		extends FCHTestBase {}
class FCHTestRequest					extends FCHTestBase {}
class FCHTestResponse					extends FCHTestBase {}
class FCHTestRouting					extends FCHTestBase {}
class FCHTestStorage					extends FCHTestBase
{
	public $suCalled = false;
	public function startup() { $this->suCalled = true; }
}
class FCHTestValidatorManager	extends FCHTestBase {}

class FCHTestDBManager				extends FCHTestBase {}
class FCHTestSecurityFilter		extends FCHTestBase implements AgaviIActionFilter, AgaviISecurityFilter {
	public function execute(AgaviFilterChain $filterChain, AgaviResponse $response) {}
	public function getContext() {}
	public function initialize(AgaviContext $context, $parameters = array()) {}
}
class FCHTestUser							extends FCHTestBase implements AgaviISecurityUser
{
	public function addCredential($credential) {}
	public function clearCredentials() {}
	public function hasCredentials($credential) {}
	public function isAuthenticated() {}
	public function removeCredential($credential) {}
	public function setAuthenticated($authenticated) {}
}

class FactoryConfigHandlerTest extends ConfigHandlerTestBase
{
	protected		$conf;

	protected		$factories;

	protected		$databaseManager,
							$request,
							$storage,
							$validatorManager,
							$user,
							$loggerManager,
							$controller,
							$routing,
							$response;

	public function setUp()
	{
		$this->conf = AgaviConfig::export();
		$this->factories = array();
	}

	public function tearDown()
	{
		AgaviConfig::clear();
		AgaviConfig::import($this->conf);
	}

	public function testFactoryConfigHandler()
	{
		$FCH = new AgaviFactoryConfigHandler();

		$params_ex = array('p1' => 'v1', 'p2' => 'v2');

		AgaviConfig::set('core.use_database', true);
		AgaviConfig::set('core.use_logging', true);
		AgaviConfig::set('core.use_security', true);
		$this->includeCode($c= $FCH->execute(AgaviConfig::get('core.config_dir') . '/tests/factories.xml'));


		// Action stack
		$this->assertSame(
			array(
				'class' => 'FCHTestActionStack',
				'parameters' => $params_ex,
			),
			$this->factories['action_stack']
		);

		// Dispatch filter
		$this->assertSame(
			array(
				'class' => 'FCHTestDispatchFilter',
				'parameters' => $params_ex,
			),
			$this->factories['dispatch_filter']
		);

		// Execution filter
		$this->assertSame(
			array(
				'class' => 'FCHTestExecutionFilter',
				'parameters' => $params_ex,
			),
			$this->factories['execution_filter']
		);

		// Filter chain
		$this->assertSame(
			array(
				'class' => 'FCHTestFilterChain',
				'parameters' => $params_ex,
			),
			$this->factories['filter_chain']
		);

		// Security filter
		$this->assertSame(
			array(
				'class' => 'FCHTestSecurityFilter',
				'parameters' => $params_ex,
			),
			$this->factories['security_filter']
		);

		// Response
		$this->assertSame(
			array(
				'class' => 'FCHTestResponse',
				'parameters' => $params_ex,
			),
			$this->factories['response']
		);

		$this->assertType('FCHTestDBManager', $this->databaseManager);
		$this->assertReference($this, $this->databaseManager->context);
		$this->assertSame($params_ex, $this->databaseManager->params);

		$this->assertType('FCHTestRequest', $this->request);
		$this->assertReference($this, $this->request->context);
		$this->assertSame($params_ex, $this->request->params);

		$this->assertType('FCHTestStorage', $this->storage);
		$this->assertReference($this, $this->storage->context);
		$this->assertSame($params_ex, $this->storage->params);
		$this->assertTrue($this->storage->suCalled);

		$this->assertType('FCHTestValidatorManager', $this->validatorManager);
		$this->assertReference($this, $this->validatorManager->context);
		$this->assertSame($params_ex, $this->validatorManager->params);

		$this->assertType('FCHTestUser', $this->user);
		$this->assertReference($this, $this->user->context);
		$this->assertSame($params_ex, $this->user->params);

		$this->assertType('FCHTestLoggerManager', $this->loggerManager);
		$this->assertReference($this, $this->loggerManager->context);
		$this->assertSame($params_ex, $this->loggerManager->params);

		$this->assertType('FCHTestController', $this->controller);
		$this->assertReference($this, $this->controller->context);
		$this->assertSame($params_ex, $this->controller->params);

		$this->assertType('FCHTestRouting', $this->routing);
		$this->assertReference($this, $this->routing->context);
		$this->assertSame($params_ex, $this->routing->params);
	}

}
?>