<?php

namespace U232;

class Config {

	public static function get( $key ){
		global $INSTALLER09;
		if( isset( $INSTALLER09[ $key ] ) ){
			return $INSTALLER09[ $key ];
		}
		return;
	}

	public static function set( $key, $value ){
		global $INSTALLER09;
		$INSTALLER09[ $key ] = $value;
	}

}
