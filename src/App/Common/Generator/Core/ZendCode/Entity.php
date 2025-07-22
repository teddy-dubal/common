<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Generator\Core\ZendCode;

use App\Common\Generator\Core\ZendCode\AbstractGenerator;
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
class Entity extends AbstractGenerator
{

    private function getProperties()
    {
        $classProperties = [];
        $properties      = [
            ['name' => 'primary_key'],
            ['name' => '_columnsList'],
            ['name' => '_parentList'],
            ['name' => '_dependentList'],
        ];
        foreach ($properties as $column) {
            $classProperties[] = (new PropertyGenerator($column['name'], null, PropertyGenerator::FLAG_PROTECTED
            ))->setDocBlock((new DocBlockGenerator())
                    ->setShortDescription($column['name'])
                    ->setLongDescription('')
                    ->setTags([
                        new GenericTag('var', $column['name']),
                    ]));
        }
        return $classProperties;
    }

    public function getClassArrayRepresentation()
    {
        $data = $this->getData();
        // var_dump((new DocBlockGenerator())
        //                 ->setShortDescription('Converts database column name to PHP setter/getter function name')
        //                 ->setLongDescription('')
        //                 ->setTags([
        //                     new ParamTag('thevar', ['string'], 'Column name'),
        //                     new ReturnTag([
        //                         'datatype' => 'self',
        //                     ]),
        //                 ]));exit;
        return [
            'name'          => 'Entity',
            'namespacename' => $data['_namespace'] . '\Entity',
            'flags'         => ClassGenerator::FLAG_ABSTRACT,
            'docblock'      =>
            (new DocBlockGenerator())
                ->setShortDescription('Generic Entity Class')
                ->setLongDescription('')
                ->setTags([
                    new GenericTag('author', $data['_author']),
                    new GenericTag('license', $data['_license']),
                    new GenericTag('package', $data['_namespace']),
                    new GenericTag('copyright', $data['_copyright']),
                ])
            ,
            'properties'    => $this->getProperties(),
            'methods'       => [
                [
                    'name'       => 'setColumnsList',
                    'parameters' => [
                        new ParameterGenerator('data',
                            'array')
                        ,
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->_columnsList = $data;' . "\n" . 'return $this;',
                    'docblock'   =>
                    (new DocBlockGenerator())
                        ->setShortDescription('Set the list of columns associated with this model')
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('data', ['array'], 'array of field names'),
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ])
                    ,
                ],
                [
                    'name'       => 'getColumnsList',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->_columnsList;',
                    'docblock'   =>
                    (new DocBlockGenerator())
                        ->setShortDescription('eturns columns list array')
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag([
                                'datatype' => 'array',
                            ]),
                        ])
                    ,
                ],
                [
                    'name'       => 'setParentList',
                    'parameters' => [
                        new ParameterGenerator('data', 'array')
                        ,
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->_parentList = $data;' . "\n" . 'return $this;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Set the list of relationships associated with this model')
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('data', ['array'], 'Array of relationship'),
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'getParentList',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->_parentList;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Returns relationship list array')
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag([
                                'datatype' => 'array',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'setDependentList',
                    'parameters' => [
                        new ParameterGenerator('data', 'array')
                        ,
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->_dependentList = $data;' . "\n" . 'return $this;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Set the list of relationships associated with this model')
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('data', ['array'], 'array of relationships'),
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'getDependentList',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->_dependentList;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Returns relationship list array')
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag([
                                'datatype' => 'array',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'columnNameToVar',
                    'parameters' => ['column'],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'if (! isset($this->_columnsList[$column])) {' . "\n" .
                    '    throw new \Exception("column \'$column\' not found!");' . "\n" .
                    '}' . "\n" .
                    'return $this->_columnsList[$column];',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Converts database column name to php setter/getter function name')
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('column', ['string'], 'Column name'),
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'varNameToColumn',
                    'parameters' => ['thevar'],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    'foreach ($this->_columnsList as $column => $var) {' . "\n" .
                    '    if ($var == $thevar) {' . "\n" .
                    '        return $column;' . "\n" .
                    '    }' . "\n" .
                    '}' . "\n" .
                    'return null;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Converts database column name to PHP setter/getter function name')
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('thevar', ['string'], 'Column name'),
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'setOptions',
                    'parameters' => [
                        new ParameterGenerator('options', 'array')
                        ,
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    '$this->exchangeArray($options);' . "\n" .
                    'return $this;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Array of options/values to be set for this model.')
                        ->setLongDescription('Options without a matching method are ignored.')
                        ->setTags([
                            new ParamTag('options', ['array'], 'array of Options'),
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'exchangeArray',
                    'parameters' => [
                        new ParameterGenerator('options', 'array')
                        ,
                    ],
                    'flags'      => MethodGenerator::FLAG_ABSTRACT,
                    'body'       => '',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Array of options/values to be set for this model.')
                        ->setShortDescription('Options without a matching method are ignored.')
                        ->setTags([
                            new ParamTag('options', ['array'], 'array of Options'),
                            new ReturnTag([
                                'datatype' => 'self',
                            ]),
                        ]),
                ],
                [
                    'name'       => 'toArray',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_ABSTRACT,
                    'body'       => '',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Returns an array, keys are the field names.')
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag(['datatype' => 'array']),
                        ]),
                ],
                [
                    'name'       => 'getPrimaryKey',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return  $this->primary_key;',
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription('Returns primary key.')
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag(['datatype' => 'array|string']),
                        ]),
                ],
            ],
        ];
    }

}
