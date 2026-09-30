<?php
/**
 * Jembatan kompatibilitas CodeIgniter 3 -> Kodhe Framework
 *
 * Fork bcit-ci/CodeIgniter ini sedang dikembangkan agar berjalan di atas
 * paket-paket `kodhe/*` (lihat composer.json & bootstrap/app.php). Suite
 * unit-test warisan CI3 masih merujuk ke kelas-kelas global `CI_*` yang
 * dulu berada di folder `system/`. Folder tersebut sudah tidak ada, jadi
 * file ini memetakan nama-nama lama itu ke implementasi Kodhe.
 *
 * Peta alias mengikuti sumber resmi framework:
 *   packages/framework/src/Config/Setup.php  ('aliases' => [...])
 *
 * Catatan penting:
 * - Semua class_alias() dibungkus class_exists() dengan autoload=FALSE agar
 *   mocks/autoloader.php tetap menjadi pemilik proses pemuatan file. Tanpa
 *   pembungkusan ini, PHP akan mencoba autoload lebih dulu dan autoloader
 *   mock tidak pernah dijalankan.
 * - Kelas database CI_DB_* sengaja TIDAK dialiaskan di sini; ia hanya bisa
 *   dimuat lewat konfigurasi DB driver aktif (mocks/database/db.php) dan
 *   suite-nya berjalan terpisah via tests/travis/*.phpunit.xml.
 *
 * Dimuat dari Bootstrap.php sebelum autoloader mock didaftarkan.
 */

// ---------------------------------------------------------------------------
// Core classes (dulu system/core/*.php)
// ---------------------------------------------------------------------------
$ci_test_aliases = array(
	// name lama (CI3)              => FQCN Kodhe
	'CI_Benchmark'             => 'Kodhe\Framework\Support\Legacy\Benchmark',
	'CI_Config'                => 'Kodhe\Framework\Config\Config',
	'CI_Exceptions'            => 'Kodhe\Framework\Support\Legacy\Exceptions',
	'CI_Hooks'                 => 'Kodhe\Framework\Support\Legacy\Hooks',
	'CI_Input'                 => 'Kodhe\Framework\Support\Legacy\Input',
	'CI_Lang'                  => 'Kodhe\Framework\Support\Language',
	'CI_Language'              => 'Kodhe\Framework\Support\Language',
	'CI_Loader'                => 'Kodhe\Framework\Config\Loaders\FileLoader',
	'CI_Log'                   => 'Kodhe\Framework\Support\Legacy\Log',
	'CI_Output'                => 'Kodhe\Framework\Support\Legacy\Output',
	'CI_Security'              => 'Kodhe\Framework\Support\Legacy\Security',
	'CI_URI'                   => 'Kodhe\Framework\Support\Legacy\URI',
	'CI_Utf8'                  => 'Kodhe\Framework\Support\Legacy\Utf8',
	'CI_Controller'            => 'Kodhe\Framework\Http\Controllers\BaseController',

	// Model punya implementasi legacy khusus di paket database
	'CI_Model'                 => 'Kodhe\Framework\Database\ORM\CI_Model',

	// Libraries yang dipindah/dirombak menjadi paket kodhe/*
	'CI_Calendar'              => 'Kodhe\Framework\Calendar\Calendar',
	'CI_Encryption'            => 'Kodhe\Framework\Encryption\Encryption',
	'CI_Form_validation'       => 'Kodhe\Framework\Validation\FormValidation',
	'CI_Parser'                => 'Kodhe\Framework\Parser\Parser',
	'CI_Table'                 => 'Kodhe\Framework\Table\Table',
	'CI_Typography'            => 'Kodhe\Framework\Typography\Typography',
	'CI_Upload'                => 'Kodhe\Framework\Upload\Upload',
	'CI_User_agent'            => 'Kodhe\Framework\Agent\UserAgent',

	// Driver-library CI3 (CI_Driver_Library + parent tiap grup driver).
	// Mocks tetap menang karena blok class_exists(..., FALSE) di bawah.
	'CI_Driver_Library'        => 'Kodhe\Framework\Driver\DriverLibrary',
	'CI_Driver'                => 'Kodhe\Framework\Session\Driver',
	'CI_Session'               => 'Kodhe\Framework\Session\Session',
	'CI_Cache'                 => 'Kodhe\Framework\Cache\Cache',
);

foreach ($ci_test_aliases as $legacy_name => $kodhe_class)
{
	if (class_exists($legacy_name, FALSE))
	{
		// Sudah tersedia (mis. didefinisikan ulang oleh mocks/) -> biarkan.
		continue;
	}

	if (class_exists($kodhe_class))
	{
		class_alias($kodhe_class, $legacy_name);
	}
}

unset($ci_test_aliases, $legacy_name, $kodhe_class);
