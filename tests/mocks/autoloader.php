<?php

// This autoloader provide convenient way to working with mock object
// make the test looks natural. This autoloader support cascade file loading as well
// within mocks directory.
//
// Prototype :
//
// $mock_table = new Mock_Libraries_Table(); 			// Will load ./mocks/libraries/table.php
// $mock_database_driver = new Mock_Database_Driver();	// Will load ./mocks/database/driver.php
// and so on...
function autoload($class)
{
	$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR;


	if (strpos($class, 'Mock_') === 0)
	{
		$class = strtolower(str_replace(array('Mock_', '_'), array('', DIRECTORY_SEPARATOR), $class));
	}
	elseif (strpos($class, 'CI_') === 0)
	{
		// CI3 punya folder system/{core,libraries,database}/; Kodhe tidak.
		// Semua kelas CI_* nyata sudah dialiaskan oleh tests/aliases.php ke
		// FQCN paket kodhe/*, jadi di sini kita hanya mencari versi mock-nya
		// (mocks/core, mocks/libraries, mocks/libraries/<Driver>/drivers).
		// Mocks selalu menang atas kelas asli — sama seperti dulu, karena
		// autoloader ini jalan sebelum composer PSR-4 sempat turun tangan.
		$ci_core = array(
			'Benchmark',
			'Config',
			'Controller',
			'Exceptions',
			'Hooks',
			'Input',
			'Lang',
			'Loader',
			'Log',
			'Model',
			'Output',
			'Router',
			'Security',
			'URI',
			'Utf8'
		);

		$ci_libraries = array(
			'Calendar',
			'Driver_Library',
			'Email',
			'Encrypt',
			'Encryption',
			'Form_validation',
			'Ftp',
			'Image_lib',
			'Javascript',
			'Migration',
			'Pagination',
			'Parser',
			'Profiler',
			'Table',
			'Trackback',
			'Typography',
			'Unit_test',
			'Upload',
			'User_agent',
			'Xmlrpc',
			'Zip'
		);

		$ci_drivers = array('Session', 'Cache');

		$subclass = substr($class, 3);
		$file = NULL;

		if (in_array($subclass, $ci_core))
		{
			$file = $dir.'core/'.$subclass.'.php';
		}
		elseif (in_array($subclass, $ci_libraries))
		{
			$file = $dir.'libraries/'.(($subclass === 'Driver_Library') ? 'driver' : strtolower($subclass)).'.php';
		}
		elseif (in_array($subclass, $ci_drivers))
		{
			$file = $dir.'libraries/'.$subclass.'.php';
		}
		elseif (in_array(($parent = strtok($subclass, '_')), $ci_drivers))
		{
			$file = $dir.'libraries/'.$parent.'/drivers/'.strtolower(substr($subclass, strlen($parent)+1)).'.php';
		}
		else
		{
			// CI_DB_* dan lainnya: tidak ada mock-nya. Biarkan aliases.php /
			// composer autoload yang menangani (return FALSE).
			return FALSE;
		}

		if ( ! file_exists($file))
		{
			// Tidak ada mock -> serahkan ke autoloader lain (composer) dan
			// aliases yang didaftarkan Bootstrap.php.
			return FALSE;
		}

		include_once($file);
		return TRUE;
	}

	$file = isset($file) ? $file : $dir.$class.'.php';

	if ( ! file_exists($file))
	{
		return FALSE;
	}

	include_once($file);
}
