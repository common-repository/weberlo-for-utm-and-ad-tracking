<?php

spl_autoload_register(function ($class_name) {
    
    if ( false === strpos( $class_name, 'WEBERLO_' ))
        return;

    $class_name = strtolower( str_replace( '_','-', $class_name )); 
    
    if( false !== strpos( $class_name, 'admin' ) )
    {
        $class_dir = 'admin';
        $class_name = str_replace( 'admin-', '', $class_name );
    }
    elseif( false !== strpos( $class_name, 'public' ) )
    {
        $class_dir = 'public';
        $class_name = str_replace( 'public-', '', $class_name );
    }
    else
        $class_dir = 'includes';

    include WEBERLO_PLUGIN_PATH.$class_dir.'/class-'.$class_name.'.php';
});