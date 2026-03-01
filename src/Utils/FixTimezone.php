<?php
namespace Bucorel\F2\Utils;

class FixTimezone{
	
	const TIMEZONES = [
		'Asia/Calcutta'=>'Asia/Kolkata'
	];
	
	public static function fix( string $timezone ){
		if( isset( self::TIMEZONES[ $timezone ] ) ){
			return self::TIMEZONES[ $timezone ];
		}else{
			return $timezone;
		}
	}
}
?>
