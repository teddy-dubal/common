<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Common\Factory\ModelFactory;
use App\Model\Entity\User;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Zend\Db\Adapter\Adapter;



$config    = require_once __DIR__ . '/config/config.php';
$app       = new Container();
$app['db'] = array(
    'driver'   => 'Pdo_Mysql',
    'host'     => $config['db.host'],
    'dbname'   => 'common',
    'username' => $config['db.user'],
    'password' => $config['db.password'],
    'charset'  => 'utf8',
);
$app['logger'] = function ($container) {
    $log = new Logger('App-Common-Mysql-Log');
    $log->pushHandler(new StreamHandler('php://stderr'));
    return $log;
};
$app['entity'] = function() use ($app) {
    $db           = new Adapter($app['db']);
    $modelFactory = new ModelFactory($db, $app);
    return $modelFactory;
};

$timestart   = microtime(true);
$userManager = $app['entity']->get('\App\Model\Table\User');
$nbUser      = 10;
echo "***********************************************" . PHP_EOL;
//Ajouter x users
echo sprintf('Ajout de %s users', $nbUser) . PHP_EOL;
for ($i = 0; $i < $nbUser; $i++) {
    $user   = new User();
    $user->setOptions(array('name' => 'Teddy_' . $i));
    $user->exchangeArray(array('name' => 'Teddy'));
    $result = $userManager->saveEntity($user);
    echo sprintf('User id:%s', $result) . PHP_EOL;
}
echo "***********************************************" . PHP_EOL;
//Afficher les x users
echo sprintf('Affichage des %s users', $nbUser) . PHP_EOL;
$result   = $userManager->findBy([]);
$id_Users = array();
foreach ($result as $v) {
    var_dump($v);
    $id_Users[] = $v['id'];
}
$key = array_rand($id_Users);
$id  = $id_Users[$key];
echo "***********************************************" . PHP_EOL;
echo sprintf('Find user id : %s', $id) . PHP_EOL;
$u   = $userManager->find($id);

var_dump($u);

echo "***********************************************" . PHP_EOL;
$name = 'Teddy';
echo sprintf('Find user by criteria (name) : %s', $name) . PHP_EOL;
$u    = $userManager->findBy(array('name' => $name), 'id ASC', 10, null, false);

var_dump($u);
echo "***********************************************" . PHP_EOL;

echo sprintf('Find user by criteria (name) in debug mode : %s', $name) . PHP_EOL;
$userManager->setDebug(true);
$u    = $userManager->findBy(array('name' => $name), 'id ASC', 10, null, false);
var_dump($u);
$userManager->setDebug(false);
echo "***********************************************" . PHP_EOL;
$name = 'Teddy';
echo sprintf('Count by criteria (name) : %s', $name) . PHP_EOL;
$u    = $userManager->countBy(array('name' => $name));

var_dump($u);

echo "***********************************************" . PHP_EOL;
$name = 'Teddy';
echo sprintf('FindOneBy With result : %s', $name) . PHP_EOL;
$u    = $userManager->findOneBy(array('name' => $name));
var_dump($u);
echo "***********************************************" . PHP_EOL;
$name = 'Meddy';
echo sprintf('FindOneBy Without result : %s', $name) . PHP_EOL;
$u    = $userManager->findOneBy(array('name' => $name));
var_dump($u);
echo "***********************************************" . PHP_EOL;
$name = 'Teddy';
echo sprintf('FindOneEntityBy With result : %s', $name) . PHP_EOL;
$u    = $userManager->findOneEntityBy(array('name' => $name));
var_dump($u);
echo "***********************************************" . PHP_EOL;
$name = 'Meddy';
echo sprintf('FindOneEntityBy Without result : %s', $name) . PHP_EOL;
$u    = $userManager->findOneEntityBy(array('name' => $name));
var_dump($u);
echo "***********************************************" . PHP_EOL;
echo sprintf('Suppression des %s users', $nbUser) . PHP_EOL;
foreach ($id_Users as $v) {
    $userToDelete = new User();
    $userToDelete->setId($v);
    $res          = $userManager->deleteEntity($userToDelete);
}

echo "***********************************************" . PHP_EOL;

$timeend = microtime(true);
$time    = $timeend - $timestart;

$page_load_time = number_format($time, 3);
//echo "Debut du script: " . date("H:i:s", $timestart);
//echo "<br>Fin du script: " . date("H:i:s", $timeend);
echo PHP_EOL . "Script execute en " . $page_load_time . " sec" . PHP_EOL;
