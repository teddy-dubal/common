<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Generator\Core\ZendCode;

use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\DocBlock\Tag\GenericTag;
use Laminas\Code\Generator\FileGenerator;
use Laminas\Code\Generator\MethodGenerator;

/**
 * Description of Entity
 *
 * @author teddy
 */
abstract class AbstractGenerator
{

    private $fileGenerator;
    private $classGenerator;
    private $methodGenerator;
    private $data;
    private $entityClass;

    public function __construct()
    {
        $this->fileGenerator   = new FileGenerator();
        $this->classGenerator  = new ClassGenerator();
        $this->methodGenerator = new MethodGenerator();
    }

    public function setData($data = [])
    {
        $this->data = $data;
    }

    public function setNamespace($data)
    {
        $this->data = array_merge($this->data, ['namespace' => $data]);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getFileGenerator()
    {
        return $this->fileGenerator;
    }

    abstract public function getClassArrayRepresentation();

    public function generate()
    {
        $c     = $this->getClassArrayRepresentation();
        $class = new ClassGenerator($c["name"] ?? null,
            $c["namespacename"] ?? null,
            $c["flags"] ?? null,
            $c['extendedclass'] ?? null,
            [],
            $c["properties"] ?? [],
            $c["methods"] ?? [],
            $c["docblock"] ?? null);
        $this->defineFileInfo($class);
        return $this->fileGenerator->setClass($class)->generate();
    }

    /**
     * Fill file level phpdoc
     *
     * @param ClassGenerator $class contained class
     */
    protected function defineFileInfo(ClassGenerator $class)
    {

        $doc = (new DocBlockGenerator())
            ->setShortDescription('Contains ' . $class->getName() . ' class file')
            ->setLongDescription('Generated Automatically.' . PHP_EOL . 'Please do not modify')
            ->setTags([
                new GenericTag('author', $this->data['_author']),
                new GenericTag('license', $this->data['_license']),
                new GenericTag('package', $class->getNamespaceName()),
                new GenericTag('copyright', $this->data['_copyright']),
            ]);
        $this->fileGenerator->setDocBlock($doc);
    }

    /**
     *
     *  removes underscores and capital the letter that was after the underscore
     *  example: 'ab_cd_ef' to 'AbCdEf'
     *
     * @param String $str
     * @return String
     */
    protected function _getCapital($str)
    {
        $temp = '';
        foreach (explode("_", $str) as $part) {
            $temp .= ucfirst($part);
        }
        return $temp;
    }

}
