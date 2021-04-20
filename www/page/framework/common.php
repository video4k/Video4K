<?php
	/* Greetings from Tirana */
	
	error_reporting (FALSE);
	ini_set ('display_errors', FALSE);
	date_default_timezone_set ('Europe/Zurich');
	set_time_limit (30);
	
	DEFINE ('USR_INACTIVE', 0);
	DEFINE ('USR_ACTIVE', 1);
	DEFINE ('USR_TRUSTED', 2);
	DEFINE ('USR_COADMIN', 3);
	DEFINE ('USR_ADMIN', 4);
	
	DEFINE ('MAIN_PATH', dirname (__FILE__) . DIRECTORY_SEPARATOR . '..');
	DEFINE ('BULK_PATH', dirname (__FILE__) . DIRECTORY_SEPARATOR . 'bulks');
	DEFINE ('SMARTY_PATH', dirname (__FILE__) . DIRECTORY_SEPARATOR . 'smarty');
	
	include_once ('config.include.php');
	include_once ('smarty/smarty.class.php');
	include_once ('classes/recaptcha.lib.php');
	include_once ('classes/pid.class.php');
	include_once ('classes/image.class.php');
	include_once ('classes/base.class.php');
	include_once ('classes/database.class.php');
	include_once ('classes/user.class.php');
	include_once ('classes/imdb.parser.php');
	include_once ('classes/imdb.class.php');
	include_once ('classes/checker.class.php');
	
	$Smarty = new Smarty ();
	$DB = new Database ();
	
	$Base = new Base ();
	{
		$Base->SecurityEscape ($_GET);
		$Base->SecurityEscape ($_POST);
		$Base->SecurityEscape ($_COOKIE);
		$Base->SecurityEscape ($_SERVER);
	}
	
	$User = new User ();
	$IMDB = new IMDB ();
	
	$Smarty->compile_dir = (SMARTY_PATH . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR);
	$Smarty->config_dir = (SMARTY_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR);
	$Smarty->cache_dir = (SMARTY_PATH . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);
	$Smarty->template_dir = (MAIN_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);
	
	$Smarty->assign ('CONFIG', $CONFIG);
	$Smarty->assign ('SLIDER', $Base->GetSliderContent ());
?>