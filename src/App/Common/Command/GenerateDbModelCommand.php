<?php
namespace App\Common\Command;

use App\Common\Command\BaseCommand;
use App\Common\Generator\Core\MakeMysql;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class GenerateDbModelCommand extends BaseCommand
{

    protected function configure()
    {
        $this
            ->setName('app:model-generator')
            ->setDescription('Generate Model Class')
            ->setDefinition([
                new InputArgument('config-file', InputArgument::REQUIRED, 'Path to config file'),
                new InputArgument('database', InputArgument::REQUIRED, 'The Database'),
                new InputArgument('namespace', InputArgument::REQUIRED, 'The namespace.'),
                new InputArgument('location', InputArgument::REQUIRED, 'Where to store model files'),
                new InputOption('--tables-all', null, InputOption::VALUE_NONE, '', null),
                new InputOption('--tables-type', null, InputOption::VALUE_REQUIRED, 'Type of file to generate Mysql [mysql] - MongoDb [mongodb]', 'mysql'),
                new InputOption('--tables-regex', null, InputOption::VALUE_REQUIRED, '', false),
                new InputOption('--tables-prefix', null, InputOption::VALUE_REQUIRED, '', []),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $database     = $input->getArgument('database');
        $namespace    = $input->getArgument('namespace');
        $location     = $input->getArgument('location');
        $configfile   = $input->getArgument('config-file');
        $tablesAll    = $input->getOption('tables-all');
        $tablesType   = $input->getOption('tables-type');
        $tablesRegex  = $input->getOption('tables-regex');
        $tablesPrefix = $input->getOption('tables-prefix');

        if (! file_exists($configfile)) {
            $output->writeln(sprintf('<error>Incorrect config file path "%s"</error>', $configfile));
            return false;
        }
        if (in_array(pathinfo(
            $configfile,
            PATHINFO_EXTENSION
        ), ['yaml', 'yml'])) {
            $configValues = Yaml::parse(file_get_contents($configfile));
            $conf         = $configValues['parameters'];
            $config       = [
                'docs.author'    => '',
                'docs.license'   => '',
                'docs.copyright' => '',
                'db.type'        => 'Mysql',
                'db.socket'      => '',
                'db.host'        => $conf['database_host'],
                'db.port'        => $conf['database_port'],
                'db.user'        => $conf['database_user'],
                'db.password'    => $conf['database_password'],
            ];
        } else {
            $config = require_once $configfile;
        }
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
        if (! $filesystem->isAbsolutePath($location)) {
            $location = getcwd() . DIRECTORY_SEPARATOR . $location;
        }
        $location .= DIRECTORY_SEPARATOR;
        $dbAdapter->addTablePrefixes($tablesPrefix);
        $dbAdapter->setLocation($location);
        $a = ['Table', 'Entity'];
        if ($tablesType == 'mongodb') {
            $a[] = 'Document';
        }
        foreach ($a as $name) {
            $dir = $location . $name;
            if (! is_dir($dir)) {
                if (! @mkdir($dir, 0755, true)) {
                    $output->writeln(sprintf('<error>Could not create directory zf2 "%s"</error>', $dir));
                    return false;
                }
            }
        }
        $dbAdapter->setTableList($tables);
        $dbAdapter->addTablePrefixes($tablesPrefix);
        foreach ($tables as $table) {
            if ($tablesRegex && ! preg_match("/$tablesRegex/", $table) > 0) {
                continue;
            }
            $dbAdapter->setTableName($table);
            try {
                $dbAdapter->parseTable();
                $dbAdapter->generate(['db-type' => $tablesType]);
            } catch (Exception $e) {
                $output->writeln(sprintf('<error>Warning: Failed to process "%s" : %s ... Skipping</error>', $table, $e->getMessage()));
            }
        }
        $output->writeln(sprintf('<info>Done !!</info>'));
        return 1;
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');
        if (! $input->getArgument('config-file')) {
            $question = new Question('Please set the config file path : ');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new RuntimeException('Config path name can not be empty');
                }
                return $answer;
            });
            $item = $helper->ask($input, $output, $question);
            $input->setArgument('config-file', $item);
        }
        $helper = $this->getHelper('question');
        if (! $input->getArgument('database')) {
            $question = new Question('Please choose a module database : ');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new RuntimeException('Database name can not be empty');
                }
                return $answer;
            });
            $item = $helper->ask($input, $output, $question);
            $input->setArgument('database', $item);
        }
        $helper = $this->getHelper('question');
        if (! $input->getArgument('namespace')) {
            $question = new Question('Please choose a namespace : ');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new RuntimeException('Namespace can not be empty');
                }
                return $answer;
            });
            $item = $helper->ask($input, $output, $question);
            $input->setArgument('namespace', $item);
        }
        $helper = $this->getHelper('question');
        if (! $input->getArgument('location')) {
            $question = new Question('Please choose a location : ');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new RuntimeException('Location can not be empty');
                }
                return $answer;
            });
            $item = $helper->ask($input, $output, $question);
            $input->setArgument('location', $item);
        }
    }

}
