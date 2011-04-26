<?php
/**
 * Task for creating unit test files for PHPUnit testing
 *
 * @package    sfPhpunitPlugin
 * @subpackage task
 *
 * @author     Pablo Godel <pgodel@gmail.com>
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPhpunitCreateModelsTestTask extends sfBasePhpunitCreateTask
{
	// list of methods to not create a test method.
	private $skipMethods = array(
    '__toString',
    '__construct',
	);

	private $modelTypes = array(
    'propel' => array(
    'classFileSuffix' => '.php',
    'default_model_path' => 'lib/model',
    'default_connection' => 'propel',
  	'default_target' => 'model',
    'ignored_directory' => array('om', 'map'),
	),
    'doctrine' => array(
    'classFileSuffix' => '.class.php',
    'default_model_path' => 'lib/model/doctrine',
    'default_connection' => 'doctrine',
		'default_target' => 'model',
    'ignored_directory' => array('om', 'map'),
	),
	);

	/**
	 * @see sfTask
	 */
	protected function configure()
	{
	  parent::configure();
	  
		$this->addArguments(array(
  		new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'Application that will be used to load configuration before running tests'),
		));

		$this->addOptions(array(
			new sfCommandOption('type', null, sfCommandOption::PARAMETER_REQUIRED , 'Model type (propel,doctrine)', 'propel'),
			new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'Environment that will be used to load configuration before running tests', 'test'),
			new sfCommandOption('target', null, sfCommandOption::PARAMETER_REQUIRED, 'The location where to save the tests (inside test directory)', 'models'),
			new sfCommandOption('file_suffix', null, sfCommandOption::PARAMETER_REQUIRED, 'File suffix/extension, only needed if type option is not defined', '' ),
			new sfCommandOption('skip_methods', null, sfCommandOption::PARAMETER_OPTIONAL, 'List of methods to skip (multiple methods separated by comma)', ''),
			new sfCommandOption('class', 'c', sfCommandOption::PARAMETER_OPTIONAL, 'The class', ''),
		));

		$this->namespace = 'phpunit';
		$this->name = 'create-models';
		$this->briefDescription = 'Creates a stub class of a lib/model class for PHPUnit testing';

		$this->detailedDescription = <<<EOF
The [phpunit:create] task creates a stub class of a lib/model Class to be used by PHPUnit testing
EOF;
	}

	/**
	 * @see sfTask
	 */
	protected function execute($arguments = array(), $options = array())
	{
	  $this->checkORMType($options['type']);
	  parent::execute($arguments,$options);
    $this->_runInitTask();

		$options['application'] = $arguments['application'];

		// still no class_path given? take the default one!
		if (empty($options['class_path']))
		{
			$options['class_path'] = $this->modelTypes[$options['type']]['default_model_path'];
		}

		if (!empty($options['skip_methods']))
		{
			$methods = explode(',', $options['skip_methods']);

			foreach($methods as $method)
			{
				array_push($this->skipMethods, $method);
			}
		}

		// a custom class given?
		if(!empty($options['class']))
		{
			if (strpos($options['class_path'] , ':') !== false)
			{
				throw new sfCommandException(': is not supported in class_path when specifying the class name.');
			}

			$options['libpath'] = $options['class_path'];

			$this->createTestClass($options, $options['target']);

			return;
		}

		$paths = explode(':', $options['class_path']);

		$namespaces= array();

		foreach($paths as $path)
		{
			$finder= sfFinder::type('directory');

			$ignoredDirs= $this->modelTypes[$options['type']]['ignored_directory'];

			foreach ($ignoredDirs as $ignDir)
			{
				$finder= $finder->not_name($ignDir);
			}

			$dirs= $finder->in($path);

			foreach ($dirs as $dir)
			{
				if (is_dir($dir))
				{
					$namespaces[]= $dir;
				}
			}
		}

		$paths= array_merge($paths, $namespaces);

		foreach ($paths as $path)
		{
			$options['libpath'] = $path;

			$dir = new DirectoryIterator($path);

			$this->logSection('phpunit', sprintf('Searching %s', $path));

			while ($dir->valid())
			{
				if (strpos($dir, '.php') !== false)
				{
					$subfolder = basename(dirname($path.DIRECTORY_SEPARATOR.$dir));

					$suffix = !empty($options['file_suffix'])? $options['file_suffix'] : $this->modelTypes[$options['type']]['classFileSuffix'];
					$options['class'] = str_replace($suffix, '', $dir);

					$this->createTestClass($options, $subfolder);
				}

				$dir->next();
			}

		}

		$this->_runInitTask();
	}

	private function createTestClass($arguments, $subfolder = null)
	{
		$className = $arguments['class'];

		if (empty($className))
		{
			throw new sfCommandException('Class not specified.');
		}

		// if path is relative, add symfony project root path
		if ($arguments['libpath'][0] != DIRECTORY_SEPARATOR)
		{
			$arguments['libpath'] = sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.$arguments['libpath'];
		}

		$targetDir = 'models';
		if ($subfolder) $targetDir.= '/'.$subfolder;

		$this->_createDir($targetDir);

		// if class has interface in name, ignore it.
		if (stripos($className, 'interface') !== false)
		{
			if ($arguments['verbose'])
			{
				$this->logSection('phpunit', sprintf('Skipped interface class %s', $className));
			}
			return;
		}

		$suffix = !empty($arguments['file_suffix'])? $arguments['file_suffix'] : $this->modelTypes[$arguments['type']]['classFileSuffix'];

		$classFile = $className.$suffix;

		$classFilePath = $arguments['libpath'].DIRECTORY_SEPARATOR.$classFile;
		if (!file_exists($classFilePath))
		{
			throw new sfCommandException(sprintf('PHP file %s not found.', $classFilePath));
		}		
 
	  $vars = array(
            'className' => $className.'TestCase',
            'parentName' => 'sfBasePhpunitTestCase', 
            'modelClassName' => $arguments['class'],
            'methods' => $this->_renderMethods($className));

          $this->_createModelClass($targetDir, $vars);
	}
	
	protected function _createModelClass($targetDir, array $vars = array())
	{  
    if (!isset($vars['parentName'])) {
      throw new Exception('The parent class has to be defined');
    }
    if (!isset($vars['modelClassName']) || !class_exists($vars['modelClassName'], true)) {
      throw new Exception('The model class `'.$vars['modelClassName'].'` should be defined and exist');
    }
	  
	  if (empty($vars['methods'])) {
      if ($this->_isVerbose()) {
        $this->logSection('phpunit', sprintf('Skipped class %s with no methods', $vars['modelClassName']));
      }
      return;
    }
	   
	  $source = 'model/ModelTestCase.tpl';
	  if ((strpos($vars['modelClassName'],'Table') !== false || strpos($vars['modelClassName'],'Peer'))) {
      $source = 'model/ModelTableTestCase.tpl';
    }
	  
	  return $this->_createClass($targetDir, $source, $vars);
	}
	
	protected function _renderMethods($className)
	{
	  $rc = new ReflectionClass($className);
    $methodsOutput = '';
    foreach ($rc->getMethods() as $method) {
      // compare filename where method resides to make sure we are not including a method from a parent class.
      if ($method->getFileName() != $rc->getFileName()) continue;
      if (in_array($method->getName(), $this->skipMethods)) continue;
        
      
      $methodsOutput .= $this->_renderTemplate('model/_method.tpl', array(
        'methodName' => ucfirst($method->getName())));
    }

    return $methodsOutput;
	}
	
  protected function checkORMType($type)
  {
    $supported = array_keys($this->modelTypes);
    if (!in_array($type, $supported)) {
      throw new Exception('The supported ORM `'.implode('`, `', $supported).'` . But you give a `'.$type.'`');
    } 
  }
}