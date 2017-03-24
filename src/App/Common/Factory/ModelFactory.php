<?php

namespace App\Common\Factory;

use \Exception;

class ModelFactory
{

    private $adapter;
    private $container;

    public function __construct($adapter, $container = null)
    {
	$this->adapter	 = $adapter;
	$this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
	return $this->container;
    }

    /**
     *
     * @param Container $container
     * @return \App\Common\Model\ModelFactory
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    public function get($className)
    {
	if ($this->getContainer()->offsetExists('EntityManager.' . $className)) {
	    return $this->getContainer()['EntityManager.' . $className];
	}
	if (count(explode('\\', $className)) > 1 && class_exists($className)) {
	    $class = new $className($this->adapter);
	    $class->setContainer($this->container);
	    $this->getContainer()['EntityManager.' . $className] = $class;
	    return $class;
	}
	/**
	 * @TODO put model path in config file
	 */
	if (class_exists($cl = 'Appevent\Model\Table\\' . $className)) {
	    $class = new $cl($this->adapter);
	    $class->setContainer($this->container);
	    $this->getContainer()['EntityManager.' . $className] = $class;
	    return $class;
	}
	if (class_exists($cl = 'Appevent\Generated\Model\Table\\' . $className)) {
	    $class = new $cl($this->adapter);
	    $class->setContainer($this->container);
	    $this->getContainer()['EntityManager.' . $className] = $class;
	    return $class;
	}
	throw new Exception("Class " . $className . " Not found");
    }

}
