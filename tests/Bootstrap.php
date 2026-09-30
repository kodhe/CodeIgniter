<?php
/**
 * Bootstrap suite unit-test warisan CodeIgniter 3, sudah disesuaikan untuk
 * Kodhe Framework (hasil pengembangan fork bcit-ci/CodeIgniter).
 *
 * Perubahan utama dari bootstrap CI3 asli:
 * - Folder `system/` tidak ada lagi; kode framework kini berasal dari paket
 *   composer `kodhe/*` (atau `../packages/*` saat pengembangan inline).
 * - Path konstanta mengikuti cara public/index.php + bootstrap/app.php:
 *     SYSPATH      => vendor/kodhe/framework/  (fallback: ../packages/framework/)
 *     BASEPATH     => SYSPATH                  (dipakai legacy common.php)
 *     SYSTEM_PATH  => SYSPATH.'src/'           (helper lama tests/mocks/*.php)
 *     APPPATH      => application/             (real path, bukan vfsStream)
 * - Legacy global functions (is_php, show_error, get_config, load_class, ...)
 *   dimuat dari src/Support/Legacy/common.php beserta compat/*.
 * - Alias kelas CI_* -> FQCN Kodhe didaftarkan lewat aliases.php SEBELUM
 *   autoloader mock di-register, supaya mocks/ tetap bisa menang atas
 *   kelas nyata bila file mock-nya tersedia.
 * - Hack lama yang menulis ulang TestCase.php milik PHPUnit (untuk PHP 7 /
 *   PHPUnit < 9) dihapus; suite ini sekarang menarget PHPUnit >= 9.
 */

// Errors on full!
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);

$dir = realpath(dirname(__FILE__));

// ---------------------------------------------------------------------------
// Path constants
// ---------------------------------------------------------------------------
defined('PROJECT_BASE') OR define('PROJECT_BASE', realpath($dir.'/../').'/');

$kodhe_vendor_path  = PROJECT_BASE.'vendor/kodhe/framework/';
$kodhe_packages_path = PROJECT_BASE.'../packages/framework/';

if ( ! defined('SYSPATH'))
{
	define('SYSPATH', is_dir($kodhe_vendor_path) ? $kodhe_vendor_path : $kodhe_packages_path);
}

// BASEPATH dipakai oleh legacy common.php & bootstrap/app.php warisan CI3.
defined('BASEPATH') OR define('BASEPATH', SYSPATH);

// SYSTEM_PATH dipertahankan karena tests/mocks/ dan banyak test lama masih
// merujuknya — TAPI dengan layout direktori CI3: SYSTEM_PATH.'core/',
// SYSTEM_PATH.'helpers/', SYSTEM_PATH.'libraries/', SYSTEM_PATH.'database/'.
// Layout Kodhe berbeda (src/, Resources/), jadi path-path itu tidak akan
// pernah resolve; mocks/ci_testcase.php & mocks/database/db.php sudah
// disesuaikan untuk memakai konstanta baru di bawah ini.
defined('SYSTEM_PATH') OR define('SYSTEM_PATH', SYSPATH.'src/');

// Helper & language milik Kodhe Framework (pengganti system/helpers dan
// system/language dari CI3).
defined('CI_HELPER_PATH') OR define('CI_HELPER_PATH', SYSPATH.'src/Support/Helpers/');
defined('CI_LANGUAGE_PATH') OR define('CI_LANGUAGE_PATH', SYSPATH.'Resources/language/');

// Root legacy code (common.php, compat/) — pengganti system/ secara umum.
defined('CI_LEGACY_PATH') OR define('CI_LEGACY_PATH', SYSPATH.'src/Support/Legacy/');

defined('APPPATH') OR define('APPPATH', PROJECT_BASE.'application/');
defined('VIEWPATH') OR define('VIEWPATH', APPPATH.'views/');
defined('ENVIRONMENT') OR define('ENVIRONMENT', 'development');

// ---------------------------------------------------------------------------
// Composer autoload (vfsStream + seluruh paket kodhe/*)
// ---------------------------------------------------------------------------
if ( ! class_exists('org\bovigo\vfs\vfsStream', FALSE))
{
	if (file_exists(PROJECT_BASE.'vendor/autoload.php'))
	{
		include_once PROJECT_BASE.'vendor/autoload.php';
	}
	elseif (file_exists(PROJECT_BASE.'../vendor/autoload.php'))
	{
		include_once PROJECT_BASE.'../vendor/autoload.php';
	}
}

if ( ! class_exists('vfsStream', FALSE))
{
	class_alias('org\bovigo\vfs\vfsStream', 'vfsStream');
	class_alias('org\bovigo\vfs\vfsStreamDirectory', 'vfsStreamDirectory');
	class_alias('org\bovigo\vfs\vfsStreamWrapper', 'vfsStreamWrapper');
}

// Set localhost "remote" IP
isset($_SERVER['REMOTE_ADDR']) OR $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// ---------------------------------------------------------------------------
// Prep our test environment
// ---------------------------------------------------------------------------
// 1) Mock globals versi test (get_instance(), load_class(), dsb). Dimuat
//    lebih dulu agar guard function_exists() di legacy common.php Kodhe
//    otomatis melewatkan fungsi yang sudah di-mock (show_error, get_config,
//    log_message, ...) — sama seperti bootstrap CI3 lama.
include_once $dir.'/mocks/core/common.php';

// 2) Kelas dasar test; dimuat eksplisit karena banyak file mocks/ lain
//    (core/security.php, core/uri.php, libraries/*.php) memanggil
//    CI_TestCase::instance() saat runtime dan nama "CI_TestCase" tidak
//    cocok dengan pola autoloader mocks/.
include_once $dir.'/mocks/ci_testcase.php';
include_once $dir.'/mocks/ci_testconfig.php';

// 3) Legacy global functions & konstanta dasar milik Kodhe untuk fungsi
//    yang TIDAK di-mock (is_php, is_really_writable, remove_invisible_
//    characters, html_escape, set_status_header asli, dll).
if (file_exists(CI_LEGACY_PATH.'common.php'))
{
	include_once CI_LEGACY_PATH.'common.php';
}

// 4) Alias CI_* -> FQCN Kodhe. class_exists($legacy, FALSE) di aliases.php
//    memastikan mock yang sudah terlanjur ter-load tidak ditimpa.
include_once $dir.'/aliases.php';

// Compat functions (mbstring/hash/password/standard) — sama seperti CI3,
// hanya dimuat bila ekstensi terkait tersedia.
if (extension_loaded('mbstring'))
{
	defined('MB_ENABLED') OR define('MB_ENABLED', TRUE);
	@ini_set('mbstring.internal_encoding', 'UTF-8');
	function_exists('mb_substitute_character') AND mb_substitute_character('none');
}
else
{
	defined('MB_ENABLED') OR define('MB_ENABLED', FALSE);
}

if (extension_loaded('iconv'))
{
	defined('ICONV_ENABLED') OR define('ICONV_ENABLED', TRUE);
	@ini_set('iconv.internal_encoding', 'UTF-8');
}
else
{
	defined('ICONV_ENABLED') OR define('ICONV_ENABLED', FALSE);
}

ini_set('default_charset', 'UTF-8');

foreach (array('mbstring', 'hash', 'password', 'standard') as $compat_file)
{
	$compat_path = SYSTEM_PATH.'Support/Legacy/compat/'.$compat_file.'.php';
	if (is_file($compat_path))
	{
		include_once $compat_path;
	}
}

// ---------------------------------------------------------------------------
// Autoloader mock — didaftarkan TERAKHIR (setelah composer autoload dan
// aliases.php), sehingga:
//   - kelas nyata Kodhe / vendor sudah resolve duluan via composer,
//   - hanya kelas CI_*/Mock_* tanpa FQCN terdaftar yang dimuat dari mocks/.
// ---------------------------------------------------------------------------
include_once $dir.'/mocks/autoloader.php';
spl_autoload_register('autoload');

unset($dir, $kodhe_vendor_path, $kodhe_packages_path, $compat_file, $compat_path);
