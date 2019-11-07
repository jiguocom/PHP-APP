<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/26
 * Time: 17:27
 */

namespace App;


abstract class Behavior{
    public $owner;

    public function attach($to){
        $this->owner = $to;

        $methods = get_class_methods($this);
        foreach ($methods as $methodName){
            if(strpos($methodName,'on')==0 && strlen($methodName)>2){
                $this->owner->on(lcfirst(substr($methodName,2)),[$this,$methodName]);
            }
        }
    }
}