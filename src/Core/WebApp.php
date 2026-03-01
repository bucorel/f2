<?php
namespace Bucorel\F2\Core;

/**
 * WebApp (Web Application) Router implementation.
 * * This class extends the base Router and provides specific dispatch logic
 * suitable for web applications:
 * 1. Checks for and applies dynamic per-route rate limits.
 * 2. Initializes session handling for stateful interactions.
 */
class WebApp extends Router{

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

		$controllerClassName = $this->getController( $route );

		if( $controllerClassName === false ){
			if( isset($this->config['ON_404_ERROR']) && $this->config['ON_404_ERROR']!="" ){
				$controllerClassName = $this->getController( $this->config['ON_404_ERROR'] );
			}
		}
		/**
		 * @todo add request rate limitter here
		 */
		
		$this->loadController( $controllerClassName );
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
}
?>
