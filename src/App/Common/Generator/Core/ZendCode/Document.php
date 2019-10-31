<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Generator\Core\ZendCode;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\DocBlock\Tag\GenericTag;
use Zend\Code\Generator\DocBlock\Tag\ParamTag;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

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
                    'longDescription'  => null,
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
                ['debug', false, PropertyGenerator::FLAG_PROTECTED]
            ],
            'methods'       => [
                [
                    'name'       => '__construct',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'container',
                                'type' => '\Pimple\Container',
                            ]
                        )
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    '$this->container = $container;' . PHP_EOL,
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Constructor',
                            'longDescription'  => null,
                            'tags'             => [
                                new ParamTag('adapter', ['\Pimple\Container']),
                            ],
                        ]
                    ),
                ],
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
                    '$this->debug = $debug;' . "\n" .
                    'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Set debug mode',
                            'longDescription'  => null,
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
                    '$this->container = $c;' . "\n" .
                    'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Inject container',
                            'longDescription'  => null,
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
                            'longDescription'  => null,
                            'tags'             => [
                                new ReturnTag([
                                    'datatype' => '\Pimple\Container',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'findBy',
                    'parameters' => [
                        ParameterGenerator::fromArray([
                            'name'         => 'criteria',
                            'defaultvalue' => [],
                            'type'         => 'Array',
                        ]),
                        ParameterGenerator::fromArray([
                            'name'         => 'order',
                            'defaultvalue' => null,
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'int',
                            'name'         => 'limit',
                            'defaultvalue' => 0,
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'int',
                            'name'         => 'offset',
                            'defaultvalue' => 0,
                        ])
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 
                    '$doc = $this->find($criteria,[\'limit\'=>$limit,\'sort\'=>$order,\'skip\'=>$offset]);' . PHP_EOL .
                    '$res = [];' . PHP_EOL .
                    'foreach ($doc as $d) {' . PHP_EOL .
                    '   $t        = json_decode(\MongoDB\BSON\toJSON(\MongoDB\BSON\fromPHP($d)), true);' . PHP_EOL .
                    '   $t[\'_id\'] = $d->_id->__toString();' . PHP_EOL .
                    '   $res[]    = $t;' . PHP_EOL .
                    '}' . PHP_EOL .
                    'return $res;' . PHP_EOL .
                    '' . PHP_EOL,
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Find by criteria',
                            'longDescription'  => null,
                            'tags'             => [
                                new ParamTag('criteria', ['array'], 'Search criteria'),
                                new ParamTag('order', ['string'], 'sorting option'),
                                new ParamTag('limit', ['int'], 'limit option'),
                                new ParamTag('offset', ['int'], 'offset option'),
                                new ReturnTag(['array'], ''),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'findDoc',
                    'parameters' => [
                        ParameterGenerator::fromArray([
                            'name'         => 'id',
                        ])
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 
                    '$return = parent::findOne([\'_id\' => new \MongoDB\BSON\ObjectId($id)]);' . PHP_EOL .
                    'if ($return) {' . PHP_EOL .
                    '   $return        = $return->getArrayCopy();' . PHP_EOL .
                    '   $return[\'_id\'] = $return[\'_id\']->__toString();' . PHP_EOL .
                    '}' . PHP_EOL .
                    'return $return;' . PHP_EOL .
                    '' . PHP_EOL,
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Find by criteria',
                            'longDescription'  => null,
                            'tags'             => [
                                new ParamTag('id', ['mixed'], 'Search by primary key'),
                                new ReturnTag(['array'], ''),
                            ],
                        ]
                    ),
                ],
                [
                    'name'      =>'deleteEntity',
                    'parameters'=>[
                        ParameterGenerator::fromArray(
                            [
                                'name'=>'entity',
                                'type'=>$this->data['_namespace'].'\Entity\Entity',
                            ]
                        )
                    ],
                    'flags'     =>[MethodGenerator::FLAG_PUBLIC,MethodGenerator::FLAG_ABSTRACT],
                    'body'      =>null,
                    'docblock'  =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>'Converts database column name to php setter/getter function name',
                            'longDescription' =>null,
                            'tags'            =>[
                                new ParamTag('entity',[$this->data['_namespace'].'\Entity\Entity']),
                                new ParamTag('useTransaction',['boolean']),
                                new ReturnTag([
                                    'datatype'=>'int',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'      =>'findOneBy',
                    'parameters'=>[
                        ParameterGenerator::fromArray([
                            'name'        =>'criteria',
                            'defaultvalue'=>[],
                            'type'        =>'array',
                        ]),
                    ],
                    'flags'     =>MethodGenerator::FLAG_PUBLIC,
                    'body'      =>'return current($this->findBy($criteria,[],1));',
                    'docblock'  =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>'Find one by criteria',
                            'longDescription' =>null,
                            'tags'            =>[
                                new ParamTag('criteria',['array'],'Search criteria'),
                                new ReturnTag(['array|boolean'],''),
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
