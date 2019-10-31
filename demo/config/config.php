<?php

// configuration parameters that are used both in zend framework v1 and v2
$config = array(

    # author name used in documentation
    'docs.author'       => '<YOUR NAME HERE>',

    # licence for your code
    'docs.license'      => 'http://framework.zend.com/license/new-bsd     New BSD License',

    # copyright for your (generated) code
    'docs.copyright'    => 'ZF model generator',

    # database type
    'db.type'           => 'Mysql',

    # database host
    'db.host'           => 'mariadb',
    # database unix socket (currently usful for mysql unix socket. leave empty if you want to use host)
    'db.socket'         => '',

    # database port
    'db.port'           => '3306',

    # database user
    'db.user'           => 'common',

    # database password
    'db.password'       => 'common',

    'save.return_id'    => true,

    # default namespace name
    'namespace.default' => 'Default',

);
return $config;
