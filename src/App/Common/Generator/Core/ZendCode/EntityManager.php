<?php

namespace App\Generator\Core\ZendCode;

use \Zend\Code\Generator\ClassGenerator;
use \Zend\Code\Generator\DocBlock\Tag\ParamTag;
use \Zend\Code\Generator\DocBlock\Tag\GenericTag;
use \Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use \Zend\Code\Generator\DocBlockGenerator;
use \Zend\Code\Generator\MethodGenerator;
use \Zend\Code\Generator\ParameterGenerator;
use \Zend\Code\Generator\PropertyGenerator;

/**
 * Description of Entity
 *
 * @author teddy
 */
class EntityManager extends AbstractGenerator {

    private $data;

    public function getClassArrayRepresentation() {
        $this->data = $this->getData();

        $methods = $this->getConstructor();
        $methods = array_merge($methods, $this->getMethods());
        $methods = array_merge($methods, $this->getSaveEntityMethod());
        $methods = array_merge($methods, $this->getDeleteEntityMethod());


        return array(
            'name' => $this->data['_className'],
            'namespacename' => $this->data['_namespace'] . '\Table',
            'extendedclass' => 'Manager',
            'docblock' => DocBlockGenerator::fromArray(
                    array(
                        'shortDescription' => 'Application Entity Manager',
                        'longDescription' => null,
                        'tags' => array(
                            array(
                                'name' => 'package',
                                'description' => $this->data['_namespace'],
                            ),
                            array(
                                'name' => 'author',
                                'description' => $this->data['_author'],
                            ),
                            array(
                                'name' => 'copyright',
                                'description' => $this->data['_copyright'],
                            ),
                            array(
                                'name' => 'license',
                                'description' => $this->data['_license'],
                            ),
                        )
                    )
            ),
            'properties' => $this->getProperties(),
            'methods' => $methods
        );
    }

    private function getProperties() {
        $classProperties = array();
        $classProperties[] = PropertyGenerator::fromArray(array(
                    'name' => 'table',
                    'defaultvalue' => $this->data['_tbname'],
                    'flags' => PropertyGenerator::FLAG_PROTECTED,
                    'docblock' => DocBlockGenerator::fromArray(array(
                        'shortDescription' => 'Name of database table ',
                        'longDescription' => null,
                        'tags' => array(
                            new GenericTag('var', 'string' . ' ' . 'Name of DB Table'),
                        )
                    ))
        ));
        $classProperties[] = PropertyGenerator::fromArray(array(
                    'name' => 'id',
                    'defaultvalue' => 'array' !== $this->data['_primaryKey']['phptype'] ? $this->data['_primaryKey']['field'] : eval('return ' . $this->data['_primaryKey']['field'] . ';'),
                    'flags' => PropertyGenerator::FLAG_PROTECTED,
                    'docblock' => DocBlockGenerator::fromArray(array(
                        'shortDescription' => 'Primary key name',
                        'longDescription' => null,
                        'tags' => array(
                            new GenericTag('var', 'string|array' . ' ' . 'Primary key name'),
                        )
                    ))
        ));
        $classProperties[] = PropertyGenerator::fromArray(array(
                    'name' => 'sequence',
                    'defaultvalue' => 'array' !== $this->data['_primaryKey']['phptype'],
                    'flags' => PropertyGenerator::FLAG_PROTECTED,
                    'docblock' => DocBlockGenerator::fromArray(array(
                        'shortDescription' => 'Is primary Key auto increment',
                        'longDescription' => null,
                        'tags' => array(
                            new GenericTag('var', 'boolean' . ' ' . 'Is primary Key auto increment'),
                        )
                    ))
        ));
        return $classProperties;
    }

    private function getConstructor() {
        $constructBody = 'parent::__construct($adapter, $entity ? $entity : new ' . $this->data['_className'] . 'Entity());' . PHP_EOL;
        $methods = array(
            array(
                'name' => '__construct',
                'parameters' => array(
                    ParameterGenerator::fromArray(array(
                        'name' => 'adapter',
                        'type' => 'Adapter',
                    )),
                    ParameterGenerator::fromArray(
                            array(
                                'name' => 'entity',
                                'type' => $this->data['_className'] . 'Entity',
                                'defaultvalue' => null,
                            )
                    ),
                ),
                'flags' => MethodGenerator::FLAG_PUBLIC,
                'body' => $constructBody,
                'docblock' => DocBlockGenerator::fromArray(
                        array(
                            'shortDescription' => 'Constructor',
                            'longDescription' => 'Pass a DB Adapter to handle connection',
                            'tags' => array(
                                new ParamTag('adapter', array('Adapter'), 'Zend DB Adapter'),
                                new ParamTag('entity', array($this->data['_className'] . 'Entity'), 'Reference entity'),
                            )
                        )
                )
            ),
        );
        return $methods;
    }

    private function getMethods() {
        $constructBody = '' . PHP_EOL;
        $constructBody .= '$rowset = $this->select(' . PHP_EOL;
        if ($this->data['_primaryKey']['phptype'] !== 'array') {
            $constructBody .='      array(\'' . $this->data['_primaryKey']['field'] . '\' => $id)' . PHP_EOL;
        } else {
            $constructBody .= '$id' . PHP_EOL;
        }
        $constructBody .= ');' . PHP_EOL;
        $constructBody .= '$row = $rowset->current();' . PHP_EOL;
        $constructBody .= 'if (!$row) {' . PHP_EOL;
        $constructBody .= '     return null;' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        $constructBody .= 'return $row->getArrayCopy();' . PHP_EOL;
        $methods[] = array(
            'name' => 'find',
            'parameters' => array(
                ParameterGenerator::fromArray(array(
                    'name' => 'id',
                    'type' => $this->data['_primaryKey']['phptype'],
                ))
            ),
            'flags' => MethodGenerator::FLAG_PUBLIC,
            'body' => $constructBody,
            'docblock' => DocBlockGenerator::fromArray(
                    array(
                        'shortDescription' => 'Finds row by primary key',
                        'longDescription' => null,
                        'tags' => array(
                            new ParamTag('id', array($this->data['_primaryKey']['phptype']), 'Primary key value'),
                            new ReturnTag(array('array','null'), 'Found entity'),
                        )
                    )
            )
        );
        return $methods;
    }

    private function getDeleteEntityMethod() {
        $constructBody = '' . PHP_EOL;
        $constructBody .= 'if (! $entity instanceof ' . $this->data['_className'] . 'Entity ){' . PHP_EOL;
        $constructBody .= '    throw new \Exception(\'Unable to delete: invalid entity\');' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        $constructBody .= 'if ($useTransaction) {' . PHP_EOL;
        $constructBody .= '    $this->beginTransaction();' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        $constructBody .= 'try {' . PHP_EOL;
        if ($this->data['_softDeleteColumn'] != null) {
            foreach ($this->data['_columns'] as $column) {
                if ($column['field'] == $this->data['_softDeleteColumn']) {
                    $constructBody .= '    $entity->set' . $column['capital'] . '(';
                    if ($column['phptype'] == 'boolean') {
                        $constructBody .= 'true';
                    } else {
                        $constructBody .= '1';
                    }
                    $constructBody .= ');' . PHP_EOL;
                    break;
                }
            }
            $constructBody .= '    $result = $this->saveEntity($entity, true, false, false);' . PHP_EOL;
        } else {
            if ($this->data['_primaryKey']['phptype'] == 'array') {
                $constructBody .= '     $where = array();' . PHP_EOL;
                foreach ($this->data['_primaryKey']['fields'] as $key) {
                    $constructBody .= '    $pk_val = $entity->get' . $key['capital'] . '();' . PHP_EOL;
                    $constructBody .= '    if ($pk_val === null) {' . PHP_EOL;
                    $constructBody .= '        throw new \Exception(\'The value for ' . $key['capital'] . ' cannot be null\');' . PHP_EOL;
                    $constructBody .= '    } else {' . PHP_EOL;
                    $constructBody .= '        $where[\'' . $key['field'] . '\'] =  $pk_val; ' . PHP_EOL;
                    $constructBody .= '    }' . PHP_EOL;
                }
            } else {
                $constructBody .= '    $where = array(\'' . $this->data['_primaryKey']['field'] . '\' => $entity->get' . $this->data['_primaryKey']['capital'] . '());' . PHP_EOL;
            }
            $constructBody .= '    $result = $this->delete($where);' . PHP_EOL;
        }

        $constructBody .= '    if ($useTransaction) {' . PHP_EOL;
        $constructBody .= '        $this->commit();' . PHP_EOL;
        $constructBody .= '    }' . PHP_EOL;
        $constructBody .= '} catch (\Exception $e) {' . PHP_EOL;
        $constructBody .= '    !isset($this->getContainer()[\'logger\']) ? : $this->getContainer()[\'logger\']->debug(__CLASS__ . \'|\' . __FUNCTION__, array(' . PHP_EOL;
        $constructBody .= '    \'error\'=>$e->getMessage()));' . PHP_EOL;
        $constructBody .= '    if ($useTransaction) {' . PHP_EOL;
        $constructBody .= '        $this->rollback();' . PHP_EOL;
        $constructBody .= '    }' . PHP_EOL;
        $constructBody .= '    $result = false;' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        $constructBody .= 'return $result;' . PHP_EOL;
        $constructBody .= '' . PHP_EOL;
        $methods[] = array(
            'name' => 'deleteEntity',
            'parameters' => array(
                ParameterGenerator::fromArray(array(
                    'name' => 'entity',
                    'type' => $this->data['_className'] . 'Entity',
                )),
                ParameterGenerator::fromArray(array(
                    'name' => 'useTransaction',
                    'defaultValue' => true,
                )),
            ),
            'flags' => MethodGenerator::FLAG_PUBLIC,
            'body' => $constructBody,
            'docblock' => DocBlockGenerator::fromArray(
                    array(
                        'shortDescription' => 'Deletes the current entity',
                        'longDescription' => null,
                        'tags' => array(
                            new ParamTag('entity', array($this->data['_className'] . 'Entity'), 'Entity to delete'),
                            new ParamTag('useTransaction', array('boolean'), 'Flag to indicate if delete should be done inside a database transaction'),
                            new ReturnTag(array('int', 'array', 'false'), 'Inserted id'),
                        )
                    )
            )
        );
        return $methods;
    }

    private function getSaveEntityMethod() {
        $constructBody = '' . PHP_EOL;
        $constructBody .= '$data = $entity->toArray();' . PHP_EOL;
        $constructBody .= 'if ($ignoreEmptyValues) {' . PHP_EOL;
        $constructBody .= '    foreach ($data as $key => $value) {' . PHP_EOL;
        $constructBody .= '        if ($value === null or $value === \'\') {' . PHP_EOL;
        $constructBody .= '            unset($data[$key]);' . PHP_EOL;
        $constructBody .= '        }' . PHP_EOL;
        $constructBody .= '    }' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        if ($this->data['_primaryKey']['phptype'] == 'array') {
            $constructBody .= '$primary_key = array();' . PHP_EOL;
            foreach ($this->data['_primaryKey']['fields'] as $key) {
                if (!$key['ai']) {
                    $constructBody .= '$pk_val = $entity->get' . $key['capital'] . '();' . PHP_EOL;
                    $constructBody .= 'if ($pk_val === null) {' . PHP_EOL;
                    $constructBody .= '    return false;' . PHP_EOL;
                    $constructBody .= '} else {' . PHP_EOL;
                    $constructBody .= '    $primary_key[\'' . $key['field'] . '\'] =  $pk_val;' . PHP_EOL;
                    $constructBody .= '}' . PHP_EOL;
                } else {
                    $constructBody .= '$primary_key[\'' . $key['field'] . '\'] =  $entity->get' . $key['capital'] . '();' . PHP_EOL;
                }
            }
            $constructBody .= '$exists = $this->find($primary_key);' . PHP_EOL;
            $constructBody .= '$success = true;' . PHP_EOL;
            $constructBody .= 'if ($useTransaction) {' . PHP_EOL;
            $constructBody .= '    $this->beginTransaction();' . PHP_EOL;
            $constructBody .= '}' . PHP_EOL;
            $constructBody .= 'try {' . PHP_EOL;
            $constructBody .= '    // Check for current existence to know if needs to be inserted' . PHP_EOL;
            $constructBody .= '    if ($exists === null) {' . PHP_EOL;
            $constructBody .= '        $this->insert($data);' . PHP_EOL;
            if ($this->data['_primaryKey']['phptype'] == 'array') {
                foreach ($this->data['_primaryKey']['fields'] as $key) {
                    if ($key['ai']) {
                        $constructBody .= '        $success = $primary_key[\'' . $key['field'] . '\'] =  $this->getLastInsertValue();' . PHP_EOL;
                    }
                }
            }
        } else {
            $constructBody .= '$primary_key = $entity->get' . $this->data['_primaryKey']['capital'] . '();' . PHP_EOL;
            $constructBody .= '$success = true;' . PHP_EOL;
            $constructBody .= 'if ($useTransaction) {' . PHP_EOL;
            $constructBody .= '    $this->beginTransaction();' . PHP_EOL;
            $constructBody .= '}' . PHP_EOL;
            if (!$this->data['_primaryKey']['foreign_key']) {
                $constructBody .= 'unset($data[\'' . $this->data['_primaryKey']['field'] . '\']);' . PHP_EOL;
                $constructBody .= 'try {' . PHP_EOL;
                $constructBody .= '    if ($primary_key === null) {' . PHP_EOL;
            } else {
                $constructBody .= '$exists = $this->find($primary_key);' . PHP_EOL;
                $constructBody .= 'try {' . PHP_EOL;
                $constructBody .= '    if ($exists === null) {' . PHP_EOL;
            }
            $constructBody .= '        $this->insert($data);' . PHP_EOL;
            $constructBody .= '        $primary_key = $this->getLastInsertValue();' . PHP_EOL;
            $constructBody .= '        if ($primary_key) {' . PHP_EOL;
            $constructBody .= '            $entity->set' . $this->data['_primaryKey']['capital'] . '($primary_key);' . PHP_EOL;
            if ($this->data['_returnId']) {
                $constructBody .= '            $success = $primary_key;' . PHP_EOL;
            }
            $constructBody .= '        } else {' . PHP_EOL;
            $constructBody .= '            $success = false;' . PHP_EOL;
            $constructBody .= '        }' . PHP_EOL;
        }
        $constructBody .= '    } else {' . PHP_EOL;
        $constructBody .= '        $this->update(' . PHP_EOL;
        $constructBody .= '            $data,' . PHP_EOL;
        $constructBody .= '            array(' . PHP_EOL;
        if ($this->data['_primaryKey']['phptype'] == 'array') {
            foreach ($this->data['_primaryKey']['fields'] as $key) {
                $constructBody .= '            \'' . $key['field'] . ' = ?\' => $primary_key[\'' . $key['field'] . '\'],' . PHP_EOL;
            }
        } else {
            $constructBody .='             \'' . $this->data['_primaryKey']['field'] . ' = ?\' => $primary_key' . PHP_EOL;
        }
        $constructBody .= '            )' . PHP_EOL;
        $constructBody .= '        );' . PHP_EOL;
        if ($this->data['_primaryKey']['phptype'] != 'array') {
            $constructBody .='     $success = $primary_key;' . PHP_EOL;
        }
        $constructBody .= '    }' . PHP_EOL;
        if (count($this->data['dependentTables']) > 0) {
            $constructBody .= '    if ($recursive) {' . PHP_EOL;
            foreach ($this->data['dependentTables'] as $key) {
                $constructBody .= '        if ($success && $entity->get' . $this->data['relationNameDependent'][$key['key_name']] . '() !== null) {' . PHP_EOL;
                if ($key['type'] !== 'many') {
                    $constructBody .= '$success = $success &&  $entity->get' . $this->data['relationNameDependent'][$key['key_name']] . '()' . PHP_EOL;
                    if ($this->data['_primaryKey']['phptype'] !== 'array') {
                        $constructBody .= '->set' . $this->_getCapital($key['column_name']) . '($primary_key)' . PHP_EOL;
                    }
                    $constructBody .= '->saveEntity($ignoreEmptyValues, $recursive, false);' . PHP_EOL;
                } else {
                    $constructBody .= '            $' . $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name'] . ' = $entity->get' . $this->data['relationNameDependent'][$key['key_name']] . '();' . PHP_EOL;
                    $constructBody .= '            $entityManager = new ' . $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name'] . '($this->adapter);' . PHP_EOL;
                    $constructBody .= '            foreach ($' . $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name'] . ' as $value) {' . PHP_EOL;
                    $constructBody .= '                $value' . PHP_EOL;
                    if ($this->data['_primaryKey']['phptype'] !== 'array') {
                        $constructBody .= '                    ->set' . $this->_getCapital($key['column_name']) . '($primary_key)' . PHP_EOL;
                    } else {
                        if (is_array($key['column_name'])) {
                            foreach (explode(',', $key['column_name'][0]) as $_column) {
                                $column = trim(str_replace('`', '', $_column));
                                $constructBody .= '                ->set' . $this->_getCapital($column) . '($primary_key[\'' . $column . '\'])' . PHP_EOL;
                            }
                        } else {
                            $constructBody .= '                ->set' . $this->_getCapital($key['column_name']) . '($primary_key[\'' . $key['foreign_tbl_column_name'] . '\'])' . PHP_EOL;
                        }
                    }
                    $constructBody .= '                 ;' . PHP_EOL;
                    $constructBody .= '                if (! ($success && $entityManager->saveEntity($value,$ignoreEmptyValues, $recursive, false))) {' . PHP_EOL;
                    $constructBody .= '                    break;' . PHP_EOL;
                    $constructBody .= '                }' . PHP_EOL;
                    $constructBody .= '            }' . PHP_EOL;
                }
                $constructBody .= '        }' . PHP_EOL;
            }
            $constructBody .= '    }' . PHP_EOL;
        }
        $constructBody .= '    if ($useTransaction && $success) {' . PHP_EOL;
        $constructBody .= '        $this->commit();' . PHP_EOL;
        $constructBody .= '    } elseif ($useTransaction) {' . PHP_EOL;
        $constructBody .= '        $this->rollback();' . PHP_EOL;
        $constructBody .= '    }' . PHP_EOL;
        $constructBody .= '} catch (\Exception $e) {' . PHP_EOL;
        $constructBody .= '    !isset($this->getContainer()[\'logger\']) ? : $this->getContainer()[\'logger\']->debug(__CLASS__ . \'|\' . __FUNCTION__, array(' . PHP_EOL;
        $constructBody .= '    \'error\'=>$e->getMessage()));' . PHP_EOL;
        $constructBody .= '    if ($useTransaction) {' . PHP_EOL;
        $constructBody .= '        $this->rollback();' . PHP_EOL;
        $constructBody .= '    }' . PHP_EOL;
        $constructBody .= '    $success = false;' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        $constructBody .= 'return $success;' . PHP_EOL;
        $methods[] = array(
            'name' => 'saveEntity',
            'parameters' => array(
                ParameterGenerator::fromArray(array(
                    'name' => 'entity',
                    'type' => $this->data['_className'].'Entity',
                )),
                ParameterGenerator::fromArray(array(
                    'name' => 'ignoreEmptyValues',
                    'defaultValue' => true,
                )),
                ParameterGenerator::fromArray(array(
                    'name' => 'recursive',
                    'defaultValue' => false,
                )),
                ParameterGenerator::fromArray(array(
                    'name' => 'useTransaction',
                    'defaultValue' => true,
                )),
            ),
            'flags' => MethodGenerator::FLAG_PUBLIC,
            'body' => $constructBody,
            'docblock' => DocBlockGenerator::fromArray(
                    array(
                        'shortDescription' => 'Saves current row, and optionally dependent rows',
                        'longDescription' => null,
                        'tags' => array(
                            new ParamTag('entity', array($this->data['_className'].'Entity'), 'Entity to save'),
                            new ParamTag('ignoreEmptyValues', array('boolean'), 'Should empty values saved'),
                            new ParamTag('recursive', array('boolean'), 'Should the object graph be walked for all related elements'),
                            new ParamTag('useTransaction', array('boolean'), 'Flag to indicate if save should be done inside a database transaction'),
                            new ReturnTag(array('int', 'array', 'false'), 'Inserted ID'),
                        )
                    )
            )
        );
        return $methods;
    }

    /**
     *
     * @return type
     */
    public function generate() {
        $class = ClassGenerator::fromArray($this->getClassArrayRepresentation());
        $class
                ->addUse('Zend\Db\Adapter\Adapter')
                ->addUse($this->data['_namespace'] . '\Entity\\Entity')
                ->addUse($this->data['_namespace'] . '\Entity\\' . $this->data['_className'], $this->data['_className'] . 'Entity')
        ;
        $this->defineFileInfo($class);
        $fileGenerator = $this->getFileGenerator();
        return $fileGenerator
                        ->setClass($class)
                        ->generate();
    }

}
