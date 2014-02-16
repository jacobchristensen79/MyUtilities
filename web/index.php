<?php
/**
 * @Autor: Jacob Christensen
 */

// Load classes
require dirname(__FILE__).'/../include/boostrap.php';

// Start Application
$app = new App();
$app->sessionStart();

// Page to Load

$page = $app->getRequestedPage();

$mainLayout = 'layout';
$content='';

// Content Render
switch($page){
	case 'login':
		$view = $app->login();
		$data = array();
		if ( 'login' == $view ) {
			$data['_token'] = $app->getToken();
			
		}
		$content = $app->render($view, $data);
		break;
	case 'logout':
		$app->logout();		
		break;
	case 'home':
		$content = $app->render('home');
		break;
	case 'faq':
		$content = $app->render('faq');
		break;
	case 'makeshort':
		$content = $app->insertNewUrl();
		break;
	case 'account':
		$data = $app->getProfileData();
		$content = $app->render('account', $data);
		break;
    case 'thanks':    	
        $content = $app->render('thanks');
        break;	
	default:
		$content = $app->render('error404');		
}

// Render Outpur
if ( 'html' == $app->getOutputMethod() ) {
	echo $app->render($mainLayout, array('_content' => $content));
} else {
	header('Content-Type: application/json');
	echo json_encode($content);
}
?>