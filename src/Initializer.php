<?php

function __autoload( $class )
{
    require_once( realpath( dirname( __FILE__ ) ) . '/' . str_replace( '\\', '/', $class ) . '.php' );
}
