<?php
/**
 * @Author: Grove
 * @File  : index.php
 * @Date  : 2021/4/25 2:49 下午
 * @last  : Modified by: I will never know what the next difficulty is
 */

define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");
$app->run();
