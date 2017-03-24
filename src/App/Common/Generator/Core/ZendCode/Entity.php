<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Generator\Core\ZendCode;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\ParamTag;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;

/**
 * Description of Entity
 *
 * @author teddy
 */
class Entity extends AbstractGenerator
{

    public function getClassArrayRepresentation()
    {
        $data = $this->getData();
        return array(
            'name'          => 'Entity',
            'namespacename' => $data['_namespace'] . '\Entity',
            'flags'         => ClassGenerator::FLAG_ABSTRACT,
            'docblock'      => DocBlockGenerator::fromArray(
                    array(
                        'shortDescription' => 'Generic Entity Class',
                        'longDescription'  => null,
                        'tags'             => array(
                            array(
                                'name'        => 'package',
                                'description' => $data['_namespace'],
                            ),
                            array(
                                'name'        => 'author',
                                'description' => $data['_author'],
                            ),
                            array(
                                'name'        => 'copyright',
                                'description' => $data['_copyright'],
                            ),
                            array(
                                'name'        => 'license',
                                'description' => $data['_license'],
                            ),
                        )
                    )
            ),
            'methods'       => array(
                array(
                    'name'       => 'setColumnsList',
                    'parameters' => array(
                        ParameterGenerator::fromArray(
                                array(
                                    'name' => 'data',
                                    'type' => 'array',
                                )
                        )
                    ),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->_columnsList = $data;' . "\n" . 'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Set the list of columns associated with this model',
                                'longDescription'  => null,
                                'tags'             => array(
                                    new ParamTag('data', array('array'), 'array of field names'),
                                    new ReturnTag(array(
                                        'datatype' => 'self',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'getColumnsList',
                    'parameters' => array(),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->_columnsList;',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Returns columns list array',
                                'longDescription'  => null,
                                'tags'             => array(
                                    new ReturnTag(array(
                                        'datatype' => 'array',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'setParentList',
                    'parameters' => array(
                        ParameterGenerator::fromArray(
                                array(
                                    'name' => 'data',
                                    'type' => 'array',
                                )
                        )
                    ),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->_parentList = $data;' . "\n" . 'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Set the list of relationships associated with this model',
                                'longDescription'  => null,
                                'tags'             => array(
                                    new ParamTag('data', array('array'), 'Array of relationship'),
                                    new ReturnTag(array(
                                        'datatype' => 'self',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'getParentList',
                    'parameters' => array(),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->_parentList;',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Returns relationship list array',
                                'longDescription'  => null,
                                'tags'             => array(
                                    new ReturnTag(array(
                                        'datatype' => 'array',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'setDependentList',
                    'parameters' => array(
                        ParameterGenerator::fromArray(
                                array(
                                    'name' => 'data',
                                    'type' => 'array',
                                )
                        )
                    ),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => '$this->_dependentList = $data;' . "\n" . 'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Set the list of relationships associated with this model',
                                'longDescription'  => null,
                                'tags'             => array(
                                    new ParamTag('data', array('array'), 'array of relationships'),
                                    new ReturnTag(array(
                                        'datatype' => 'self',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'getDependentList',
                    'parameters' => array(),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return $this->_dependentList;',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Returns relationship list array',
                                'longDescription'  => null,
                                'tags'             => array(
                                    new ReturnTag(array(
                                        'datatype' => 'array',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'columnNameToVar',
                    'parameters' => array('column'),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'if (! isset($this->_columnsList[$column])) {' . "\n" .
                    '    throw new \Exception("column \'$column\' not found!");' . "\n" .
                    '}' . "\n" .
                    'return $this->_columnsList[$column];',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Converts database column name to php setter/getter function name',
                                'longDescription'  => null,
                                'tags'             => array(
                                    new ParamTag('column', array('string'), 'Column name'),
                                    new ReturnTag(array(
                                        'datatype' => 'self',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'varNameToColumn',
                    'parameters' => array('thevar'),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    'foreach ($this->_columnsList as $column => $var) {' . "\n" .
                    '    if ($var == $thevar) {' . "\n" .
                    '        return $column;' . "\n" .
                    '    }' . "\n" .
                    '}' . "\n" .
                    'return null;',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Converts database column name to PHP setter/getter function name',
                                'longDescription'  => null,
                                'tags'             => array(
                                    new ParamTag('thevar', array('string'), 'Column name'),
                                    new ReturnTag(array(
                                        'datatype' => 'self',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'setOptions',
                    'parameters' => array(
                        ParameterGenerator::fromArray(
                                array(
                                    'name' => 'options',
                                    'type' => 'array',
                                )
                        )
                    ),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       =>
                    '$this->exchangeArray($options);' . "\n" .
                    'return $this;',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Array of options/values to be set for this model.',
                                'longDescription'  => 'Options without a matching method are ignored.',
                                'tags'             => array(
                                    new ParamTag('options', array('array'), 'array of Options'),
                                    new ReturnTag(array(
                                        'datatype' => 'self',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'exchangeArray',
                    'parameters' => array(
                        ParameterGenerator::fromArray(
                                array(
                                    'name' => 'options',
                                    'type' => 'array',
                                )
                        )
                    ),
                    'flags'      => MethodGenerator::FLAG_ABSTRACT,
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Array of options/values to be set for this model.',
                                'longDescription'  => 'Options without a matching method are ignored.',
                                'tags'             => array(
                                    new ParamTag('options', array('array'), 'array of Options'),
                                    new ReturnTag(array(
                                        'datatype' => 'self',
                                            )),
                                )
                            )
                    )
                ),
                array(
                    'name'       => 'toArray',
                    'parameters' => array(),
                    'flags'      => MethodGenerator::FLAG_ABSTRACT,
                    'docblock'   => DocBlockGenerator::fromArray(
                        array(
                            'shortDescription' => 'Returns an array, keys are the field names.',
                            'longDescription'  => null,
                            'tags'             => array(
                                new ReturnTag(array('datatype' => 'array')),
                            )
                        )
                    )
                ),
                array(
                    'name'       => 'getPrimaryKey',
                    'parameters' => array(),
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => 'return  $this->primary_key;',
                    'docblock'   => DocBlockGenerator::fromArray(
                            array(
                                'shortDescription' => 'Returns primary key.',
                                'longDescription'  => null,
                                'tags'             => array(
                                    new ReturnTag(array('datatype' => 'array|string')),
                                )
                            )
                    )
                )
            )
        );
    }
}
