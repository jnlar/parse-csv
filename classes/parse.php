<?php
require_once 'vendor/autoload.php';

use League\Csv\Reader;

class parse {
	private static $header;
	private static $get_csv;
	private static $csv;
	public static $user_data;

	public function __construct() {
		if (get_opt::$args->hasOpt('file')) {
			self::get_file();
			self::read_file();
			self::trim_header();
		}
	}


	private static function read_file() {
		try {
			self::$csv = Reader::createFromPath(self::$get_csv);
			self::$csv->setHeaderOffset(0);
		} catch (Exception $e) {
			die(get_opt::$cli->red("ERROR: No file specified with --file flag\n"));
		}
	}

	private static function get_file() {
		return self::$get_csv = get_opt::$args->getOpt('file');
	}

	public static function handle_dry_run() {
		if (get_opt::$args->hasOpt('dry_run') && get_opt::$args->hasOpt('file')) {
			self::parse_csv();
		}
	}

  public static function parse_csv($callback = null) {
    foreach (self::$user_data as $user) {
			if (!self::check_valid_email($user)) {
				echo sprintf("Valid Email: %s \n", $user['email']);
			} else {
				self::check_valid_email($user);
			}

			$callback;
    }
  }

	private static function check_valid_email($user) {
		if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
			echo sprintf("Invalid Email: %s \n", $user['email']);
		} else return false;
	}

	private static function trim_header() {
		self::$header = array_map('trim', self::$csv->getHeader());
		self::$user_data = array_map(
			'self::reformat_user_data',
			iterator_to_array(self::$csv->getRecords(self::$header)));
	}

	private static function clean_names($user_names) {
		$names = str_replace(' ',  '', ucfirst(strtolower($user_names)));
		return preg_replace('/[!@#$%^&*()\t\-\+\-]/', '', $names);
	}

	private static function clean_email($email) {
		return preg_replace("/[\n\s\-]/", '', strtolower($email));
	}

	private static function reformat_user_data($user_details) {
		return [
			'name' => self::clean_names($user_details['name']),
			'surname' => self::clean_names($user_details['surname']),
			'email' => self::clean_email(strtolower($user_details['email']))
		];
	}
}