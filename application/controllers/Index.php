<?php
/**
 * @Author: Grove
 * @File  : Index.php
 * @Date  : 2021/4/25 2:54 下午
 * @last  : Modified by: I will never know what the next difficulty is        
 */

class IndexController extends Yaf_Controller_Abstract
{
    public function indexAction()
    {
        $this->_view->word = 'hello Grove';
    }
    public function testAction()
    {
        echo 1111;
        return false;
    }
}
