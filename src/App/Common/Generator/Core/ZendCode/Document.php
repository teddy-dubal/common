<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Generator\Core\ZendCode;

use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\DocBlockGenerator;
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
            'docblock'      => DocBlockGenerator::fromArray(
                [
                    'shortDescription' => 'Application Model MongoDb',
                    'longDescription'  => '',
                    'tags'             => [
                        [
                            'name'        => 'package',
                            'description' => $this->data['_namespace'],
                        ],
                        [
                            'name'        => 'author',
                            'description' => $this->data['_author'],
                        ],
                        [
                            'name'        => 'copyright',
                            'description' => $this->data['_copyright'],
                        ],
                        [
                            'name'        => 'license',
                            'description' => $this->data['_license'],
                        ],
                    ],
                ]
            ),
            'properties'    => [
                ['container', null, PropertyGenerator::FLAG_PROTECTED],
                ['table', null, PropertyGenerator::FLAG_PROTECTED],
                ['debug', false, PropertyGenerator::FLAG_PROTECTED],
            ],
            'methods'       => [
                [
                    'name'       => 'setDebug',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'type'         => 'bool',
                                'name'         => 'debug',
                                'defaultvalue' => true,
                            ]
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    '$this->debug = $debug;' . PHP_EOL .
                    'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Set debug mode',
                            'longDescription'  => '',
                            'tags'             => [
                                new ParamTag('debug', ['boolean']),
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'setContainer',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'c',
                                'type' => 'Pimple\Container',
                            ]
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    '$this->container = $c;' . PHP_EOL .
                    'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Inject container',
                            'longDescription'  => '',
                            'tags'             => [
                                new ParamTag('c', ['Pimple\Container']),
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'getContainer',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->container;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => '',
                            'longDescription'  => '',
                            'tags'             => [
                                new ReturnTag([
                                    'datatype' => '\Pimple\Container',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'getTableName',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->table;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Get table name',
                            'longDescription'  => '',
                            'tags'             => [
                                new ReturnTag([
                                    'datatype' => 'String',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'setTableName',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 's',
                                'type' => 'String',
                            ]
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->table = $s;' . PHP_EOL .
                    'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Set table name',
                            'longDescription'  => '',
                            'tags'             => [
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'deleteDocument',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'entity',
                                'type' => $this->data['_namespace'] . '\Entity\Entity',
                            ]
                        ),
                    ],
                    'flags'      => [MethodGenerator::FLAG_PUBLIC, MethodGenerator::FLAG_ABSTRACT],
                    'body'       => null,
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Converts database column name to php setter/getter function name',
                            'longDescription'  => '',
                            'tags'             => [
                                new ParamTag('entity', [$this->data['_namespace'] . '\Entity\Entity']),
                                new ParamTag('useTransaction', ['boolean']),
                                new ReturnTag([
                                    'datatype' => 'int',
                                ]),
                            ],
                        ]
                    ),
                ],
            ],
        ];
    }

    public function generate()
    {
        $class = ClassGenerator::fromArray($this->getClassArrayRepresentation());
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
