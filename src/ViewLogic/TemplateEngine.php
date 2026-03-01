<?php
namespace Bucorel\F2\ViewLogic;
use Bucorel\F2\I18n\I18n;

class TemplateEngine{

	protected $templatePath = '';
	protected $language = 'en';
	protected $twig = null;

	/**
     * @param string $templatePath The base path where all templates are stored.
     * @param string $language The default language for UI templates.
     */
	function __construct( string $templatePath, I18n $i18n, string $baseUrl='/' ){
		$this->templatePath = $templatePath;
		$this->language = $i18n->getLanguage();

		$loader = new \Twig\Loader\FilesystemLoader( $templatePath );
		$options = array(
			'strict_variables' => false,
			'debug' => false,
			'cache'=> false
		);

		$this->twig = new \Twig\Environment($loader, $options);
		$this->twig->addGlobal( 't', $i18n );
		$this->twig->addGlobal( 'baseUrl', $baseUrl );
	}
	
	/**
     * Renders a template from the structured UI path.
     * @return string The rendered HTML.
     */
	function render( string $module, array $data=array() ):string{
		$template = 'views/'.$module;
		return $this->twig->render( $template, $data );
	}
	
	function renderView( string $module, array $data=array() ):string{
		$template = 'views/'.$this->language.'/'.$module;
		return $this->twig->render( $template, $data );
	}

	function renderTheme( string $themeName, array $data=array() ):string{
		$template = 'themes/'.$themeName;
		return $this->twig->render( $template, $data );
	}

	function renderPage( string $pageTemplate, array $data ):string{
		$template = 'pages/'.$this->language.'/'.$pageTemplate;
		return $this->twig->render( $template, $data );
	}
}
?>
