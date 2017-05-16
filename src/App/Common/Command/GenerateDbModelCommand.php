<?php

namespace App\Common\Command;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Common\Generator\Core\MakeMysql;

class GenerateDbModelCommand extends BaseCommand {

    protected function configure() {
        $this
                ->setName('app:model-generator')
                ->setDescription('Generate Model Class')
                ->setDefinition([
                    new InputArgument('config-file', InputArgument::REQUIRED, 'Path to config file'),
                    new InputArgument('database', InputArgument::REQUIRED, 'The Database'),
                    new InputArgument('namespace', InputArgument::REQUIRED, 'The namespace.'),
                    new InputArgument('location', InputArgument::REQUIRED, 'Where to store model files'),
                    new InputOption('--tables-all', null, InputOption::VALUE_NONE, '', null),
                    new InputOption('--tables-regex', null, InputOption::VALUE_REQUIRED, '', false),
                    new InputOption('--tables-prefix', null, InputOption::VALUE_REQUIRED, '', []),
                ])
                ->setHelp(<<<EOT
                        <info>info</info>
                        <comment>foo</comment>
                        <question>foo</question>
                        <error>foo</error>
EOT
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $database     = $input->getArgument('database');
        $namespace    = $input->getArgument('namespace');
        $location     = $input->getArgument('location');
        $configfile   = $input->getArgument('config-file');
        $tablesAll    = $input->getOption('tables-all');
        $tablesRegex  = $input->getOption('tables-regex');
        $tablesPrefix = $input->getOption('tables-prefix');

        if (!file_exists($configfile)) {
            $output->writeln(sprintf('<error>Incorrect config file path "%s"</error>', $configfile));
            return false;
        }
        $config  = require_once $configfile;
        $db_type = $config['db.type'];
        switch ($db_type) {
            case 'Mysql':
                $dbAdapter = new MakeMysql($config, $database, $namespace);
                break;
            default:
                break;
        }
        $tables = $dbAdapter->getTablesNamesFromDb();
        if (empty($tables)) {
            $output->writeln(sprintf('<error>Please provide at least one table to parse.</error>'));
            return false;
        }
        // Check if a relative path
        $filesystem = new Filesystem();
        if (!$filesystem->isAbsolutePath($location)) {
            $location = getcwd() . DIRECTORY_SEPARATOR . $location;
        }
        $location .= DIRECTORY_SEPARATOR;
        $dbAdapter->addTablePrefixes($tablesPrefix);
        $dbAdapter->setLocation($location);
        foreach (['Table', 'Entity'] as $name) {
            $dir = $location . $name;
            if (!is_dir($dir)) {
                if (!@mkdir($dir, 0755, true)) {
                    $output->writeln(sprintf('<error>Could not create directory zf2 "%s"</error>', $dir));
                    return false;
                }
            }
        }
        $dbAdapter->setTableList($tables);
        $dbAdapter->addTablePrefixes($tablesPrefix);
        foreach ($tables as $table) {
            if ($tablesRegex && !preg_match("/$tablesRegex/", $table) > 0) {
                continue;
            }
            $dbAdapter->setTableName($table);
            try {
                $dbAdapter->parseTable();
                $dbAdapter->generate();
            } catch (Exception $e) {
                $output->writeln(sprintf('<error>Warning: Failed to process "%s" : %s ... Skipping</error>', $table, $e->getMessage()));
            }
        }
        $output->writeln(sprintf('<info>Done !!</info>'));
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output) {

        if (!$input->getArgument('config-file')) {
            $configfile = $this->getHelper('dialog')->askAndValidate(
                    $output, 'Please set the config file path : ', function($configfile) {
                if (empty($configfile)) {
                    throw new Exception('Config path name can not be empty');
                }
                return $configfile;
            }
            );
            $input->setArgument('config-file', $configfile);
        }
        if (!$input->getArgument('database')) {
            $database = $this->getHelper('dialog')->askAndValidate(
                    $output, 'Please choose a module database : ', function($database) {
                if (empty($database)) {
                    throw new Exception('Database name can not be empty');
                }
                return $database;
            }
            );
            $input->setArgument('database', $database);
        }
        if (!$input->getArgument('namespace')) {
            $namespace = $this->getHelper('dialog')->askAndValidate(
                    $output, 'Please choose a namespace : ', function($namespace) {
                if (empty($namespace)) {
                    throw new Exception('Namespace can not be empty');
                }

                return $namespace;
            }
            );
            $input->setArgument('namespace', $namespace);
        }
        if (!$input->getArgument('location')) {
            $location = $this->getHelper('dialog')->askAndValidate(
                    $output, 'Please choose an location : ', function($location) {
                if (empty($location)) {
                    throw new Exception('Location can not be empty');
                }
                return $location;
            }
            );
            $input->setArgument('location', $location);
        }
    }

}
