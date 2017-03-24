<?php

// configuration parameters that are used both in zend framework v1 and v2
$config=array(

    # author name used in documentation
    'docs.author' => '<YOUR NAME HERE>',

    # licence for your code
    'docs.license' => 'http://framework.zend.com/license/new-bsd     New BSD License',

    # copyright for your (generated) code
    'docs.copyright' => 'ZF model generator',

    # database type
    'db.type' => 'Mysql',

    # database host
    'db.host' => 'mariadbcommon',

    # database unix socket (currently usful for mysql unix socket. leave empty if you want to use host)
    'db.socket' => '',

    # database port
    'db.port' => '3306',

    # database user
    'db.user' => 'common',

    # database password
    'db.password' => 'common',

    # zend framework version (1 - for 1.x, 2 - for 2.x)
    'zfv'=>2,

    # if enabled, all save methods will return the inserted ID when the row is created (this will not trigger when its an update)

    'save.return_id' => true,

    # default namespace name
    'namespace.default' => 'Default',
    #Override tg
    /* 'overrideTableGateway' => array(
          'namespace' => 'App\\Common\\Generator\\demo\\Custom\\Model',
          'className' => 'ModEntity'
          ) */


);
return $config;