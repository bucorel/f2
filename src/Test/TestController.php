<?php
namespace Bucorel\F2\Test;
use Bucorel\F2\Core\StatelessController;
use Bucorel\F2\Dal\PgDb;

class TestController extends StatelessController{
	function handleGetRequest():void{
		$db = new PgDb(
			'auth',
			'postgres',
			'highrisk'
		);
		
		$list = array();
		$r = $db->query( "select * from user_accounts" );
		while( $row = $db->fetch( $r ) ){
			array_push( $list, $row );
		}
		
		$this->setData( $list );
		$this->finish();
	}
}
?>
