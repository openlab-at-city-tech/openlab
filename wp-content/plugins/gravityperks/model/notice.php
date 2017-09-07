<?php

class GWNotice {
    
    public $class;
    public $message;
    
    function __construct($message, $args = array()) {
        
        extract(wp_parse_args($args, array(
            'class' => 'updated',
            'wrap' => 'p'
        )));
        
        $this->class = $class;
        $this->message = $message;
        $this->wrap = $wrap;
        
    }
    
    function display() {
        
        $str = "<div class=\"{$this->class}\">";
        
        if($this->wrap) {
            $str .= "<{$this->wrap}>{$this->message}</{$this->wrap}>";
        } else {
            $str .= $this->message;
        }
        
        $str .= '</div>';
        
        echo $str;
        
    }
    
}