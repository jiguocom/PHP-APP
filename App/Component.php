<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/26
 * Time: 17:17
 */

namespace App;


abstract class Component{
    protected $_events = [];
    protected $_behaviors = [];

    public final function on($event,$handler){
        if(!isset($this->_events[$event])){
            $this->_events[$event] = [];
        }

        if(is_callable($handler)){
            $this->_events[$event][] = $handler;
        }
    }

    public final function trigger($event,$data){
        if(isset($this->_events[$event])){
            foreach ($this->_events[$event] as $handler){
                call_user_func($handler,$data);
            }
        }
    }

    public function hasEventHandler($event){
        return !empty($this->_events[$event]);
    }

    public function attachBehavior($name,$behavior){
        if($behavior instanceof Behavior){
            $this->_behaviors[$name] = $behavior;
            $behavior->attach($this);
        }
    }

    public function __call($name, $arguments)
    {
        foreach ($this->_behaviors as $behavior){
            if(method_exists($behavior,$name)){
                $method = new ReflectionMethod($behavior,$name);

                if($method->isPublic()){
                    call_user_func_array([$behavior,$name],$arguments);
                }
            }else{
                throw new Exception('Unknown method:'.get_class($this)."::$name()");
            }
        }
    }

}