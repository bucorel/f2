<?php
namespace Bucorel\F2\Utils;

class InputField{
	
	protected $name = "";
	protected $type = null;
	protected $multiValue = false;
	protected $rules = [];
	
	function __construct( string $name, InputFieldType $type, bool $multiValue = false ){
		$this->name = $name;
		$this->type = $type;
		$this->multiValue = $multiValue;
	}
	
	function setRule( string $ruleName, mixed $value, string $errorMessage ){
		$this->rules[ $ruleName ] = array(
			"value"=>$value,
			"errMsg"=>$errorMessage
		);
	}
}
?>
