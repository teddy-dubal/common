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
use Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use Laminas\Code\Generator\DocBlock\Tag\ReturnTag;
use Laminas\Code\Generator\MethodGenerator;
use Laminas\Code\Generator\ParameterGenerator;
use Laminas\Code\Generator\PropertyGenerator;

/**
 * Description of Entity
 *
 * @author teddy
 */
class Document extends AbstractGenerator
{

    private $useTableGatewayClass = 'MongoDB\Collection';
    private $data;

    public function getClassArrayRepresentation()
    {
        $this->data = $this->getData();

        return [
            'name'          => 'Document',
            'namespacename' => $this->data['_namespace'] . '\Document',
            'extendedclass' => $this->useTableGatewayClass,
            'flags'         => ClassGenerator::FLAG_ABSTRACT,
            'docblock'      =>
            (new DocBlockGenerator())
                ->setShortDescription('Application Model MongoDb')
                ->setLongDescription('')
                ->setTags([
                    new GenericTag('package', $this->data['_namespace']),
                    new GenericTag('author', $this->data['_author']),
                    new GenericTag('copyright', $this->data['_copyright']),
                    new GenericTag('license', $this->data['_license']),
                ])
            ,
            'properties'    => [
                ['container', null, PropertyGenerator::FLAG_PROTECTED],
                ['table', null, PropertyGenerator::FLAG_PROTECTED],
                ['debug', false, PropertyGenerator::FLAG_PROTECTED],
            ],
            'methods'       => [
                [
                    'name'       => 'setDebug',
                    'parameters' => [
                        new ParameterGenerator(
                            'debug',
                            'bool',
                            true
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    '$this->debug = $debug;' . PHP_EOL .
                    'return $this;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Set debug mode')
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('debug', ['boolean']),
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'setContainer',
                    'parameters' => [
                        new ParameterGenerator('c',
                            'Pimple\Container'),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    '$this->container = $c;' . PHP_EOL .
                    'return $this;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Inject container')
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('c', ['Pimple\Container']),
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'getContainer',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->container;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('')
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag([
                                'datatype' => '\Pimple\Container',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'getTableName',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->table;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('')
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag([
                                'datatype' => 'array|string',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'setTableName',
                    'parameters' => [
                        new ParameterGenerator('s', 'String'),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->table = $s;' . PHP_EOL .
                    'return $this;',
                    'docblock'   =>
                    (new DocBlockGenerator())
                        ->setShortDescription('Set table name')
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ])
                    ,
                ],
                [
                    'name'       => 'deleteDocument',
                    'parameters' => [
                        new ParameterGenerator('entity', $this->data['_namespace'] . '\Entity\Entity'),
                    ],
                    'flags'      => [MethodGenerator::FLAG_PUBLIC, MethodGenerator::FLAG_ABSTRACT],
                    'body'       => null,
                    'docblock'   =>
                    (new DocBlockGenerator())
                        ->setShortDescription('Converts database column name to php setter/getter function name')
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('entity', [$this->data['_namespace'] . '\Entity\Entity']),
                            new ParamTag('useTransaction', ['boolean']),
                            new ReturnTag([
                                'datatype' => 'int',
                            ]),
                        ])
                    ,
                ],
            ],
        ];
    }

    public function generate()
    {
        $c     = $this->getClassArrayRepresentation();
        $class = new ClassGenerator(
            $c['name'],
            $c['namespacename'],
            $c['flags'],
            $c['extendedclass'],
            [],
            $c['properties'],
            $c['methods'],
            $c['docblock'],
        );
        $class->addUse($this->useTableGatewayClass);
        $this->defineFileInfo($class);
        $fileGenerator = $this->getFileGenerator();

        return $fileGenerator
            ->setClass($class)
            ->generate();
    }

    /**
     * @param string $tableGatewayClass
     *
     * @return self
     */
//    public function setTableGatewayClass($tableGatewayClass) {
    //        $this->tableGatewayClass = $tableGatewayClass;
    //
    //        return $this;
    //    }

    /**
     * @param string $useTableGatewayClass
     *
     * @return self
     */
    public function setUseTableGatewayClass($useTableGatewayClass)
    {
        $this->useTableGatewayClass = $useTableGatewayClass;

        return $this;
    }

}
