<?php

namespace U232;
use mysqli;
use mysqli_stmt;

class DB {

	protected static $_inst;

	public function __construct(){
		return self::getInstance();
	}

	public static function getInstance(){
		if( is_null( self::$_inst ) ){
			self::$_inst = new __DB();
		}
		return self::$_inst;
	}

	public static function prepare( $query ){
		return self::getInstance()->prepare( $query );
	}

}

class __DB extends mysqli {

	public function __construct(){
		parent::init();
        if(
			! parent::real_connect(
				Config::get( 'SQL_HOST' ),
				Config::get( 'SQL_USER' ),
				Config::get( 'SQL_PASS' ),
				Config::get( 'SQL_DB' )
			)
		){
            exit( 'Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() );
        }
		if( $this->connect_errno === 2002 ){
			exit( 'ERROR_SQL_HOST' );
		} elseif( $this->connect_errno === 1045 ){
			exit( 'ERROR_SQL_CREDENTIALS' );
		} elseif( $this->connect_errno === 1049 ){
			exit( 'ERROR_SQL_DATABASE' );
		} elseif( $this->connect_errno > 0 ){
			exit( 'ERROR_SQL_UNTRACKED: ' . $this->connect_errno );
		}
		$this->set_charset( 'utf8' );
	}

	public function prepare( $query ){
		return new DB_Stmt( $this, $query );
	}

}

class DB_Stmt extends mysqli_stmt {

	private $_param_count = 0;

	public function prepare( $query ){
		$this->_param_count = substr_count( ':', $query );
		parent::prepare( $query );
	}

	public function bind( $type_map, $params = [] ){
		$params = (array) $params;
		if( is_array( $params ) ){
			parent::bind_param( $type_map, ...$params );
			return $this;
		} else {
			throw new \Exception( 'Invalid call for bind method.' );
		}
	}

	public function result( &$params ){
		$meta = $this->result_metadata();
		while( $field = $meta->fetch_field() ){
			$params[ $field->name ] =& $row[ $field->name ];
		}
		call_user_func_array( array( $this, 'bind_result' ), $params );
	}

}
