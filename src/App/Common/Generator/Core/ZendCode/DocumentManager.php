<?php
namespace App\Common\Generator\Core\ZendCode;

use \Laminas\Code\Generator\ClassGenerator;
use \Laminas\Code\Generator\DocBlockGenerator;
use \Laminas\Code\Generator\DocBlock\Tag\GenericTag;
use \Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use \Laminas\Code\Generator\DocBlock\Tag\ReturnTag;
use \Laminas\Code\Generator\MethodGenerator;
use \Laminas\Code\Generator\ParameterGenerator;
use \Laminas\Code\Generator\PropertyGenerator;

/**
 * Description of Document
 *
 * @author teddy
 */
class DocumentManager extends AbstractGenerator
{

    private $data;

    public function getClassArrayRepresentation()
    {
        $this->data = $this->getData();

        $methods = $this->getConstructor();
        $methods = array_merge($methods, $this->getMethodsFindDoc());
        $methods = array_merge($methods, $this->getMethodsfindDocBy());
        $methods = array_merge($methods, $this->getMethodsfindOneDocBy());
        $methods = array_merge($methods, $this->getSaveDocumentMethod());
        $methods = array_merge($methods, $this->getDeleteDocumentMethod());

        return [
            'name'          => $this->data['_className'],
            'namespacename' => $this->data['_namespace'] . '\Document',
            'extendedclass' => $this->data['_namespace'] . '\Document\Document',
            'docblock'      =>
            (new DocBlockGenerator())
                ->setShortDescription('Application Document')
                ->setLongDescription('')
                ->setTags([
                    new GenericTag('author', $this->data['_author']),
                    new GenericTag('license', $this->data['_license']),
                    new GenericTag('package', $this->data['_namespace']),
                    new GenericTag('copyright', $this->data['_copyright']),
                ])
            ,
            'properties'    => $this->getProperties(),
            'methods'       => $methods,
        ];
    }

    private function getProperties()
    {
        $classProperties   = [];
        $classProperties[] = (new PropertyGenerator(
            'id',
            'array' !== $this->data['_primaryKey']['phptype'] ? $this->data['_primaryKey']['field'] : eval('return ' . $this->data['_primaryKey']['field'] . ';'),
            PropertyGenerator::FLAG_PROTECTED
        ))->setDocBlock((new DocBlockGenerator())
                ->setShortDescription('Primary key name')
                ->setLongDescription('')
                ->setTags([
                    new GenericTag('var', 'string|array' . ' ' . 'Primary key name'),
                ]));
        return $classProperties;
    }

    private function getConstructor()
    {
        $constructBody = '$this->container = $app;' . PHP_EOL;
        $constructBody .= '$docManager = $app[\'document\']->getDb();' . PHP_EOL;
        $constructBody .= 'parent::__construct($docManager->getManager(), $docManager->getDatabaseName(), \'' . $this->data['_tbname'] . '\');' . PHP_EOL;
        $indexes = [];
        $unique  = [];
        foreach ($this->data['_columns'] as $column) {
            if ($column['index']) {
                $indexes[$column['field']] = 1;
            }
            if ($column['unique']) {
                $unique[$column['field']] = 1;
            }
        }
        $constructBody .= '$indexes = iterator_to_array($this->listIndexes());' . PHP_EOL;
        $constructBody .= '$acc = [];' . PHP_EOL;
        $constructBody .= 'foreach ($indexes as $val) {' . PHP_EOL;
        $constructBody .= ' if ($val->getName() == \'_id_\'){' . PHP_EOL;
        $constructBody .= ' continue;' . PHP_EOL;
        $constructBody .= ' }' . PHP_EOL;
        $constructBody .= ' $acc[]=$val->getName();' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        $constructBody .= 'if (empty($acc)){' . PHP_EOL;
        if (count($indexes)) {
            $constructBody .= '$this->createIndex(' . var_export($indexes, true) . ');';
        }
        if (count($unique)) {
            $constructBody .= '$this->createIndex(' . var_export($unique, true) . ',[ \'unique\' => true]);';
        }
        $constructBody .= '}' . PHP_EOL;
        $methods = [
            [
                'name'       => '__construct',
                'parameters' => [
                    new ParameterGenerator('app', '\Pimple\Container'),
                ],
                'flags'      => MethodGenerator::FLAG_PUBLIC,
                'body'       => $constructBody,
                'docblock'   => (new DocBlockGenerator())
                    ->setShortDescription('Constructor')
                    ->setLongDescription('')
                    ->setTags([
                        new ParamTag('entity', [$this->data['_namespace'] . '\Entity\Entity']),
                    ]),
            ],
        ];
        return $methods;
    }

    private function getMethodsFindDoc()
    {
        $body = '$dc = $this->findOne(';
        if ('array' !== $this->data['_primaryKey']['phptype']) {
            $body .= '[\'' . $this->data['_primaryKey']['field'] . '\' => new \MongoDB\BSON\ObjectId($id)]';
        } else {
            $body .= '$id';
        }
        $body .= ');' . PHP_EOL;
        $body .= 'if ($dc) {' . PHP_EOL;
        $body .= '   $dc        = $dc->getArrayCopy();' . PHP_EOL;
        if ('array' !== $this->data['_primaryKey']['phptype']) {
            $body .= '  foreach ($dc as $k => $d) {' . PHP_EOL;
            $body .= '      if ($d instanceof \MongoDB\BSON\ObjectId) {' . PHP_EOL;
            $body .= '          $dc[$k] = $d->__toString();' . PHP_EOL;
            $body .= '      }' . PHP_EOL;
            $body .= '      if ($d instanceof \MongoDB\BSON\UTCDateTime) {' . PHP_EOL;
            $body .= '          $dc[$k] = $d->toDateTime()->format(\DateTime::ATOM);' . PHP_EOL;
            $body .= '      }' . PHP_EOL;
            $body .= '  }' . PHP_EOL;
        }
        $body .= '}' . PHP_EOL;
        $body .= 'return $dc;';
        return [
            [
                'name'       => 'findDoc',
                'parameters' => [
                    new ParameterGenerator('id'),
                ],
                'flags'      => MethodGenerator::FLAG_PUBLIC,
                'body'       => $body,
                'docblock'   =>
                (new DocBlockGenerator())
                    ->setShortDescription('Find by criteria')
                    ->setLongDescription('')
                    ->setTags([
                        new ParamTag('id', ['mixed'], 'Search by primary key'),
                        new ReturnTag(['array'], ''),
                    ])
                ,
            ],
        ];
    }

    private function getMethodsfindOneDocBy()
    {
        return [
            [
                'name'       => 'findOneDocBy',
                'parameters' => [
                    new ParameterGenerator('criteria',
                        'array',
                        []
                    ),
                ],
                'flags'      => MethodGenerator::FLAG_PUBLIC,
                'body'       => 'return current($this->findDocBy($criteria,[],1));',
                'docblock'   => (new DocBlockGenerator())
                    ->setShortDescription('Find one by criteria')
                    ->setLongDescription('')
                    ->setTags([
                        new ParamTag('criteria', ['array'], 'Search criteria'),
                        new ReturnTag(['array|boolean'], ''),
                    ]),
            ],
        ];
    }
    private function getMethodsfindDocBy()
    {
        $body = '$doc = $this->find($criteria,[\'limit\' => $limit,\'sort\' => $order,\'skip\' => $offset]);' . PHP_EOL;
        $body .= '$res = [];' . PHP_EOL;
        $body .= 'foreach ($doc as $dc) {' . PHP_EOL;
        $body .= '   $t        = json_decode(\MongoDB\BSON\Document::fromPHP($dc)->toRelaxedExtendedJSON(), true);' . PHP_EOL;
        if ('array' !== $this->data['_primaryKey']['phptype']) {
            $body .= '  foreach ($dc as $k => $d) {' . PHP_EOL;
            $body .= '      if ($d instanceof \MongoDB\BSON\ObjectId) {' . PHP_EOL;
            $body .= '          $t[$k] = $d->__toString();' . PHP_EOL;
            $body .= '      }' . PHP_EOL;
            $body .= '      if ($d instanceof \MongoDB\BSON\UTCDateTime) {' . PHP_EOL;
            $body .= '          $t[$k] = $d->toDateTime()->format(\DateTime::ATOM);' . PHP_EOL;
            $body .= '      }' . PHP_EOL;
            $body .= '  }' . PHP_EOL;
        } else {
            foreach ($this->data['_primaryKey']['fields'] as $key) {
                $body .= '  if ( $dc->' . $key['field'] . ' instanceof \MongoDB\BSON\ObjectId ){' . PHP_EOL;
                $body .= '      $t[\'' . $key['field'] . '\'] = $dc->' . $key['field'] . '->__toString();' . PHP_EOL;
                $body .= '  }' . PHP_EOL;
            }
        }
        $body .= '   $res[] = $t;' . PHP_EOL;
        $body .= '}' . PHP_EOL;
        $body .= 'return $res;' . PHP_EOL;
        $body .= '' . PHP_EOL;
        return [
            [
                'name'       => 'findDocBy',
                'parameters' => [
                    new ParameterGenerator('criteria', 'array',[]),
                    (new ParameterGenerator("order","array|null"))->setDefaultValue(null),
                    new ParameterGenerator("limit", 'int', 0),
                    new ParameterGenerator("offset",
                        'int',
                        0)
                ],
                'flags'      => MethodGenerator::FLAG_PUBLIC,
                'body'       => $body,
                'docblock'   =>
                (new DocBlockGenerator())
                    ->setShortDescription('Find by criteria')
                    ->setLongDescription('')
                    ->setTags([
                        new ParamTag('criteria', ['array'], 'Search criteria'),
                        new ParamTag('order', ['string'], 'sorting option'),
                        new ParamTag('limit', ['int'], 'limit option'),
                        new ParamTag('offset', ['int'], 'offset option'),
                        new ReturnTag(['array'], ''),
                    ])
                ,
            ],
        ];
    }
    private function getDeleteDocumentMethod()
    {
        $constructBody = '' . PHP_EOL;
        $constructBody .= 'if (! $entity instanceof ' . $this->data['_className'] . 'Document ){' . PHP_EOL;
        $constructBody .= '    throw new \Exception(\'Unable to delete: invalid entity\');' . PHP_EOL;
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
            $constructBody .= '    $result = $this->saveDocument($entity);' . PHP_EOL;
        } else {
            if ($this->data['_primaryKey']['phptype'] == 'array') {
                $constructBody .= '     $where =[];' . PHP_EOL;
                foreach ($this->data['_primaryKey']['fields'] as $key) {
                    $constructBody .= '    $pk_val = $entity->get' . $key['capital'] . '();' . PHP_EOL;
                    $constructBody .= '    if ($pk_val === null) {' . PHP_EOL;
                    $constructBody .= '        throw new \Exception(\'The value for ' . $key['capital'] . ' cannot be null\');' . PHP_EOL;
                    $constructBody .= '    } else {' . PHP_EOL;
                    $constructBody .= '        $where[\'' . $key['field'] . '\'] =  \is_string($pk_val) ? new \MongoDB\BSON\ObjectId($pk_val) : $pk_val; ' . PHP_EOL;
                    $constructBody .= '    }' . PHP_EOL;
                }
            } else {
                $constructBody .= '    $where = [\'' . $this->data['_primaryKey']['field'] . '\' => \is_string($entity->get' . $this->data['_primaryKey']['capital'] . '()) ? new \MongoDB\BSON\ObjectId($entity->get' . $this->data['_primaryKey']['capital'] . '()):$entity->get' . $this->data['_primaryKey']['capital'] . '()];' . PHP_EOL;
            }
            $constructBody .= '    $result = $this->deleteOne($where);' . PHP_EOL;
        }

        $constructBody .= '} catch (\Exception $e) {' . PHP_EOL;
        $constructBody .= '    !isset($this->getContainer()[\'logger\']) ? : $this->getContainer()[\'logger\']->error($e->getMessage(), [\'extra\'=>[
                \'class\'=> __CLASS__,
                \'function\'=> __FUNCTION__,
                \'msg\'=> $e->getMessage(),' . PHP_EOL;
        if ($this->data['_softDeleteColumn'] == null) {
            $constructBody .= '             \'data\'=> $where,' . PHP_EOL;
        }
        $constructBody .= ']' . PHP_EOL;
        $constructBody .= ']);' . PHP_EOL;
        $constructBody .= '    $result = false;' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        $constructBody .= 'return $result->getDeletedCount();' . PHP_EOL;
        $constructBody .= '' . PHP_EOL;
        $methods[] = [
            'name'       => 'deleteDocument',
            'parameters' => [
                new ParameterGenerator('entity', $this->data['_namespace'] . '\Entity\Entity'),
            ],
            'flags'      => MethodGenerator::FLAG_PUBLIC,
            'body'       => $constructBody,
            'docblock'   =>
            (new DocBlockGenerator())
                ->setShortDescription('Deletes the current entity')
                ->setLongDescription('')
                ->setTags([
                    new ParamTag('entity', [$this->data['_namespace'] . '\Entity\Entity'], 'Document to delete'),
                    new ReturnTag(['int', 'array', 'false'], 'Inserted id'),
                ])
            ,
        ];
        return $methods;
    }

    private function getSaveDocumentMethod()
    {
        $constructBody = '';
        $constructBody .= '$data = $entity->setIsDoc()->toArray();' . PHP_EOL;
        $constructBody .= 'if ($ignoreEmptyValues) {' . PHP_EOL;
        $constructBody .= '    foreach ($data as $key => $value) {' . PHP_EOL;
        $constructBody .= '        if ($value === null or $value === \'\') {' . PHP_EOL;
        $constructBody .= '            unset($data[$key]);' . PHP_EOL;
        $constructBody .= '        }' . PHP_EOL;
        $constructBody .= '    }' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        if ($this->data['_primaryKey']['phptype'] == 'array') {
            $constructBody .= '$primary_key = [];' . PHP_EOL;
            foreach ($this->data['_primaryKey']['fields'] as $key) {
                if (! $key['ai']) {
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
            $constructBody .= '$exists = $this->findDoc($primary_key);' . PHP_EOL;
            $constructBody .= '$success = true;' . PHP_EOL;
            $constructBody .= 'try {' . PHP_EOL;
            $constructBody .= '    // Check for current existence to know if needs to be inserted' . PHP_EOL;
            $constructBody .= '    if ($exists === null) {' . PHP_EOL;
            $constructBody .= '        $insert = $this->insertOne($data);' . PHP_EOL;
            if ($this->data['_primaryKey']['phptype'] == 'array') {
                foreach ($this->data['_primaryKey']['fields'] as $key) {
                    if ($key['ai']) {
                        $constructBody .= '        $success = $primary_key[\'' . $key['field'] . '\'] =  $insert->getInsertedId();' . PHP_EOL;
                    }
                }
            }
        } else {
            $constructBody .= '$primary_key = $entity->get' . $this->data['_primaryKey']['capital'] . '();' . PHP_EOL;
            $constructBody .= '$success = true;' . PHP_EOL;
            if (! $this->data['_primaryKey']['foreign_key']) {
                $constructBody .= 'unset($data[\'' . $this->data['_primaryKey']['field'] . '\']);' . PHP_EOL;
                $constructBody .= 'try {' . PHP_EOL;
                $constructBody .= '    if ($primary_key === null) {' . PHP_EOL;
            } else {
                $constructBody .= '$exists = $this->findDoc($primary_key);' . PHP_EOL;
                $constructBody .= 'try {' . PHP_EOL;
                $constructBody .= '    if ($exists === null) {' . PHP_EOL;
            }
            if ($this->data['_primaryKey']['phptype'] == 'string') {
                $constructBody .= '        $insert = $this->insertOne($data);' . PHP_EOL;
                $constructBody .= '        if ($insert){' . PHP_EOL;
                $constructBody .= '        $success = $primary_key = $insert->getInsertedId();' . PHP_EOL;
                $constructBody .= '        $entity->set' . $this->data['_primaryKey']['capital'] . '($primary_key);' . PHP_EOL;
                $constructBody .= '        } else {' . PHP_EOL;
                $constructBody .= '        $success = false;' . PHP_EOL;
                $constructBody .= '        }' . PHP_EOL;
            } else {
                $constructBody .= '        $insert = $this->insertOne($data);' . PHP_EOL;
                $constructBody .= '        $primary_key = $insert->getInsertedId();' . PHP_EOL;
                $constructBody .= '        if ($primary_key) {' . PHP_EOL;
                $constructBody .= '            $entity->set' . $this->data['_primaryKey']['capital'] . '($primary_key);' . PHP_EOL;
                if ($this->data['_returnId']) {
                    $constructBody .= '            $success = $primary_key;' . PHP_EOL;
                }
                $constructBody .= '        } else {' . PHP_EOL;
                $constructBody .= '            $success = false;' . PHP_EOL;
                $constructBody .= '        }' . PHP_EOL;
            }
        }
        $constructBody .= '    } else {' . PHP_EOL;
        if ($this->data['_primaryKey']['phptype'] == 'array') {
            foreach ($this->data['_primaryKey']['fields'] as $key) {
                $constructBody .= '     unset($data[\'' . $key['field'] . '\']);' . PHP_EOL;
            }
        } else {
            $constructBody .= '     unset($data[\'' . $this->data['_primaryKey']['field'] . '\']);' . PHP_EOL;
        }
        $constructBody .= '     $update = $this->updateOne(' . PHP_EOL;
        $constructBody .= '            [' . PHP_EOL;
        if ($this->data['_primaryKey']['phptype'] == 'array') {
            foreach ($this->data['_primaryKey']['fields'] as $key) {
                $constructBody .= '            \'' . $key['field'] . '\' => \is_string($primary_key[\'' . $key['field'] . '\']) ? new \MongoDB\BSON\ObjectId($primary_key[\'' . $key['field'] . '\']) : $primary_key[\'' . $key['field'] . '\'],' . PHP_EOL;
            }
        } else {
            $constructBody .= '             \'' . $this->data['_primaryKey']['field'] . '\' => \is_string($primary_key) ? new \MongoDB\BSON\ObjectId($primary_key) : $primary_key' . PHP_EOL;
        }

        $constructBody .= '            ],' . PHP_EOL;
        $constructBody .= '         [\'$set\' => $data ]' . PHP_EOL;
        $constructBody .= '        );' . PHP_EOL;
        if ($this->data['_primaryKey']['phptype'] != 'array') {
            $constructBody .= '     if ($update){' . PHP_EOL;
            $constructBody .= '         $success = $primary_key;' . PHP_EOL;
            $constructBody .= '     } else {' . PHP_EOL;
            $constructBody .= '         $success = false;' . PHP_EOL;
            $constructBody .= '     }' . PHP_EOL;
        }
        $constructBody .= '    }' . PHP_EOL;
        if (count($this->data['dependentTables']) > 0) {
            $constructBody .= '    if ($recursive) {' . PHP_EOL;
            foreach ($this->data['dependentTables'] as $key) {
                $constructBody .= '        if ($success && $entity->get' . $this->data['relationNameDependent'][$key['key_name']] . '() !== null) {' . PHP_EOL;
                if ($key['type'] !== 'many') {
                    // $constructBody .= '$success = $success &&  $entity->get' . $this->data['relationNameDependent'][$key['key_name']] . '()' . PHP_EOL;
                    // if ($this->data['_primaryKey']['phptype'] !== 'array') {
                    //     $constructBody .= '->set' . $this->_getCapital($key['column_name']) . '($primary_key)' . PHP_EOL;
                    // }
                    // $constructBody .= '->saveDocument($ignoreEmptyValues, $recursive, false);' . PHP_EOL;
                } else {
                    $constructBody .= '            $' . $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name'] . ' = $entity->get' . $this->data['relationNameDependent'][$key['key_name']] . '();' . PHP_EOL;
                    $constructBody .= '            $entityManager = new ' . $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name'] . '($this->container);' . PHP_EOL;
                    $constructBody .= '            foreach ($' . $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name'] . ' as $value) {' . PHP_EOL;
                    $constructBody .= '                $value' . PHP_EOL;
                    if ($this->data['_primaryKey']['phptype'] !== 'array') {
                        $constructBody .= '                    ->set' . $this->_getCapital($key['column_name']) . '(new \MongoDB\BSON\ObjectId($primary_key))' . PHP_EOL;
                    } else {
                        if (is_array($key['column_name'])) {
                            foreach (explode(',', $key['column_name'][0]) as $_column) {
                                $column = trim(str_replace('`', '', $_column));
                                $constructBody .= '                ->set' . $this->_getCapital($column) . '(new \MongoDB\BSON\ObjectId($primary_key[\'' . $column . '\']))' . PHP_EOL;
                            }
                        } else {
                            $constructBody .= '                ->set' . $this->_getCapital($key['column_name']) . '(new \MongoDB\BSON\ObjectId($primary_key[\'' . $key['foreign_tbl_column_name'] . '\']))' . PHP_EOL;
                        }
                    }
                    $constructBody .= '                 ;' . PHP_EOL;
                    $constructBody .= '                if (! ($success && $entityManager->saveDocument($value,$ignoreEmptyValues, $recursive))) {' . PHP_EOL;
                    $constructBody .= '                    break;' . PHP_EOL;
                    $constructBody .= '                }' . PHP_EOL;
                    $constructBody .= '            }' . PHP_EOL;
                }
                $constructBody .= '        }' . PHP_EOL;
            }
            $constructBody .= '    }' . PHP_EOL;
        }
        $constructBody .= '} catch (\Exception $e) {' . PHP_EOL;
        $constructBody .= '    !isset($this->getContainer()[\'logger\']) ? : $this->getContainer()[\'logger\']->error($e->getMessage(), [\'extra\'=>[
            \'class\'=> __CLASS__,
            \'function\'=> __FUNCTION__,
            \'msg\'=> $e->getMessage(),
            \'data\'=> $data,
        ],' . PHP_EOL;
        $constructBody .= ']);' . PHP_EOL;
        $constructBody .= '    $success = false;' . PHP_EOL;
        $constructBody .= '}' . PHP_EOL;
        $constructBody .= 'return $success;' . PHP_EOL;
        $methods[] = [
            'name'       => 'saveDocument',
            'parameters' => [
                new ParameterGenerator('entity', $this->data['_namespace'] . '\Entity\Entity'),
                new ParameterGenerator('ignoreEmptyValues', 'bool', true),
                new ParameterGenerator('recursive', 'bool', false),
            ],
            'flags'      => MethodGenerator::FLAG_PUBLIC,
            'body'       => $constructBody,
            'docblock'   =>
            (new DocBlockGenerator())
                ->setShortDescription('Saves current row, and optionally dependent rows')
                ->setLongDescription('')
                ->setTags([
                    new ParamTag('entity', [$this->data['_namespace'] . '\Entity\Entity'], 'Document to save'),
                    new ParamTag('ignoreEmptyValues', ['boolean'], 'Should empty values saved'),
                    new ParamTag('recursive', ['boolean'], 'Should the object graph be walked for all related elements'),
                    new ReturnTag(['int', 'array', 'false'], 'Inserted ID'),
                ])
            ,
        ];
        return $methods;
    }

    /**
     *
     * @return type
     */
    public function generate()
    {
        $c     = $this->getClassArrayRepresentation();
        $class = new ClassGenerator(
            $c['name'],
            $c['namespacename'],
            $c['flags'] ?? null,
            $c['extendedclass'],
            [],
            $c['properties'],
            $c['methods'],
            $c['docblock'],
        );
        $class
            ->addUse($this->data['_namespace'] . '\Entity\\' . $this->data['_className'], $this->data['_className'] . 'Document')
        ;
        $this->defineFileInfo($class);
        $fileGenerator = $this->getFileGenerator();
        return $fileGenerator
            ->setClass($class)
            ->generate();
    }

}
