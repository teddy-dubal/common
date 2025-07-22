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
    private $useTableGatewayClass = "Laminas\Db\TableGateway\AbstractTableGateway";
    private $data;

    public function getClassArrayRepresentation()
    {
        $this->data = $this->getData();

        return [
            "name" => "Manager",
            "namespacename" => $this->data["_namespace"] . "\Table",
            "extendedclass" => $this->useTableGatewayClass,
            "flags" => ClassGenerator::FLAG_ABSTRACT,
            "docblock" => (new DocBlockGenerator())
                ->setShortDescription("Application Model DbTables")
                ->setLongDescription("")
                ->setTags([
                    new GenericTag("package", $this->data["_namespace"]),
                    new GenericTag("author", $this->data["_author"]),
                    new GenericTag("copyright", $this->data["_copyright"]),
                    new GenericTag("license", $this->data["_license"]),
                ]),
            "properties" => [
                ["entity", null, PropertyGenerator::FLAG_PROTECTED],
                ["container", null, PropertyGenerator::FLAG_PROTECTED],
                ["debug", false, PropertyGenerator::FLAG_PROTECTED],
                (new PropertyGenerator(
                    "wasInTransaction",
                    false,
                    PropertyGenerator::FLAG_PROTECTED
                ))->setDocBlock(
                    (new DocBlockGenerator())
                        ->setShortDescription(
                            "True if we were already in a transaction when try to start a new one"
                        )
                        ->setLongDescription("")
                        ->setTags([new GenericTag("var", "bool")])
                ),
            ],
            "methods" => [
                [
                    "name" => "__construct",
                    "parameters" => [
                        new ParameterGenerator("adapter"),
                        new ParameterGenerator(
                            "entity",
                            $this->data["_namespace"] . "\Entity\Entity"
                        ),
                    ],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
$this->adapter = $adapter;
$this->entity = $entity;
$this->featureSet = new Feature\FeatureSet();
$this->initialize();
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Constructor")
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag("adapter", ["Adapter"]),
                            new ParamTag("entity", [
                                $this->data["_namespace"] . "\Entity\Entity",
                            ]),
                        ]),
                ],
                [
                    "name" => "setDebug",
                    "parameters" => [
                        new ParameterGenerator("debug", "bool", true),
                    ],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
$this->debug = $debug;
return $this;
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Set debug mode")
                        ->setLongDescription("")
                        ->setTags([
                            new GenericTag("debug", "boolean"),
                            new ReturnTag([
                                "datatype" => "self",
                            ]),
                        ]),
                ],
                [
                    "name" => "setContainer",
                    "parameters" => [
                        new ParameterGenerator("c", "Pimple\Container"),
                    ],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
$this->container = $c;
return $this;
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Inject container")
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag("c", ["Pimple\Container"]),
                            new ReturnTag([
                                "datatype" => "self",
                            ]),
                        ]),
                ],
                [
                    "name" => "getContainer",
                    "parameters" => [],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => 'return $this->container;',
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("")
                        ->setLongDescription("")
                        ->setTags([
                            new ReturnTag([
                                "datatype" => "\Pimple\Container",
                            ]),
                        ]),
                ],
                [
                    "name" => "getPrimaryKeyName",
                    "parameters" => [],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => 'return $this->id;',
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("")
                        ->setLongDescription("")
                        ->setTags([
                            new ReturnTag([
                                "datatype" => "array|string",
                            ]),
                        ]),
                ],
                [
                    "name" => "getTableName",
                    "parameters" => [],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => 'return $this->table;',
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("")
                        ->setLongDescription("")
                        ->setTags([
                            new ReturnTag([
                                "datatype" => "array|string",
                            ]),
                        ]),
                ],
                [
                    "name" => "findBy",
                    "parameters" => [
                        new ParameterGenerator("criteria", "array", []),
                        (new ParameterGenerator("order","string|null"))->setDefaultValue(null),
                        new ParameterGenerator("limit", "?int",0),
                        new ParameterGenerator("offset", "?int", 0),
                        new ParameterGenerator("toEntity", "bool", false),
                    ],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
$select = $this->sql->select();
$select->where($criteria);
if ($order) { 
        $select->order($order); 
} 
if ($limit) { 
        $select->limit($limit); 
} 
if ($offset) { 
        $select->offset($offset); 
} 
$result = $this->selectWith($select)->toArray(); 
if ($toEntity) { 
    foreach($result as &$v){ 
        $entity =  clone $this->entity; 
        $v = $entity->exchangeArray($v); 
    } 
} 
return $result;
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Find by criteria")
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag(
                                "criteria",
                                ["array"],
                                "Search criteria"
                            ),
                            new ParamTag("order", ["string"], "sorting option"),
                            new ParamTag("limit", ["int"], "limit option"),
                            new ParamTag("offset", ["int"], "offset option"),
                            new ParamTag(
                                "toEntity",
                                ["boolean"],
                                "return entity result"
                            ),
                            new ReturnTag(["array"], ""),
                        ]),
                ],
                [
                    "name" => "getResult",
                    "parameters" => [
                        new ParameterGenerator("columns", "array"),
                        new ParameterGenerator("join", "array", []),
                        new ParameterGenerator("where", "array", []),
                        new ParameterGenerator("orderBy", "array", []),
                        new ParameterGenerator("groupBy", "array", []),
                        new ParameterGenerator("having", "array", []),
                        new ParameterGenerator("limit", "?int", 0),
                        new ParameterGenerator("offset", "?int", 0),
                    ],
                    "flags" => MethodGenerator::FLAG_PROTECTED,
                    "body" => <<<'BODY'
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
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription(
                            "Manage params of sql request and return results"
                        )
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag("columns", ["array"], ""),
                            new ParamTag("join", ["array"], ""),
                            new ParamTag("where", ["array"], ""),
                            new ParamTag("orderBy", ["array"], ""),
                            new ParamTag("groupBy", ["array"], ""),
                            new ParamTag("having", ["array"], ""),
                            new ParamTag("limit", ["int"], ""),
                            new ParamTag("offset", ["int"], ""),
                            new ReturnTag(["array", "null"], "Found results"),
                        ]),
                ],
                [
                    "name" => "countBy",
                    "parameters" => [
                        new ParameterGenerator("criteria", "array", []),
                    ],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
$r = $this->sql->select()->columns(array("count" => new Expression("count(*)")))->where($criteria);
return  (int)current($this->selectWith($r)->toArray())["count"];
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Count by criteria")
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag("criteria", ["array"], "Criteria"),
                            new ReturnTag(["int"], ""),
                        ]),
                ],
                [
                    "name" => "exists",
                    "parameters" => [
                        new ParameterGenerator("criteria", "array", []),
                    ],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
$r = $this->sql->select()->where($criteria);
$r->limit(1);
$result = $this->selectWith($r);
return $result->count() === 1;
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription(
                            "Is a least one row exists with criteria"
                        )
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag("criteria", ["array"], "Criteria"),
                            new ReturnTag(["bool"], ""),
                        ]),
                ],
                [
                    "name" => "deleteEntity",
                    "parameters" => [
                        new ParameterGenerator(
                            "entity",
                            $this->data["_namespace"] . "\Entity\Entity"
                        ),
                        new ParameterGenerator("useTransaction", "bool", true),
                    ],
                    "flags" => [
                        MethodGenerator::FLAG_PUBLIC,
                        MethodGenerator::FLAG_ABSTRACT,
                    ],
                    "body" => null,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription(
                            "Converts database column name to php setter/getter function name"
                        )
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag("entity", [
                                $this->data["_namespace"] . "\Entity\Entity",
                            ]),
                            new ParamTag("useTransaction", ["boolean"]),
                            new ReturnTag([
                                "datatype" => "int",
                            ]),
                        ]),
                ],
                [
                    "name" => "beginTransaction",
                    "parameters" => [],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
if ($this->adapter->getDriver()->getConnection()->inTransaction()) {
    $this->wasInTransaction = true;
    return;
}
$this->adapter->getDriver()->getConnection()->beginTransaction();
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Begin a transaction")
                        ->setLongDescription("")
                        ->setTags([]),
                ],
                [
                    "name" => "rollback",
                    "parameters" => [],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
if ($this->wasInTransaction) {
    throw new \Exception('Inside transaction rollback call');
}
$this->adapter->getDriver()->getConnection()->rollback();
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Rollback a transaction")
                        ->setLongDescription("")
                        ->setTags([]),
                ],
                [
                    "name" => "commit",
                    "parameters" => [],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
if (!$this->wasInTransaction) {
    $this->adapter->getDriver()->getConnection()->commit();
}
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Commit a transaction")
                        ->setLongDescription("")
                        ->setTags([]),
                ],
                [
                    "name" => "selectWith",
                    "parameters" => [
                        new ParameterGenerator(
                            "select",
                            "Laminas\Db\Sql\Select"
                        ),
                    ],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" => <<<'BODY'
   if ($this->debug) {
    !isset($this->getContainer()['logger']) ? : $this->getContainer()['logger']->debug('debug.sql', ['extra'=>[
        'class'=> __CLASS__,
        'function'=> __FUNCTION__,
        'sql' => $select->getSqlString($this->getAdapter()->getPlatform())
    ]]);
    }
    try {
    return parent::selectWith($select);
    } catch(\Exception $e){
    !isset($this->getContainer()['logger']) ? : $this->getContainer()['logger']->error($e->getMessage(), ['extra'=>[
        'class'=> __CLASS__,
        'function'=> __FUNCTION__,
        'sql' => $select->getSqlString($this->getAdapter()->getPlatform())
    ]]);
    return (new ResultSet())->initialize([]);
    }
BODY
                    ,
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription(
                            "@see Laminas\Db\TableGateway\AbstractTableGateway::selectWith"
                        )
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag("select", ["Laminas\Db\Sql\Select"]),
                            new ReturnTag([
                                "datatype" => "ResultSet",
                            ]),
                        ]),
                ],
                [
                    "name" => "findOneBy",
                    "parameters" => [
                        new ParameterGenerator("criteria", "array", []),
                        (new ParameterGenerator("order", "string|null",[]))->setDefaultValue(null),
                    ],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" =>
                        'return current($this->findBy($criteria,$order,1));',
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Find one by criteria")
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag(
                                "criteria",
                                ["array"],
                                "Search criteria"
                            ),
                            new ReturnTag(["array|boolean"], ""),
                        ]),
                ],
                [
                    "name" => "findOneEntityBy",
                    "parameters" => [
                        new ParameterGenerator("criteria", "array", []),
                        (new ParameterGenerator("order","string|null"))->setDefaultValue(null),
                    ],
                    "flags" => MethodGenerator::FLAG_PUBLIC,
                    "body" =>
                        'return current($this->findBy($criteria,$order,1,0,true));',
                    "docblock" => (new DocBlockGenerator())
                        ->setShortDescription("Find Entity one by criteria")
                        ->setLongDescription("")
                        ->setTags([
                            new ParamTag(
                                "criteria",
                                ["array"],
                                "Search criteria"
                            ),
                            new ReturnTag(["boolean|Entity"], ""),
                        ]),
                ],
            ],
        ];
    }

    public function generate()
    {
        $c = $this->getClassArrayRepresentation();
        $class = new ClassGenerator(
            $c["name"],
            $c["namespacename"],
            $c["flags"],
            $c['extendedclass'],
            [],
            $c["properties"],
            $c["methods"],
            $c["docblock"]
        );
        $class
            ->addUse($this->useTableGatewayClass)
            ->addUse("Laminas\Db\TableGateway\Feature")
            ->addUse("Laminas\Db\Sql\Expression")
            ->addUse($this->data["_namespace"] . "\Entity\Entity")
            ->addUse("Laminas\Db\Adapter\Adapter")
            ->addUse("Laminas\Db\ResultSet\ResultSet");
        $this->defineFileInfo($class);
        $fileGenerator = $this->getFileGenerator();

        return $fileGenerator->setClass($class)->generate();
    }

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
