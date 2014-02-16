<?php
if ( ENVIRONMENT == 'DEVEL'){
	error_reporting(E_ALL);
	ini_set( 'display_errors','1');
} else {
	ini_set( 'display_errors','0');
	error_reporting( -1 );
}
set_error_handler( array( 'Error', 'captureNormal' ) );
set_exception_handler( array( 'Error', 'captureException' ) );
register_shutdown_function( array( 'Error', 'captureShutdown' ) );

class Error
{
	public static function sendMail($messages)
	{
		$mail_body = (is_array($messages) || is_object($messages)) ? var_export($messages, true) : $messages;
		
		$Name = "Tech Team Error"; 			//senders name
		$email = error_email; 				//senders e-mail adress
		
		$subject = "Error in:".$_SERVER['SERVER_NAME']; //subject
		$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields
		
		if ( ENVIRONMENT != 'DEVEL')
			mail($recipient, $subject, $mail_body, $header);
		else
			var_dump($messages);
	}
	
	private static function displayMessage($exception)
	{
		if ( ENVIRONMENT != 'DEVEL') {
			echo '<!-- '.print_r($exception, true).' -->'; 
			die('Disculpe, hubo un error interno. <br>Ha sido notificado el admin del sitio.<br>Intentelo pasados unos instantes. Gracias.');
		}else{
			var_dump($exception);
		}
	}

    // CATCHABLE ERRORS
    public static function captureNormal( $number, $message, $file, $line )
    {
        // Insert all in one table
		if (is_array($message)) $message = print_r($message, true);
        $error = array( 'type' => $number, 'message' => $message, 'file' => $file, 'line' => $line );
        // Display content $error variable
        self::sendMail($error);
    }
   
    // EXTENSIONS
    public static function captureException( $exception )
    {
        // Display content $exception variable
        self::sendMail($exception);
		self::displayMessage($exception);
    }
   
    // UNCATCHABLE ERRORS
    public static function captureShutdown( )
    {
        $error = error_get_last( );
        if( $error ) {           
            // Display content $error variable
            self::sendMail($error);
			self::displayMessage($error);
        } else { return true; }
    }
}

?>