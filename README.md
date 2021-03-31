App Common
======

* Generate Entity and EntityManager

```
# Php config file
php appcmd app:model-generator demo/config/config.php common App\\Model demo/models --tables-all --tables-type mongodb

# Yml config file
php appcmd app:model-generator demo/config/config.yml common App\\Model demo/models --tables-all --tables-type mongodb

```

* Pseudo Unit Test

```
php demo/mysql.php
php demo/mongodb.php

```
