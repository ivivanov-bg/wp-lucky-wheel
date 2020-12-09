<?php
/**
 * Function include all files in folder
 *
 * @param $path   Directory address
 * @param $ext    array file extension what will include
 * @param $prefix string Class prefix
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'vi_include_folder' ) ) {
	function vi_include_folder( $path, $prefix = '', $ext = array( 'php' ) ) {

		/*Include all files in payment folder*/
		if ( ! is_array( $ext ) ) {
			$ext = explode( ',', $ext );
			$ext = array_map( 'trim', $ext );
		}
		$sfiles = scandir( $path );
		foreach ( $sfiles as $sfile ) {
			if ( $sfile != '.' && $sfile != '..' ) {
				if ( is_file( $path . "/" . $sfile ) ) {
					$ext_file  = pathinfo( $path . "/" . $sfile );
					$file_name = $ext_file['filename'];
					if ( $ext_file['extension'] ) {
						if ( in_array( $ext_file['extension'], $ext ) ) {
							$class = preg_replace( '/\W/i', '_', $prefix . ucfirst( $file_name ) );

							if ( ! class_exists( $class ) ) {
								require_once $path . $sfile;
								if ( class_exists( $class ) ) {
									new $class;
								}
							}
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'wplwl_get_explode' ) ) {
	function wplwl_get_explode( $sap = ',', $string, $limit = 3 ) {
		$rand       = 0;
		$show_wheel = explode( $sap, $string, $limit );
		$show_wheel = array_map( 'absInt', $show_wheel );
		if ( sizeof( $show_wheel ) > 1 ) {
			$rand = $show_wheel[0] < $show_wheel[1] ? rand( $show_wheel[0], $show_wheel[1] ) : rand( $show_wheel[1], $show_wheel[0] );
		} else {
			$rand = $show_wheel[0];
		}

		return $rand;
	}
}

if ( ! function_exists( 'wplwl_sanitize_text_field' ) ) {
	function wplwl_sanitize_text_field( $string ) {
		return sanitize_text_field( stripslashes( $string ) );
	}
}
