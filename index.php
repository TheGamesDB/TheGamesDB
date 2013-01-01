<?php

## Connect to the database
include("include.php");

## Other Includes
include("extentions/wideimage/WideImage.php"); ## Image Manipulation Library

$time = time();

## Load Modules
include("modules/mod_userinit.php");
include("modules/mod_language.php");
include("modules/mod_main.php");
include("modules/mod_game.php");
include("modules/mod_platform.php");
include("modules/mod_comment.php");
include("modules/mod_user.php");
include("modules/mod_admin.php");
include("modules/mod_other.php");


if ($tab != "login" && isset($redirect))
{
	header("Location: $baseurl$redirect");
	exit;
}

## Default tab
if ($tab == "") {
    $tab = 'mainmenu';
}

if($tab != "mainmenu")
{
	if(!isset($headless))
	{
		// Load Template Header
		include("templates/default/header.php");
		
		// Load Tab Content
		include("tab_$tab.php");
		
		// Load Template Header
		include("templates/default/footer.php");
	}
}
else
{
	include("templates/default/front.php");
}
?>