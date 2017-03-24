<?php

namespace App\Common\Generator\Core;

use App\Common\Generator\Core\MakeDbTableAbstract;
use App\Common\Generator\Core\ZendCode\Entity;
use App\Common\Generator\Core\ZendCode\EntityItem;
use App\Common\Generator\Core\ZendCode\EntityManager;
use App\Common\Generator\Core\ZendCode\Manager;

/**
 * main class for files creation
 */
abstract class MakeDbTableFactory extends MakeDbTableAbstract
{

    /**
     *
     *  the class constructor
     *
     * @param Array $config
     * @param String $dbname
     * @param String $namespace
     */
    public function __construct($config, $dbname, $namespace)
    {
        parent::__construct($config, $dbname, $namespace);
    }

    /**
     *
     * @return boolean
     */
    public function generate()
    {
        $vars                     = get_object_vars($this);
        $vars['foreignKeysInfo']  = $this->getForeignKeysInfo();
        $vars['dependentTables']  = $this->getDependentTables();
        $getRelationNameDependent = array();
        $getRelationNameParent    = array();
        $getClassName             = array();
        $getClassNameDependent    = array();


        foreach ($vars['foreignKeysInfo'] as $key) {
            $getRelationNameParent[$key['key_name']]            = $this->_getRelationName($key, 'parent');
            $getClassName[$key['key_name']]['foreign_tbl_name'] = $this->_getClassName($key['foreign_tbl_name']);
            $getClassName[$key['key_name']]['column_name']      = $this->_getClassName($key['column_name']);
        }
        foreach ($vars['dependentTables'] as $key) {
            $getRelationNameDependent[$key['key_name']]                  = $this->_getRelationName($key, 'dependent');
            $getClassNameDependent[$key['key_name']]['foreign_tbl_name'] = $this->_getClassName($key['foreign_tbl_name']);
        }
        
        $vars['relationNameDependent'] = $getRelationNameDependent;
        $vars['relationNameParent']    = $getRelationNameParent;
        $vars['className']             = $getClassName;
        $vars['classNameDependent']    = $getClassNameDependent;
        
        $entity     = new Entity();
        $entity->setData($vars);
        $entityFile = $this->getLocation() . DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Entity.php";

        $manager = new Manager();
        $manager->setData($vars);
        if (isset($vars['_config']['overrideTableGateway'])) {
            $manager
                    ->setTableGatewayClass($vars['_config']['overrideTableGateway']['className'])
                    ->setUseTableGatewayClass(
                            $vars['_config']['overrideTableGateway']['namespace'] . '\\' . $vars['_config']['overrideTableGateway']['className']
            );
        }
        $managerFile = $this->getLocation() . DIRECTORY_SEPARATOR . "Table" . DIRECTORY_SEPARATOR . "Manager.php";

        $entityItem     = new EntityItem();
        $entityItem->setData($vars);
        $entityItemFile = $this->getLocation() . DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . $this->_className . ".php";
        
        $entityManager     = new EntityManager();
        $entityManager->setData($vars);
        $entityManagerFile = $this->getLocation() . DIRECTORY_SEPARATOR . "Table" . DIRECTORY_SEPARATOR . $this->_className . ".php";
        
        if (!file_put_contents($entityFile, $entity->generate()))
            die("Error: could not write Entity file $entityFile.");
        if (!file_put_contents($managerFile, $manager->generate()))
            die("Error: could not write Manager file $managerFile.");
        if (!file_put_contents($entityItemFile, $entityItem->generate()))
            die("Error: could not write model file $entityItemFile.");
        if (!file_put_contents($entityManagerFile, $entityManager->generate()))
            die("Error: could not write model file $entityManagerFile.");
        return true;
    }

}
