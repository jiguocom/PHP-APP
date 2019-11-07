<?php
namespace App;

class App extends Component{

    public function __construct()
    {
        //设置错误异常
        Error::initSystemHandlers();
    }

    public function run(){
        echo 'a';
    }

}