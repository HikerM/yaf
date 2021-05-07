<?php

/**
 * @Author: Grove
 * @File  : Index.php
 * @Date  : 2021/4/25 2:54 下午
 * @last  : Modified by: I will never know what the next difficulty is
 */

class LoginController extends Yaf_Controller_Abstract
{
    public function indexAction()
    {
        echo  'hello Login';
        echo '<br>';
        $r = new loginModel;
        echo $r->login();
        echo '<br>';
        $m = new DB;
        $req = $m->sel_once('article_21','where itemid = 1');
        echo '<pre>';
        print_r($req);
        echo '</pre>';
        return false;
    }

}
