<?php
namespace Bucorel\F2\Core;
use Bucorel\F2\I18n\I18n;
use Bucorel\F2\I18n\Language;
use Bucorel\F2\ViewLogic\TemplateEngine;
use Bucorel\F2\IpLocation\Country;
use Bucorel\F2\Core\IpAddress;
use Bucorel\F2\ViewLogic\UISignals;

class BFFController extends StatefulController{
	
	protected $i18n = null;
	protected $templateEngine = null;
	
	use UISignals;
	
	function init( array $config ){
		$this->config = $config;
		$this->initI18n();
		$this->checkRole();
		$this->initLocation();
		$this->initTemplatingEngine();
	}
	
	function initI18n(){
		if( !isset( $_SESSION['language'] ) ){
			$language = Language::getBrowserLanguage();
			if( Language::isSupported( $language ) &&
				in_array( $language, $this->config['AVAILABLE_LANGUAGES'] ) ){
				$_SESSION['language'] = $language;
			}else{
				$_SESSION['language'] = $this->config['DEFAULT_LANGUAGE'];
			}
		}
		
		$this->i18n = new I18n( $this->config['LANGUAGE_PATH'], $_SESSION['language'] );
	}
	
	function initTemplatingEngine(){
		$this->templateEngine = new TemplateEngine( $this->config['TEMPLATE_PATH'], $this->i18n, $this->config['BASE_URL'] );
	}
	
	function initLocation(){
		$ip = IpAddress::get() ?? 'unknown';
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

		if( !isset( $_SESSION['ip'] ) || !isset( $_SESSION['user-agent'] ) ){
			$_SESSION['ip'] = $ip;
			$_SESSION['user-agent'] = $userAgent;
			$_SESSION['country'] = Country::getCurrentCountryCode( $this->config['APP_MODE'], $this->config['IP_DB_PATH'] );
			return;
		}
		
		if( $_SESSION['ip'] != $ip || $_SESSION['user-agent'] != $userAgent ){
			$_SESSION['ip'] = $ip;
			$_SESSION['user-agent'] = $userAgent;
			$_SESSION['country'] = Country::getCurrentCountryCode( $this->config['APP_MODE'], $this->config['IP_DB_PATH'] );
			return;
		}
	}
	
	function handleGetRequest(){
		$data = [
			'config'=>$this->config
		];
		
		$this->i18n->load( 'common', 'common' );
		$this->i18n->load( $this->config['DEFAULT_THEME_LANGDATA'], 'theme' );
		echo $this->templateEngine->renderTheme( $this->config['DEFAULT_THEME'], $data );
	}
}
?>
