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
            $classProperties[] = PropertyGenerator::fromArray(
                [
                    'name'     => $column['name'],
                    'flags'    => PropertyGenerator::FLAG_PROTECTED,
                    'docblock' => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => $column['name'],
                            'tags'             => [
                                new GenericTag('var', $column['name']),
                            ],
                        ]
                    ),
                ]
            );

        }
        return $classProperties;
    }

    public function getClassArrayRepresentation()
    {
        $data = $this->getData();
        return [
            'name'          => 'Entity',
            'namespacename' => $data['_namespace'] . '\Entity',
            'flags'         => ClassGenerator::FLAG_ABSTRACT,
            'docblock'      => DocBlockGenerator::fromArray(
                [
                    'shortDescription' => 'Generic Entity Class',
                    'longDescription'  => '',
                    'tags'             => [
                        [
                            'name'        => 'package',
                            'description' => $data['_namespace'],
                        ],
                        [
                            'name'        => 'author',
                            'description' => $data['_author'],
                        ],
                        [
                            'name'        => 'copyright',
                            'description' => $data['_copyright'],
                        ],
                        [
                            'name'        => 'license',
                            'description' => $data['_license'],
                        ],
                    ],
                ]
            ),
            'properties'    => $this->getProperties(),
            'methods'       => [
                [
                    'name'       => 'setColumnsList',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'data',
                                'type' => 'array',
                            ]
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->_columnsList = $data;' . "\n" . 'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Set the list of columns associated with this model',
                            'longDescription'  => '',
                            'tags'             => [
                                new ParamTag('data', ['array'], 'array of field names'),
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'getColumnsList',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->_columnsList;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Returns columns list array',
                            'longDescription'  => '',
                            'tags'             => [
                                new ReturnTag([
                                    'datatype' => 'array',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'setParentList',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'data',
                                'type' => 'array',
                            ]
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->_parentList = $data;' . "\n" . 'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Set the list of relationships associated with this model',
                            'longDescription'  => '',
                            'tags'             => [
                                new ParamTag('data', ['array'], 'Array of relationship'),
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'getParentList',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->_parentList;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Returns relationship list array',
                            'longDescription'  => '',
                            'tags'             => [
                                new ReturnTag([
                                    'datatype' => 'array',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'setDependentList',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'data',
                                'type' => 'array',
                            ]
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->_dependentList = $data;' . "\n" . 'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Set the list of relationships associated with this model',
                            'longDescription'  => '',
                            'tags'             => [
                                new ParamTag('data', ['array'], 'array of relationships'),
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'getDependentList',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->_dependentList;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Returns relationship list array',
                            'longDescription'  => '',
                            'tags'             => [
                                new ReturnTag([
                                    'datatype' => 'array',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'columnNameToVar',
                    'parameters' => ['column'],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'if (! isset($this->_columnsList[$column])) {' . "\n" .
                    '    throw new \Exception("column \'$column\' not found!");' . "\n" .
                    '}' . "\n" .
                    'return $this->_columnsList[$column];',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Converts database column name to php setter/getter function name',
                            'longDescription'  => '',
                            'tags'             => [
                                new ParamTag('column', ['string'], 'Column name'),
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
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
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Converts database column name to PHP setter/getter function name',
                            'longDescription'  => '',
                            'tags'             => [
                                new ParamTag('thevar', ['string'], 'Column name'),
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'setOptions',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'options',
                                'type' => 'array',
                            ]
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    '$this->exchangeArray($options);' . "\n" .
                    'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Array of options/values to be set for this model.',
                            'longDescription'  => 'Options without a matching method are ignored.',
                            'tags'             => [
                                new ParamTag('options', ['array'], 'array of Options'),
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'exchangeArray',
                    'parameters' => [
                        ParameterGenerator::fromArray(
                            [
                                'name' => 'options',
                                'type' => 'array',
                            ]
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_ABSTRACT,
                    'body'       => '',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Array of options/values to be set for this model.',
                            'longDescription'  => 'Options without a matching method are ignored.',
                            'tags'             => [
                                new ParamTag('options', ['array'], 'array of Options'),
                                new ReturnTag([
                                    'datatype' => 'self',
                                ]),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'toArray',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_ABSTRACT,
                    'body'       => '',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Returns an array, keys are the field names.',
                            'longDescription'  => '',
                            'tags'             => [
                                new ReturnTag(['datatype' => 'array']),
                            ],
                        ]
                    ),
                ],
                [
                    'name'       => 'getPrimaryKey',
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return  $this->primary_key;',
                    'docblock'   => DocBlockGenerator::fromArray(
                        [
                            'shortDescription' => 'Returns primary key.',
                            'longDescription'  => '',
                            'tags'             => [
                                new ReturnTag(['datatype' => 'array|string']),
                            ],
                        ]
                    ),
                ],
            ],
        ];
    }

}
