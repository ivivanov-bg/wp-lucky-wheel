<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WP_LUCKY_WHEEL_DATA {
	private $params;
	private $default;
	protected static $instance = null;

	/**
	 * VI_WP_LUCKY_WHEEL_DATA constructor.
	 * Init setting
	 */
	public function __construct() {
		global $wp_lucky_wheel_settings;
		if ( ! $wp_lucky_wheel_settings ) {
			$wp_lucky_wheel_settings = get_option( '_wplwl_settings', array() );
		}
		$this->default = array(
			'general'                           => array(
				'enable'     => "on",
				'mobile'     => "on",
				'spin_num'   => 1,
				'delay'      => 24,
				'delay_unit' => 'h'
			),
			'notify'                            => array(
				'position'                 => 'bottom-right',
				'size'                     => 40,
				'color'                    => '',
				'intent'                   => 'popup_icon',
				'hide_popup'               => 'off',
				'show_wheel'               => '1,5',//initial time
				'show_again'               => 24,
				'show_again_unit'          => 'h',
				'show_only_front'          => 'off',
				'show_only_blog'           => 'off',
				'show_only_shop'           => 'off',
				'conditional_tags'         => '',
				'time_on_close'            => '1',
				'time_on_close_unit'       => 'd',
			),
			'wheel_wrap'                        => array(
				'description'            => '<h2><span style="color: #ffffff;">SPIN TO WIN!</span></h2>
<ul>
 	<li><em><span style="color: #dbdbdb;">Try your lucky to get discount coupon</span></em></li>
 	<li><em><span style="color: #dbdbdb;">1 spin per email</span></em></li>
 	<li><em><span style="color: #dbdbdb;">No cheating</span></em></li>
</ul>',
				'bg_image'               => VI_WP_LUCKY_WHEEL_IMAGES . '2020.png',
				'bg_color'               => '#a77e44',
				'text_color'             => '#ffffff',
				'spin_button'            => 'Try Your Lucky',
				'spin_button_color'      => '#000000',
				'spin_button_bg_color'   => '#ffbe10',
				'pointer_position'       => 'center',
				'pointer_color'          => '#f70707',
				'wheel_center_color'     => '#ffffff',
				'close_option'           => 'on',
				'font'                   => '',
				'gdpr'                   => 'off',
				'gdpr_message'           => 'I agree with the <a href="">term and condition</a>',
				'custom_css'             => '',
			),
			'wheel'                             => array(
				'prize_type'        => array(
					"non",
					"custom",
					"non",
					"custom",
					"non",
					"custom",
				),
				'custom_value'      => array(
					"",
					"prize",
					"",
					"prize",
					"",
					"prize",
				),
				'custom_label'      => array(
					"Not Lucky",
					"Prize",
					"Not Lucky",
					"Prize",
					"Not Lucky",
					"Prize",
				),
				'probability'       => array( '30', '3', '30', '3', '30', '4' ),
				'bg_color'          => array(
					'#ffe0b2',
					'#e65100',
					'#ffb74d',
					'#fb8c00',
					'#ffe0b2',
					'#e65100',
				),
				'slices_text_color' => array(
					'#fff',
					'#fff',
					'#fff',
					'#fff',
					'#fff',
					'#fff',
				),
				'slice_text_color'  => '#fff',
				'show_full_wheel'   => 'off',
				'random_color'      => 'off',
			),
			'result'                            => array(
				'auto_close'   => 0,
				'email'        => array(
					'from_name'             => '',
					'from_address'          => '',
					'subject'               => 'Wordpress lucky wheel award',
					'heading'               => 'Congratulations!',
					'content'               => "Dear {customer_name},\n You spinned and won the {prize_label}. The code is {prize_value}. Please use this code and contact with us to receive the prize. Thank you.\n Your Sincerely!",
					'header_image'          => '',
					'footer_text'           => '',
					'base_color'            => '#a1fbf2',
					'background_color'      => '#5b9dd9',
					'body_background_color' => '#ffffff',
					'body_text_color'       => '#0f0f0f',
				),
				'notification' => array(
					'win'  => 'Congratulations! You have won a {prize_label}. Code was sent to {customer_email}. Please check your inbox. Thank you!',
					'lost' => 'Just almost win. Maybe you\'ll be lucky next time.',
				),
			),
			'ajax_endpoint'                     => 'ajax',
			'custom_field_name_enable'          => 'on',
			'custom_field_name_enable_mobile'   => 'on',
			'custom_field_name_required'        => 'off',
		);

		$this->params = apply_filters( 'wp_lucky_wheel_params', wp_parse_args( $wp_lucky_wheel_settings, $this->default ) );
	}

	public static function get_instance( $new = false ) {
		if ( $new || null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function get_params( $name = '', $name_sub = '', $language = '' ) {
		$language = apply_filters( '_wplwl_settings_language', $language, $name, $name_sub );
		if ( ! $name ) {
			return $this->params;
		} elseif ( isset( $this->params[ $name ] ) ) {
			if ( $name_sub ) {
				if ( isset( $this->params[ $name ][ $name_sub ] ) ) {
					if ( $language ) {
						$name_language = $name_sub . '_' . $language;
						if ( isset( $this->params[ $name ][ $name_language ] ) ) {
							return apply_filters( 'wp_lucky_wheel_params_' . $name . '__' . $name_language, $this->params[ $name ][ $name_language ] );
						} else {
							return apply_filters( 'wp_lucky_wheel_params_' . $name . '__' . $name_language, $this->params[ $name ][ $name_sub ] );
						}
					} else {
						return apply_filters( 'wp_lucky_wheel_params_' . $name . '__' . $name_sub, $this->params[ $name ] [ $name_sub ] );
					}
				} elseif ( $this->default[ $name ] [ $name_sub ] ) {
					return apply_filters( 'wp_lucky_wheel_params_' . $name . '__' . $name_sub, $this->default[ $name ] [ $name_sub ] );
				} else {
					return false;
				}
			} else {
				if ( $language ) {
					$name_language = $name . '_' . $language;
					if ( isset( $this->params[ $name_language ] ) ) {
						return apply_filters( 'wp_lucky_wheel_params_' . $name_language, $this->params[ $name_language ] );
					} else {
						return apply_filters( 'wp_lucky_wheel_params_' . $name_language, $this->params[ $name ] );
					}
				} else {
					return apply_filters( 'wp_lucky_wheel_params_' . $name, $this->params[ $name ] );
				}
			}
		} else {
			return false;
		}
	}

	public function get_default( $name = "", $name_sub = '' ) {
		if ( ! $name ) {
			return $this->default;
		} elseif ( isset( $this->default[ $name ] ) ) {
			if ( $name_sub ) {
				if ( isset( $this->default[ $name ][ $name_sub ] ) ) {
					return apply_filters( 'wp_lucky_wheel_params_default_' . $name . '__' . $name_sub, $this->default[ $name ] [ $name_sub ] );
				} else {
					return false;
				}
			} else {
				return apply_filters( 'wp_lucky_wheel_params_default_' . $name, $this->default[ $name ] );
			}
		} else {
			return false;
		}
	}
}