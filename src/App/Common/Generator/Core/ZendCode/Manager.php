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
class Manager extends AbstractGenerator
{

    private $useTableGatewayClass = 'Laminas\Db\TableGateway\AbstractTableGateway';
    private $data;

    public function getClassArrayRepresentation()
    {
        $this->data = $this->getData();

        return [
            'name'          => 'Manager',
            'namespacename' => $this->data['_namespace'] . '\Table',
            'extendedclass' => $this->useTableGatewayClass,
            'flags'         => ClassGenerator::FLAG_ABSTRACT,
            'docblock'      => DocBlockGenerator::fromArray(
                [
                    'shortDescription' => 'Application Model DbTables',
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
                ['entity', null, PropertyGenerator::FLAG_PROTECTED],
                ['container', null, PropertyGenerator::FLAG_PROTECTED],
                ['debug', false, PropertyGenerator::FLAG_PROTECTED],
                PropertyGenerator::fromArray(
                    [
                        'name'         => 'wasInTransaction',
                        'defaultvalue' => false,
                        'flags'        => PropertyGenerator::FLAG_PROTECTED,
                        'docblock'     => DocBlockGenerator::fromArray(
                            [
                                'shortDescription' => 'True if we were already in a transaction when try to start a new one',
                                'longDescription'  => '',
                                'tags'             => [
                                    new GenericTag('var', 'bool'),
                                ],
                            ]
                        ),
                    ]
                ),
            ],
            'methods'       => [
                [
                    'name'       => '__construct',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'adapter',
                                //'type' => 'Adapter',
                            ]
                        ),
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'entity',
                                'type' => $this->data['_namespace'] . '\Entity\Entity',
                            ]
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    '$this->adapter = $adapter;' . "\n" .
                    '$this->entity = $entity;' . "\n" .
                    '$this->featureSet = new Feature\FeatureSet();' . "\n" .
                    '$this->initialize();',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Constructor',
                            'longDescription'  => null,
                            'tags'             => [
                                new ParamTag('adapter', ['Adapter']),
                                new ParamTag('entity', [$this->data['_namespace'] . '\Entity\Entity']),
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
                    'name'       => 'getPrimaryKeyName',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->id;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => '',
                            'longDescription'  => null,
                            'tags'             => [
                                new ReturnTag([
                                    'datatype' => 'array|string',
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
                            'shortDescription' => '',
                            'longDescription'  => null,
                            'tags'             => [
                                new ReturnTag([
                                    'datatype' => 'array|string',
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
                            'type'         => 'array',
                        ]),
                        ParameterGenerator::fromArray([
                            'name'         => 'order',
                            'defaultvalue' => null,
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'int',
                            'name'         => 'limit',
                            'defaultvalue' => null,
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'int',
                            'name'         => 'offset',
                            'defaultvalue' => null,
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'bool',
                            'name'         => 'toEntity',
                            'defaultvalue' => false,
                        ]),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$select = $this->sql->select();' . PHP_EOL .
                    '$select->where($criteria);' . PHP_EOL .
                    'if ($order) {' . PHP_EOL .
                    '      $select->order($order);' . PHP_EOL .
                    '}' . PHP_EOL .
                    'if ($limit) {' . PHP_EOL .
                    '      $select->limit($limit);' . PHP_EOL .
                    '}' . PHP_EOL .
                    'if ($offset) {' . PHP_EOL .
                    '      $select->offset($offset);' . PHP_EOL .
                    '}' . PHP_EOL .
                    '$result = $this->selectWith($select)->toArray();' . PHP_EOL .
                    'if ($toEntity) {' . PHP_EOL .
                    '    foreach($result as &$v){' . PHP_EOL .
                    '        $entity =  clone $this->entity;' . PHP_EOL .
                    '        $v = $entity->exchangeArray($v);' . PHP_EOL .
                    '    }' . PHP_EOL .
                    '}' . PHP_EOL .
                    'return $result;' . PHP_EOL,
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Find by criteria',
                            'longDescription'  => null,
                            'tags'             => [
                                new ParamTag('criteria', ['array'], 'Search criteria'),
                                new ParamTag('order', ['string'], 'sorting option'),
                                new ParamTag('limit', ['int'], 'limit option'),
                                new ParamTag('offset', ['int'], 'offset option'),
                                new ParamTag('toEntity', ['boolean'], 'return entity result'),
                                new ReturnTag(['array'], ''),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'getResult',
                    'parameters' => [
                        ParameterGenerator::fromArray([
                            'name' => 'columns',
                            'type' => 'array',
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'array',
                            'name'         => 'join',
                            'defaultvalue' => [],
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'array',
                            'name'         => 'where',
                            'defaultvalue' => [],
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'array',
                            'name'         => 'orderBy',
                            'defaultvalue' => [],
                        ]),
                        ParameterGenerator::fromArray([
                            'name'         => 'groupBy',
                            'defaultvalue' => [],
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'array',
                            'name'         => 'having',
                            'defaultvalue' => [],
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'int',
                            'name'         => 'limit',
                            'defaultvalue' => null,
                        ]),
                        ParameterGenerator::fromArray([
                            'type'         => 'int',
                            'name'         => 'offset',
                            'defaultvalue' => null,
                        ]),
                    ],
                    'flags'      => MethodGenerator::FLAG_PROTECTED,
                    'body'       => <<<'BODY'
$select = $this->sql->select();
        $select->columns($columns);
        foreach ($join as $j) {
            $select->join($j['name'], $j['on'], $j['columns'], $j['type']);
        }
        foreach ($where as $w) {
            $select->where($w);
        }
        foreach ($orderBy as $o) {
            $select->order($o);
        }
        foreach ($groupBy as $g) {
            $select->group($g);
        }
        foreach ($having as $h) {
            $select->having($h);
        }
        if ($limit) {
            $select->limit($limit);
        }
        if ($offset) {
            $select->offset($offset);
        }
        return $this->selectWith($select)->toArray();
BODY
                    ,
                    'docblock'   =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>'Manage params of sql request and return results',
                            'longDescription' =>null,
                            'tags'            =>[
                                new ParamTag('columns',['array'],''),
                                new ParamTag('join',['array'],''),
                                new ParamTag('where',['array'],''),
                                new ParamTag('orderBy',['array'],''),
                                new ParamTag('groupBy',['array'],''),
                                new ParamTag('having',['array'],''),
                                new ParamTag('limit',['int'],''),
                                new ParamTag('offset',['int'],''),
                                new ReturnTag(['array','null'],'Found results'),
                            ],
                        ]
                    )
                ],
                [
                    'name'      =>'countBy',
                    'parameters'=>[
                        ParameterGenerator::fromArray([
                            'name'        =>'criteria',
                            'defaultvalue'=>[],
                            'type'        =>'array',
                        ]),
                    ],
                    'flags'     =>MethodGenerator::FLAG_PUBLIC,
                    'body'      =>'$r = $this->sql->select()->columns(array("count" => new Expression("count(*)")))->where($criteria);'.PHP_EOL.
                    'return  (int)current($this->selectWith($r)->toArray())["count"];'.PHP_EOL,
                    'docblock'  =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>'Count by criteria',
                            'longDescription' =>null,
                            'tags'            =>[
                                new ParamTag('criteria',['array'],'Criteria'),
                                new ReturnTag(['int'],''),
                            ],
                        ]
                    ),
                ],
                [
                    'name'      =>'exists',
                    'parameters'=>[
                        ParameterGenerator::fromArray([
                            'name'        =>'criteria',
                            'defaultvalue'=>[],
                            'type'        =>'array',
                        ]),
                    ],
                    'flags'     =>MethodGenerator::FLAG_PUBLIC,
                    'body'      =>'$r = $this->sql->select()->where($criteria);'.PHP_EOL.
                    '$r->limit(1);'.PHP_EOL.
                    '$result = $this->selectWith($r);'.PHP_EOL.PHP_EOL.
                    'return $result->count() === 1;',
                    'docblock'  =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>'Is a least one row exists with criteria',
                            'longDescription' =>null,
                            'tags'            =>[
                                new ParamTag('criteria',['array'],'Criteria'),
                                new ReturnTag(['bool'],''),
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
                        ),
                        ParameterGenerator::fromArray([
                            'type'        =>'bool',
                            'name'        =>'useTransaction',
                            'defaultValue'=>true,
                        ]),
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
                    'name'      =>'beginTransaction',
                    'parameters'=>[],
                    'flags'     =>MethodGenerator::FLAG_PUBLIC,
                    'body'      =><<<'BODY'
if ($this->adapter->getDriver()->getConnection()->inTransaction()) {
    $this->wasInTransaction = true;

    return;
}
$this->adapter->getDriver()->getConnection()->beginTransaction();
BODY
                    ,
                    'docblock'  =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>'Begin a transaction',
                            'longDescription' =>null,
                            'tags'            =>[],
                        ]
                    )
                ],
                [
                    'name'      =>'rollback',
                    'parameters'=>[],
                    'flags'     =>MethodGenerator::FLAG_PUBLIC,
                    'body'      =><<<'BODY'
if ($this->wasInTransaction) {
    throw new \Exception('Inside transaction rollback call');
}
$this->adapter->getDriver()->getConnection()->rollback();
BODY
                    ,
                    'docblock'  =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>'Rollback a transaction',
                            'longDescription' =>null,
                            'tags'            =>[],
                        ]
                    )
                ],
                [
                    'name'      =>'commit',
                    'parameters'=>[],
                    'flags'     =>MethodGenerator::FLAG_PUBLIC,
                    'body'      =><<<'BODY'
if (!$this->wasInTransaction) {
    $this->adapter->getDriver()->getConnection()->commit();
}
BODY
                    ,
                    'docblock'  =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>' Commit a transaction',
                            'longDescription' =>null,
                            'tags'            =>[],
                        ]
                    )
                ],
                [
                    'name'      =>'selectWith',
                    'parameters'=>[
                        ParameterGenerator::fromArray([
                            'name'=>'select',
                            'type'=>'Laminas\Db\Sql\Select',
                        ]),
                    ],
                    'flags'     =>MethodGenerator::FLAG_PUBLIC,
                    'body'      =><<<'BODY'
   if ($this->debug) {
    !isset($this->getContainer()['logger']) ? : $this->getContainer()['logger']->debug(__CLASS__ . '|' . __FUNCTION__, array(
    'sql' => $select->getSqlString($this->getAdapter()->getPlatform())));
    }
    try {
    return parent::selectWith($select);
    } catch(\Exception $e){
    !isset($this->getContainer()['logger']) ? : $this->getContainer()['logger']->error(__CLASS__ . '|' . __FUNCTION__, array(
    'error' => $e->getMessage()));
    }
BODY
                    ,
                    'docblock'  =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>' @see Laminas\Db\TableGateway\AbstractTableGateway::selectWith',
                            'longDescription' =>null,
                            'tags'            =>[
                                new ParamTag('select',['Laminas\Db\Sql\Select']),
                                new ReturnTag([
                                    'datatype'=>'ResultSet',
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
                        ParameterGenerator::fromArray([
                            'name'        =>'order',
                            'defaultvalue'=>null,
                        ]),
                    ],
                    'flags'     =>MethodGenerator::FLAG_PUBLIC,
                    'body'      =>'return current($this->findBy($criteria,$order,1));',
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
                [
                    'name'      =>'findOneEntityBy',
                    'parameters'=>[
                        ParameterGenerator::fromArray([
                            'name'        =>'criteria',
                            'defaultvalue'=>[],
                            'type'        =>'array',
                        ]),
                        ParameterGenerator::fromArray([
                            'name'        =>'order',
                            'defaultvalue'=>null,
                        ]),
                    ],
                    'flags'     =>MethodGenerator::FLAG_PUBLIC,
                    'body'      =>'return current($this->findBy($criteria,$order,1,null,true));',
                    'docblock'  =>DocBlockGenerator::fromArray(
                        [
                            'shortDescription'=>'Find Entity one by criteria',
                            'longDescription' =>null,
                            'tags'            =>[
                                new ParamTag('criteria',['array'],'Search criteria'),
                                new ReturnTag(['boolean|Entity'],''),
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
        $class->addUse($this->useTableGatewayClass)
            ->addUse('Laminas\Db\TableGateway\Feature')
            ->addUse('Laminas\Db\Sql\Expression')
            ->addUse($this->data['_namespace'] . '\Entity\Entity')
            ->addUse('Laminas\Db\Adapter\Adapter')
            ->addUse('Laminas\Db\ResultSet\ResultSet');
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
