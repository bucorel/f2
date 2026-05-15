<?php
namespace Bucorel\F2\Core;

/**
 * WebApp (Web Application) Router implementation.
 * * This class extends the base Router and provides specific dispatch logic
 * suitable for web applications:
 * 1. Checks for and applies dynamic per-route rate limits.
 * 2. Initializes session handling for stateful interactions.
 */
class WebSite extends Router{

    /**
     * Dispatches the request through the application workflow.
     * * The workflow is: Resolve Route -> Apply Rate Limit -> Init Session -> Load Controller.
     * * @throws \ErrorException If the resolved route is undefined or the controller is missing.
     * @return void
     */
	function dispatch(): void{
		$route = $this->getRoute();

		//if no route is given in the REQUEST_URI use default route if configured
		if( $route == '' ){
			$route = $this->config['DEFAULT_ROUTE'] ?? '';
		}

		//if still no route
		if( $route == '' ){
			JsonResponse::showFatalError( 'NO_ROUTE_TO_DISPATCH' );
		}

		//echo $route;
		$page = $this->config['TEMPLATE_PATH'].$route.'.html';
		if( file_exists( $page ) ){
			//echo 'showing page - '.$page;
			$this->renderPage( $this->config['TEMPLATE_PATH'], $this->config['BASE_URL'], $route.'.html' );
		}else{
			header( 'HTTP/1.1 404 Not Found' );
			echo 'Not Found <b>/'.$route.'</b>';
			exit;
		}
	}

	function run():void{
		$this->initSession();
		$this->dispatch();
	}

	function initSession(){
		if( isset( $this->config['SESSION_NAME'] ) && $this->config['SESSION_NAME'] != "" ){
			session_name( $this->config['SESSION_NAME'] );
		}
		
		session_start();
	}
	
	function renderPage( string $templatePath, string $baseUrl, string $page ){
		$loader = new \Twig\Loader\FilesystemLoader( $templatePath );
		$options = array(
			'strict_variables' => false,
			'debug' => false,
			'cache'=> false
		);

		$twig = new \Twig\Environment($loader, $options);
		$twig->addGlobal( 'baseUrl', $baseUrl );
		
		//$template = 'pages/'.$this->language.'/'.$pageTemplate;
		echo  $twig->render( $page );
	}
}
?>
