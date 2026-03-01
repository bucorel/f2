<?php
/**
 * Author - Pushpendra Singh Thakur <thakur@bucorel.com>
 * Copyright - Business Computing Research Laboratory <www.bucorel.com>
 */

namespace Bucorel\F2\Dal;

use PgSql\Connection;
use PgSql\Result;

class PgDb {
	
	protected ?Connection $db = null;
	protected string $connectionString = "";
	
	function __construct(
		string $dbName,
		string $user = "",
		string $password = "",
		string $host = "localhost",
		int $port = 5432,
		int $timeout = 3000
	) {
		$this->connectionString = "
			host={$host}
			port={$port}
			dbname={$dbName}
			user={$user}
			password={$password}
			connect_timeout={$timeout}
		";
	}
	
	function connect(): void {
		if( !$this->db ){
			$this->db = pg_connect( $this->connectionString );
		}
	}
	
	function query( string $sql, array $params=[] ): Result | false {
		$this->connect();
		return pg_query_params( $this->db, $sql, $params );
	}
	
	function fetch( Result $resultSet ): array | false {
		return pg_fetch_assoc( $resultSet );
	}
	
	function rowCount( Result $resultSet ): int {
		return pg_num_rows( $resultSet );
	}
	
	function affectedRowCount( Result $resultSet ): int {
		return pg_affected_rows( $resultSet );
	}
	
	function begin(): Result | false {
		return $this->query( "BEGIN" );
	}
	
	function commit(): Result | false {
		return $this->query( "COMMIT" );
	}
	
	static function isDuplicateRecordError( string $errorMessage ): bool {
		$errorMessage = strtolower( $errorMessage );
		
		return
			str_contains( $errorMessage, 'duplicate key value' ) ||
			str_contains( $errorMessage, 'violates unique constraint' );
	}
}

