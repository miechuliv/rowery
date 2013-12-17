<?php
final class Loader {
	protected $registry;

    // tablica mocków - kluczem jest nazwa modelu
    private $_mocks = array();

    // production || testing
    private $_mode;
	
	public function __construct($registry,$mode = 'production') {
		$this->registry = $registry;

        $this->_mode = $mode;
	}

    /*
     * pozwala na podmienienie prawdziwej klasy mockiem
     */

    public function addMock($key,$mock)
    {
        $this->_mocks[$key] = $mock;
    }
	
	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
		
	public function library($library) {
        /*
         * nie ma potrzeby ładowania prawdziwej biblioteki
         */
        if($this->_mode == 'testing')
        {
            return false;
        }

		$file = DIR_SYSTEM . 'library/' . $library . '.php';
		
		if (file_exists($file)) {
			include_once($file);
		} else {
			trigger_error('Error: Could not load library ' . $library . '!');
			exit();					
		}
	}
	
	public function helper($helper) {

        /*
         *  nie ma potrzeby ładowania prawdziwego helpera
         */
        if($this->_mode == 'testing')
        {
            return false;
        }

		$file = DIR_SYSTEM . 'helper/' . $helper . '.php';
		
		if (file_exists($file)) {
			include_once($file);
		} else {
			trigger_error('Error: Could not load helper ' . $helper . '!');
			exit();					
		}
	}
		
	public function model($model) {

        /*
         * zwraca makiete zamiast prawdziwego modelu
         */
        if($this->_mode == 'testing')
        {
            if(!isset($this->_mocks[$model]))
            {
                 ob_start();
                 $content = var_dump($this->_mocks);
                 ob_end_clean();
                 throw new Exception("Unable to load mock: ".$model." ".$content);
            }
            $this->registry->set('model_' . str_replace('/', '_', $model), $this->_mocks[$model]);

            return false;
        }


		$file  = DIR_APPLICATION . 'model/' . $model . '.php';
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
		
		if (file_exists($file)) { 
			include_once($file);
			
			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {
			trigger_error('Error: Could not load model ' . $model . '!');
			exit();					
		}
	}
	 
	public function database($driver, $hostname, $username, $password, $database) {
		$file  = DIR_SYSTEM . 'database/' . $driver . '.php';
		$class = 'Database' . preg_replace('/[^a-zA-Z0-9]/', '', $driver);
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set(str_replace('/', '_', $driver), new $class($hostname, $username, $password, $database));
		} else {
			trigger_error('Error: Could not load database ' . $driver . '!');
			exit();				
		}
	}

    /*
     * tu nie ma potrzeby podmiany bo mozna podstawic w rejestrze przekzanym do controller / modelu
     */
	public function config($config) {
		$this->config->load($config);
	}

    /*
   * tu nie ma potrzeby podmiany bo mozna podstawic w rejestrze przekzanym do controller / modelu
   */
	public function language($language) {
		return $this->language->load($language);
	}		
} 
?>
