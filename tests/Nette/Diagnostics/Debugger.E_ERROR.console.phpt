<?php

/**
 * Test: Nette\Diagnostics\Debugger E_ERROR in console.
 *
 * @author     David Grudl
 * @package    Nette\Diagnostics
 * @exitCode   255
 * @httpCode   500
 * @outputMatch OK!
 */

use Nette\Diagnostics\Debugger;


require __DIR__ . '/../bootstrap.php';


Debugger::$productionMode = FALSE;
header('Content-Type: text/plain');

Debugger::enable();

$onFatalErrorCalled = FALSE;

register_shutdown_function(function() use (& $onFatalErrorCalled) {
	Assert::true($onFatalErrorCalled);
	Assert::match(extension_loaded('xdebug') ? "
Fatal error: Call to undefined function missing_funcion() in %a%
exception 'Nette\\FatalErrorException' with message 'Call to undefined function missing_funcion()' in %a%
Stack trace:
#0 %a%: third()
#1 %a%: second()
#2 %a%: first()
#3 {main}
" : "
Fatal error: Call to undefined function missing_funcion() in %a%
exception 'Nette\\FatalErrorException' with message 'Call to undefined function missing_funcion()' in %a%
Stack trace:
#0 [internal function]: Nette\\Diagnostics\\Debugger::_shutdownHandler()
#1 {main}
", ob_get_clean());
	echo 'OK!';
});


Debugger::$onFatalError[] = function() use (& $onFatalErrorCalled) {
	$onFatalErrorCalled = TRUE;
};
ob_start();


function first($arg1, $arg2)
{
	second(TRUE, FALSE);
}


function second($arg1, $arg2)
{
	third(array(1, 2, 3));
}


function third($arg1)
{
	missing_funcion();
}


first(10, 'any string');
