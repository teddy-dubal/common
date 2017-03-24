* NOTICE - Please read the ChangeLog file for latest updates and changes
           regarding v0.7RC2.
* I officially started working on a version that supports Zend Framework 2.
* (about time!) :)          

Zend-Db-Model-Generator
----------------------

Instructions:

1. copy config.php-default to config.php inside data directory
2. edit config.php and configure your db and other relevant directives.
3. execute it.

parameters:
    --database            : database name (required option)
    --location            : specify where to create the files (default is current directory)
    --templates           : specify the location of the templates (default is "templates")
    --namespace           : override config file's default namespace
 *  --table               : table name (parameter can be used more then once)
    --table-prefix        : remove that prefix of table name (can be used more then once)
    --all-tables          : create classes for all the scripts in the database
 *  --ignore-table        : not to create a class for a specific table
 *  --ignore-tables-regex : ignore tables by perl regular expression
 *  --tables-regex        : add tables by perl regular expression

                    parameters with * can be used more then once.
   
For comments/suggestions please e-mail,msn,google talk, google wave me at kfirufk@gmail.com.

REQUIREMENTS 
------------

1. php-cli 5.2+
2. PDO extension
3. This script was only tested on MySQL and MS-SQL server. if it works on any other servers
   please let me know.

NOTICE
------

Since version 0.4, in order to prevent code duplication,
common classes where seperated from the model class to MainModel.php
and from the DbTable class to MainDbTable.php. these classes are already
placed by the script in their appropriate places

USAGE
-----

Changelog is created by svn2cl (http://arthurdejong.org/svn2cl).
