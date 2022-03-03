<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['post_controller_constructor'][] = array(
                                //'class'    => 'MyClass',
                                'function' => 'auth',
                                'filename' => 'hooks.php',
                                'filepath' => 'hooks'
                                );
$hook['pre_system'][] = array(
					     'class' => 'maintenance_hook',
					     'function' => 'offline_check',
					     'filename' => 'maintenance_hook.php',
					     'filepath' => 'hooks'
					     );