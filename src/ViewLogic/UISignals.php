<?php
namespace Bucorel\F2\ViewLogic;

trait UISignals{
	
	const SIGNAL_FILL = 'fill';
	const SIGNAL_SELECT_PANEL = 'selpanel';
	const SIGNAL_SELECT_PANE = 'selpane';
	const SIGNAL_SHOW_POPUP = 'popup';
	const SIGNAL_CLOSE_POPUP = 'cpopup';
	const SIGNAL_NEXT = 'next';
	const SIGNAL_CLEAR_ALL = 'clear';
	const SIGNAL_MAP = 'map';
	const SIGNAL_FIELD_ERROR = 'ferror';
	const SIGNAL_RECAPTCHA = 'recaptcha';
	const SIGNAL_REFRESH = 'refresh';
	
	function fill( string $targetId, string $data ){
		$signal = array(
			'_typ'=>self::SIGNAL_FILL,
			'_tar'=>$targetId,
			'_dat'=>$data
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function fillPanel( string $panelId, string $content ){
		$this->fill( $panelId.'_panel', $content );
	}
	
	function selectPanel( string $panelId ){
		$signal = array(
			'_typ'=>self::SIGNAL_SELECT_PANEL,
			'_tar'=>$panelId
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function selectPane( string $paneId ){
		$signal = array(
			'_typ'=>self::SIGNAL_SELECT_PANE,
			'_tar'=>$paneId
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function showPopup( string $popupType, string $popupId, string $title, string $content, int $closeBtn=1 ){
		$signal = array(
			'_typ'=>self::SIGNAL_SHOW_POPUP,
			'_pty'=>$popupType,
			'_tar'=>$popupId,
			'_tit'=>$title,
			'_dat'=>$content,
			'_cbtn'=>$closeBtn
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function closePopup( string $popupId ){
		$signal = array(
			'_typ'=>self::SIGNAL_CLOSE_POPUP,
			'_tar'=>$popupId
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function next( string $url, int $showProgressBar=1 ){
		$signal = array(
			'_typ'=>self::SIGNAL_NEXT,
			'_tar'=>$url,
			'_pbar'=>$showProgressBar
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function clearAll(){
		$signal = array(
			'_typ'=>self::SIGNAL_CLEAR_ALL
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function initMap( string $mapHolderId = 'map' ){
		$signal = array(
			'_typ'=>self::SIGNAL_MAP,
			'_tar'=>$mapHolderId
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function showFieldError( string $fieldId, string $message ){
		$signal = array(
			'_typ'=>self::SIGNAL_FIELD_ERROR,
			'_dat'=>array(
				$fieldId=>array( $message )
			)
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function initReCaptcha(){
		$signal = array(
			'_typ'=>self::SIGNAL_RECAPTCHA
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
	
	function refresh(){
		$signal = array(
			'_typ'=>self::SIGNAL_REFRESH
		);
		
		//we are using JsonResponse->appendData()
		$this->appendData( $signal );
	}
}
?>
