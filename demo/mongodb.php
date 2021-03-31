<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Common\Factory\DocumentFactory;
use App\Common\ServiceProvider\MongoDb;
use App\Model\Entity\User;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;

$config = require_once __DIR__ . '/config/config.php';
$app    = new Container();

$app['mongodb_conf'] = $config['mongodb'];
$app['logger']       = function ($container) {
    $log = new Logger('App-Common-Mongo-Log');
    $log->pushHandler(new StreamHandler('php://stderr'));
    return $log;
};
$app['document'] = function () use ($app) {
    return new DocumentFactory($app, $app['mongodb_conf']['mongodb.options']['database']);
};
$app->register(new MongoDb(), $app['mongodb_conf']);

$timestart   = microtime(true);
$userManager = $app['document']->get('\App\Model\Document\User');
$nbUser      = 10;
echo "***********************************************" . PHP_EOL;
//Ajouter x users
echo sprintf('Ajout de %s users', $nbUser) . PHP_EOL;
for ($i = 0; $i < $nbUser; $i++) {
    $user = new User();
    $user->setOptions(array('name' => 'Teddy_' . $i, 'created_at' => 'now'));
    // $user->exchangeArray(array('name' => 'Teddy'));
    $result = $userManager->saveDocument($user);
    echo sprintf('User id:%s', $result) . PHP_EOL;
}
echo "***********************************************" . PHP_EOL;
//Afficher les x users
echo sprintf('Affichage des %s users', $nbUser) . PHP_EOL;
$result   = $userManager->findDocBy([]);
$id_Users = array();
foreach ($result as $v) {
    var_dump($v);
    $id_Users[] = current($v['_id']);
}
echo "***********************************************" . PHP_EOL;
$name = 'Teddy';
echo sprintf('Find user by criteria (name) : %s', $name) . PHP_EOL;
$u = $userManager->findDocBy(array('name' => $name));

var_dump($u);
echo "***********************************************" . PHP_EOL;

echo sprintf('Find user by criteria (name) in debug mode : %s', $name) . PHP_EOL;
$userManager->setDebug(true);
$u = $userManager->findDocBy(array('name' => $name));
var_dump($u);
$userManager->setDebug(false);
echo "***********************************************" . PHP_EOL;
$name = 'Teddy';
echo sprintf('Count by criteria (name) : %s', $name) . PHP_EOL;
$u = $userManager->count(array('name' => $name));

var_dump($u);

echo "***********************************************" . PHP_EOL;
$name = 'Teddy';
echo sprintf('FindOneBy With result : %s', $name) . PHP_EOL;
$u = $userManager->findOneDocBy(array('name' => $name));
var_dump($u);
echo "***********************************************" . PHP_EOL;
$name = 'Meddy';
echo sprintf('FindOneBy Without result : %s', $name) . PHP_EOL;
$u = $userManager->findOneDocBy(array('name' => $name));
var_dump($u);
echo "***********************************************" . PHP_EOL;
echo sprintf('Suppression des %s users', $nbUser) . PHP_EOL;
foreach ($id_Users as $v) {
    // $res = $userManager->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($v)]);
}

echo "***********************************************" . PHP_EOL;

$timeend = microtime(true);
$time    = $timeend - $timestart;

$page_load_time = number_format($time, 3);
//echo "Debut du script: " . date("H:i:s", $timestart);
//echo "<br>Fin du script: " . date("H:i:s", $timeend);
echo PHP_EOL . "Script execute en " . $page_load_time . " sec" . PHP_EOL;
