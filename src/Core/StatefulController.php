<?php
namespace Bucorel\F2\Core;

class StatefulController extends BaseController{
	
	protected $allowedRoles = [ 'any' ];
	
	function init( array $config ){
		$this->config = $config;
		$this->checkRole();
	}
	
	function checkRole(){
		//anyone can access this controller
		if( in_array( 'any', $this->allowedRoles ) ){
			return;
		}
		
		//check if this controller is for authorised users only
		if( in_array( 'auth', $this->allowedRoles ) && isset( $_SESSION['user']['roles'] ) ){
			return;
		}
		
		//check if this controller is for specific roles only
		if( isset( $_SESSION['user']['roles'] ) && 
			count( array_intersect( $this->allowedRoles, $_SESSION['user']['roles'] ) ) > 0 ){
			return;
		}
        
		$this->setStatus( self::STATUS_UNAUTHORIZED );
		$this->setMessage( 'Unauthorized' );
		$this->finish();
	}
}
?>
