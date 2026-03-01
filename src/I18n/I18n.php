<?php
namespace Bucorel\F2\I18n;

class I18n{
	
	protected $data = [];
	protected $path = "";
	protected $language = "";
	
	function __construct( string $path, string $language='en' ){
		$this->path = $path.'/'.$language.'/';
		$this->language = $language;
	}
	
	function load( string $module, string $alias ):void{
		$file = $this->path.$module.'.php';
		if( file_exists( $file ) ){
			require_once( $file );
			if( isset( $labels ) ){
				$this->data[ $alias ] = $labels;
			}
		}
	}
	
	function get( string $moduleAlias, string $label ){
		if( isset( $this->data[ $moduleAlias ][ $label ] ) ){
			return $this->data[ $moduleAlias ][ $label ];
		}else{
			return $this->language.':'.$moduleAlias.':'.$label;
		}
	}
	
	function getLanguage(){
		return $this->language;
	}
	
	function dump(){
		return $this->data;
	}
}
?>
