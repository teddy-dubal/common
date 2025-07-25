<?php
namespace App\Common\Generator\Core\ZendCode;

use Laminas\Code\Generator\DocBlock\Tag\GenericTag;
use \Laminas\Code\Generator\ClassGenerator;
use \Laminas\Code\Generator\DocBlockGenerator;
use \Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use \Laminas\Code\Generator\DocBlock\Tag\ReturnTag;
use \Laminas\Code\Generator\MethodGenerator;
use \Laminas\Code\Generator\ParameterGenerator;
use \Laminas\Code\Generator\PropertyGenerator;

/**
 * Description of Entity
 *
 * @author teddy
 */
class EntityItem extends AbstractGenerator
{

    private $data;

    public function getClassArrayRepresentation()
    {
        $this->data = $this->getData();

        $methods = $this->getConstructor();
        $methods = array_merge($methods, $this->getAccessor());
        $methods = array_merge($methods, $this->getParentRelation());
        $methods = array_merge($methods, $this->getDependentTables());
        $methods = array_merge($methods, $this->getUtils());

        return [
            'name'          => $this->data['_className'],
            'namespacename' => $this->data['_namespace'] . '\Entity',
            'extendedclass' => $this->data['_namespace'] . '\Entity\Entity',
            'docblock'      => (new DocBlockGenerator())
                ->setShortDescription('Application Entity')
                ->setLongDescription('')
                ->setTags([
                    new GenericTag('author', $this->data['_author']),
                    new GenericTag('license', $this->data['_license']),
                    new GenericTag('package', $this->data['_namespace']),
                    new GenericTag('copyright', $this->data['_copyright']),
                ]),
            'properties'    => $this->getProperties(),
            'methods'       => $methods,
        ];
    }

    private function getProperties()
    {

        $classProperties   = [];
        $classProperties[] = (new PropertyGenerator('primary_key', 'array' !== $this->data['_primaryKey']['phptype'] ? $this->data['_primaryKey']['field'] : eval('return ' . $this->data['_primaryKey']['field'] . ';'), PropertyGenerator::FLAG_PROTECTED
        ))->setDocBlock((new DocBlockGenerator())
                ->setShortDescription('Primary key name')
                ->setLongDescription('')
                ->setTags([
                    new GenericTag('var', $this->data['_primaryKey']['phptype'] . ' primary_key'),
                ]));

        $classProperties[] = (new PropertyGenerator('isDoc', false, PropertyGenerator::FLAG_PROTECTED
        ))->setDocBlock((new DocBlockGenerator())
                ->setShortDescription('Set Entity type')
                ->setLongDescription('')
                ->setTags([
                    new GenericTag('var', 'boolean isDoc'),
                ]));

        foreach ($this->data['_columns'] as $column) {
            $comment           = ! empty($column['comment']) ? $column['comment'] : '';
            $classProperties[] = (new PropertyGenerator($column['capital'], null, PropertyGenerator::FLAG_PROTECTED
            ))->setDocBlock((new DocBlockGenerator())
                    ->setShortDescription($column['capital'])
                    ->setLongDescription($comment)
                    ->setTags([
                        new GenericTag('var', $column['phptype'] . ' ' . $column['capital']),
                    ]));
        }

        foreach ($this->data['foreignKeysInfo'] as $key) {
            if (! is_array($key['column_name'])) {
                $name = $this->data['relationNameParent'][$key['key_name']] . $this->_getCapital(
                    $key['column_name']
                );
                $classProperties[] = (new PropertyGenerator($name, null, PropertyGenerator::FLAG_PROTECTED
                ))->setDocBlock((new DocBlockGenerator())
                        ->setShortDescription('Parent relation')
                        ->setLongDescription('')
                        ->setTags([
                            new GenericTag('var', $this->data['className'][$key['key_name']]['foreign_tbl_name'] . ' ' . $name),
                        ]));
            }
        }

        foreach ($this->data['dependentTables'] as $key) {
            $name      = $this->data['relationNameDependent'][$key['key_name']];
            $longDescr = sprintf(
                'Type:  %s relationship', ($key['type'] == 'one') ? 'One-to-One' : 'One-to-Many'
            );
            $classProperties[] = (new PropertyGenerator($name, null, PropertyGenerator::FLAG_PROTECTED
            ))->setDocBlock((new DocBlockGenerator())
                    ->setShortDescription('Dependent relation ')
                    ->setLongDescription($longDescr)
                    ->setTags([
                        new GenericTag('var', $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name'] . ' ' . $name),
                    ]));
        }

        return $classProperties;
    }

    private function getConstructor()
    {
        $constructBody = '$this->setColumnsList([' . PHP_EOL;
        foreach ($this->data['_columns'] as $column) {
            $constructBody .= '     \'' . $column['field'] . '\' => \'' . $column['capital'] . '\',' . PHP_EOL;
        }
        $constructBody .= ']);' . PHP_EOL;
        $constructBody .= '$this->setParentList([' . PHP_EOL;
        foreach ($this->data['foreignKeysInfo'] as $key) {
            if (is_array($key['column_name'])) {
                $n = [];
                foreach ($key['column_name'] as $v) {
                    $n[] = '\'' . $this->data['relationNameParent'][$key['key_name']] . $this->_getCapital($v) . '\'';
                }
                $property = ' [' . implode(',', $n) . ']';
            } else {
                $property = '\'' . $this->data['relationNameParent'][$key['key_name']] . $this->_getCapital($key['column_name']) . '\'';
            }
            $constructBody .= ' \'' . $this->_getCapital($key['key_name']) . '\' => [' . PHP_EOL;
            $constructBody .= '     \'property\' => ' . $property . ',' . PHP_EOL;
            $constructBody .= '     \'table_name\' => \'' . $this->data['className'][$key['key_name']]['foreign_tbl_name'] . '\',' . PHP_EOL;
            $constructBody .= ' ],' . PHP_EOL;
        }
        $constructBody .= ']);' . PHP_EOL;
        $constructBody .= '$this->setDependentList([' . PHP_EOL;
        foreach ($this->data['dependentTables'] as $key) {
            $name = $this->data['relationNameDependent'][$key['key_name']];
            $constructBody .= ' \'' . $this->_getCapital($key['key_name']) . '\' => [' . PHP_EOL;
            $constructBody .= '     \'property\' => \'' . $name . '\',' . PHP_EOL;
            $constructBody .= '     \'table_name\' => \'' . $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name'] . '\',' . PHP_EOL;
            $constructBody .= ' ],' . PHP_EOL;
        }
        $constructBody .= ']);' . PHP_EOL;
        $methods = [
            [
                'name'       => '__construct',
                'parameters' => [],
                'flags'      => MethodGenerator::FLAG_PUBLIC,
                'body'       => $constructBody,
                'docblock'   => (new DocBlockGenerator())
                    ->setShortDescription('Sets up column and relationship lists')
                    ->setLongDescription(''),
            ],
        ];

        return $methods;
    }

    private function getAccessor()
    {
        $methods = [];
        foreach ($this->data['_columns'] as $column) {
            $is_date = strpos($column['type'], 'datetime') === false && strpos($column['type'], 'timestamp') === false;
            $comment = 'Sets column ' . $column['field'];
            $comment .= $is_date ? '' : ' Stored in \'Y-m-d H:i:s\' format .';
            $constructBody = '';
            if (! $is_date) {
                $constructBody .= 'if (! empty($data)) {' . PHP_EOL;
                $constructBody .= '    if ($data instanceof \MongoDB\BSON\UTCDateTime) {' . PHP_EOL;
                $constructBody .= '        $data = $data->toDateTime();' . PHP_EOL;
                $constructBody .= '    }' . PHP_EOL;
                $constructBody .= '    if (! $data instanceof \DateTime) {' . PHP_EOL;
                $constructBody .= '        $data = new \DateTime($data);' . PHP_EOL;
                $constructBody .= '    }' . PHP_EOL;
                $constructBody .= '    $data = $data->format(\'Y-m-d H:i:s\');' . PHP_EOL;
                $constructBody .= '}' . PHP_EOL;
            }
            $constructBody .= '$this->' . $column['capital'] . ' = $data;' . PHP_EOL;
            $constructBody .= 'return $this;' . PHP_EOL;

            $methods[] = new MethodGenerator('set' . $column['capital'], ['data'], MethodGenerator::FLAG_PUBLIC, $constructBody, (new DocBlockGenerator())
                    ->setShortDescription($comment)
                    ->setLongDescription('')
                    ->setTags([
                        new ParamTag('data', $column['phptype'], $column['field']),
                        new ReturnTag([
                            'datatype' => 'self',
                        ]),
                    ]));
            $comment = 'Gets column ' . $column['field'];
            $comment .= $is_date ? '' : ' Stored in \'Y-m-d H:i:s\' format .';
            $constructBody = '';
            $parameters    = [];
            $returnType    = $column['phptype'];
            $tags          = [
                new ReturnTag([
                    'datatype' => $returnType,
                ]),
            ];
            if (! $is_date) {
                $parameters = [
                    new ParameterGenerator('returnDateTime',
                        'bool',
                        false)
                    ,
                ];
                array_unshift($tags, new ParamTag('returnDateTime', ['boolean'], 'Should we return a DateTime object'));
                $constructBody .= 'if ($returnDateTime) {' . PHP_EOL;
                $constructBody .= '    if ($this->' . $column['capital'] . ' === null) {' . PHP_EOL;
                $constructBody .= '        return null;' . PHP_EOL;
                $constructBody .= '    }' . PHP_EOL;
                $constructBody .= '    return new \DateTime($this->' . $column['capital'] . ');' . PHP_EOL;
                $constructBody .= '}' . PHP_EOL;
                if ($this->data['db-type'] == 'mongodb') {
                    $constructBody .= 'if ($this->isDoc && $this->' . $column['capital'] . '){' . PHP_EOL;
                    $constructBody .= '    return new \MongoDB\BSON\UTCDateTime((new \DateTime($this->' . $column['capital'] . '))->getTimestamp() * 1000);' . PHP_EOL;
                    $constructBody .= '}' . PHP_EOL;
                }
                $constructBody .= 'return $this->' . $column['capital'] . ';' . PHP_EOL;
            } elseif ($column['phptype'] == 'boolean') {
                $constructBody .= 'return $this->' . $column['capital'] . ' ? true : false;' . PHP_EOL;
            } else {
                if ($this->data['db-type'] == 'mongodb') {
                    $constructBody .= 'if (!is_null($this->' . $column['capital'] . ')){' . PHP_EOL;
                    if ($column['primary']) {
                        $constructBody .= ' if ($this->' . $column['capital'] . ' instanceof \MongoDB\BSON\ObjectId){' . PHP_EOL;
                        $constructBody .= '     return $this->' . $column['capital'] . ';' . PHP_EOL;
                        $constructBody .= ' } else {' . PHP_EOL;
                        $constructBody .= '     return (' . $returnType . ')$this->' . $column['capital'] . ';' . PHP_EOL;
                        $constructBody .= ' }' . PHP_EOL;
                    } else {
                        $constructBody .= 'if ($this->isDoc && $this->' . $column['capital'] . ' instanceof \MongoDB\BSON\ObjectId){' . PHP_EOL;
                        $constructBody .= '    return $this->' . $column['capital'] . ';' . PHP_EOL;
                        $constructBody .= '}' . PHP_EOL;
                        $constructBody .= '     return (' . $returnType . ')$this->' . $column['capital'] . ';' . PHP_EOL;
                    }
                    $constructBody .= '}' . PHP_EOL;
                    $constructBody .= 'return $this->' . $column['capital'] . ';' . PHP_EOL;
                } else {
                    $constructBody .= 'return !empty($this->' . $column['capital'] . ') ? (' . $returnType . ')$this->' . $column['capital'] . ' : $this->' . $column['capital'] . ';' . PHP_EOL;
                }
            }

            $methods[] = new MethodGenerator('get' . $column['capital'], $parameters, MethodGenerator::FLAG_PUBLIC, $constructBody, (new DocBlockGenerator())
                    ->setShortDescription($comment)
                    ->setLongDescription('')
                    ->setTags($tags));
        }

        return $methods;
    }

    private function getParentRelation()
    {
        $methods = [];
        foreach ($this->data['foreignKeysInfo'] as $key) {
            if (is_array($key['column_name'])) {
                continue;
            }
            $comment       = 'Sets parent relation ' . $this->data['className'][$key['key_name']]['column_name'];
            $constructBody = '';
            $constructBody .= '$this->' . $this->data['relationNameParent'][$key['key_name']] . $this->_getCapital(
                $key['column_name']
            ) . ' = $data;' . PHP_EOL;
            $constructBody .= '$primary_key = $data->getPrimaryKey();' . PHP_EOL;
            $constructBody .= '$dataValue = $data->toArray();' . PHP_EOL;
            if (is_array($key['foreign_tbl_column_name']) && is_array($key['column_name'])) {
                while ($column = next($key['foreign_tbl_column_name'])) {
                    $foreign_column = next($key['column_name']);
                    $constructBody .= '$this->set' . $this->_getCapital(
                        $column
                    ) . '($primary_key[\'' . $foreign_column . '\']);' . PHP_EOL;
                }
            } else {
                /*
                $constructBody .= 'if (is_array($primary_key)) {' . PHP_EOL;
                $constructBody .= '     $primary_key = $primary_key[\'' . $key['foreign_tbl_column_name'] . '\'];' . PHP_EOL;
                $constructBody .= '}' . PHP_EOL;
                 */
                $constructBody .= '$this->set' . $this->_getCapital($key['column_name']) . '($dataValue[$primary_key]);' . PHP_EOL;
            }
            $constructBody .= 'return $this;' . PHP_EOL;
            $methods[] = [
                'name'       => 'set' . $this->data['relationNameParent'][$key['key_name']] . $this->_getCapital(
                    $key['column_name']
                ),
                'parameters' => [
                    new ParameterGenerator('data',
                        $this->data['_namespace'] . '\Entity\\' . $this->data['className'][$key['key_name']]['foreign_tbl_name']
                    )
                    ,
                ],
                'flags'      => MethodGenerator::FLAG_PUBLIC,
                'body'       => $constructBody,
                'docblock'   => (new DocBlockGenerator())
                    ->setShortDescription($comment)
                    ->setLongDescription('')
                    ->setTags([
                        new ParamTag('data', [$this->data['_namespace'] . '\Entity\\' . $this->data['className'][$key['key_name']]['foreign_tbl_name']]),
                        new ReturnTag(['datatype' => 'self']),
                    ]),
            ];
            $comment       = 'Gets parent ' . $this->data['className'][$key['key_name']]['column_name'];
            $constructBody = '';
            $constructBody .= 'return $this->' . $this->data['relationNameParent'][$key['key_name']] . $this->_getCapital(
                $key['column_name']
            ) . ';' . PHP_EOL;
            $methods[] = [
                'name'       => 'get' . $this->data['relationNameParent'][$key['key_name']] . $this->_getCapital(
                    $key['column_name']
                ),
                'parameters' => [],
                'flags'      => MethodGenerator::FLAG_PUBLIC,
                'body'       => $constructBody,
                'docblock'   => (new DocBlockGenerator())
                    ->setShortDescription($comment)
                    ->setLongDescription('')
                    ->setTags([
                        new ReturnTag(['datatype' => $this->data['className'][$key['key_name']]['foreign_tbl_name']]),
                    ]),
            ];
        }

        return $methods;
    }

    private function getDependentTables()
    {
        $methods = [];
        foreach ($this->data['dependentTables'] as $key) {
            if ($key['type'] == 'one') {
                $comment       = 'Sets dependent relation ' . $key['key_name'];
                $constructBody = '';
                $constructBody .= '$this->' . $this->data['relationNameDependent'][$key['key_name']] . ' = $data;' . PHP_EOL;
                $constructBody .= 'return $this;' . PHP_EOL;
                $methods[] = [
                    'name'       => 'set' . $this->data['relationNameDependent'][$key['key_name']],
                    'parameters' => [
                        new ParameterGenerator('data',
                            $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name'])
                        ,
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => $constructBody,
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription($comment)
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('data', [$this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name']]),
                            new ReturnTag(['datatype' => 'self']),
                        ]),
                ];
                $comment       = 'Gets dependent ' . $key['key_name'];
                $constructBody = '';
                $constructBody .= 'return $this->' . $this->data['relationNameDependent'][$key['key_name']] . ';' . PHP_EOL;
                $methods[] = [
                    'name'       => 'get' . $this->data['relationNameDependent'][$key['key_name']],
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => $constructBody,
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription($comment)
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag([$this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name']]),
                        ]),
                ];
            } else {
                $comment       = 'Sets dependent relation ' . $key['key_name'];
                $constructBody = '';
                $constructBody .= 'foreach ($data as $object) {' . PHP_EOL;
                $constructBody .= '     $this->add' . $this->data['relationNameDependent'][$key['key_name']] . '($object);' . PHP_EOL;
                $constructBody .= '}' . PHP_EOL;
                $constructBody .= 'return $this;' . PHP_EOL;
                $methods[] = [
                    'name'       => 'set' . $this->data['relationNameDependent'][$key['key_name']],
                    'parameters' => [
                        new ParameterGenerator('data',
                            'array'),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => $constructBody,
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription($comment)
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('data', ['array'], ' array of ' . $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name']),
                            new ReturnTag(['datatype' => 'self']),
                        ]),
                ];
                $comment       = 'Gets dependent ' . $key['key_name'];
                $constructBody = '';
                $constructBody .= 'return $this->' . $this->data['relationNameDependent'][$key['key_name']] . ';' . PHP_EOL;
                $methods[] = [
                    'name'       => 'get' . $this->data['relationNameDependent'][$key['key_name']],
                    'parameters' => [],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => $constructBody,
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription($comment)
                        ->setLongDescription('')
                        ->setTags([
                            new ReturnTag(['datatype' => 'array'], 'array of ' . $this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name']),
                        ]),
                ];
                $comment       = 'Sets dependent relations ' . $key['key_name'];
                $constructBody = '';
                $constructBody .= '$this->' . $this->data['relationNameDependent'][$key['key_name']] . '[] = $data;' . PHP_EOL;
                $constructBody .= 'return $this;' . PHP_EOL;
                $methods[] = [
                    'name'       => 'add' . $this->data['relationNameDependent'][$key['key_name']],
                    'parameters' => [
                        new ParameterGenerator('data'
                        ),
                    ],
                    'flags'      => MethodGenerator::FLAG_PUBLIC,
                    'body'       => $constructBody,
                    'docblock'   => (new DocBlockGenerator())
                        ->setShortDescription($comment)
                        ->setLongDescription('')
                        ->setTags([
                            new ParamTag('data', [$this->data['classNameDependent'][$key['key_name']]['foreign_tbl_name']], $comment),
                            new ReturnTag(['datatype' => 'self']),
                        ]),
                ];
            }
        }

        return $methods;
    }

    private function getUtils()
    {
        $constructBody = '';
        foreach ($this->data['_columns'] as $column) {
            $is_date = strpos($column['type'], 'datetime') !== false || strpos($column['type'], 'timestamp') !== false;
            if ($is_date) {
                $constructBody .= '$this->set' . $column['capital'] . '(isset($data[\'' . $column['field'] . '\']) ? $data[\'' . $column['field'] . '\'] : null);' . PHP_EOL;
            } else {
                $constructBody .= '$this->' . $column['capital'] . ' = isset($data[\'' . $column['field'] . '\']) ? $data[\'' . $column['field'] . '\'] : null;' . PHP_EOL;
            }
        }
        $constructBody .= 'return $this;';

        $methods[] = new MethodGenerator('exchangeArray', [
            new ParameterGenerator('data',
                'array'),
        ], MethodGenerator::FLAG_PUBLIC, $constructBody, (new DocBlockGenerator())
                ->setShortDescription('Array of options/values to be set for this model.')
                ->setLongDescription('Options without a matching method are ignored.')
                ->setTags([
                    new ParamTag('data', ['array'], 'array of values to set'),
                    new ReturnTag(['datatype' => 'self']),
                ]));
        $constructBody = '$this->isDoc = $val;' . PHP_EOL;
        $constructBody .= 'return $this;' . PHP_EOL;

        $methods[] = new MethodGenerator('setIsDoc', [
            new ParameterGenerator('val',
                'bool',
                true),
        ], MethodGenerator::FLAG_PUBLIC, $constructBody, (new DocBlockGenerator())
                ->setShortDescription('Set type of entity')
                ->setLongDescription('')
                ->setTags([
                    new ParamTag('val', ['boolean']),
                ]));
        $constructBody = '';
        $constructBody .= '$result = array(' . PHP_EOL;
        foreach ($this->data['_columns'] as $column) {
            $constructBody .= '     \'' . $column['field'] . '\' => $this->get' . $column['capital'] . '(),' . PHP_EOL;
        }
        $constructBody .= ');' . PHP_EOL;
        $constructBody .= 'return $result;' . PHP_EOL;

        $methods[] = (new MethodGenerator('toArray', [], MethodGenerator::FLAG_PUBLIC, $constructBody, (new DocBlockGenerator())
                ->setShortDescription('Returns an array, keys are the field names.')
                ->setLongDescription('')
                ->setTags([
                    new ReturnTag(['datatype' => 'array']),
                ])))->setReturnType('array');
        return $methods;
    }

    /**
     *
     * @return string
     */
    public function generate()
    {
        $c     = $this->getClassArrayRepresentation();
        $class = new ClassGenerator(
            $c['name'],
            $c['namespacename'],
            null,
            $c['extendedclass'],
            [],
            $c['properties'],
            $c['methods'],
            $c['docblock'],
        );
        $class->addUse($this->data['_namespace'] . '\Entity\Entity');
        $this->defineFileInfo($class);
        $fileGenerator = $this->getFileGenerator();

        return $fileGenerator
            ->setClass($class)
            ->generate();
    }

}
