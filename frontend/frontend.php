<?php

/**
 * Class VI_WP_LUCKY_WHEEL_Frontend_Frontend
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WP_LUCKY_WHEEL_Frontend_Frontend {
	protected $settings;
	protected $is_mobile;
	protected $detect;

	function __construct() {
		$this->settings = VI_WP_LUCKY_WHEEL_DATA::get_instance();
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
		if ( $this->settings->get_params( 'ajax_endpoint' ) === 'ajax' ) {
			add_action( 'wp_ajax_wplwl_get_email', array( $this, 'get_email' ) );
			add_action( 'wp_ajax_nopriv_wplwl_get_email', array( $this, 'get_email' ) );
		} else {
			add_action( 'rest_api_init', array( $this, 'register_api' ) );
		}
	}

	public function get_from_address() {
		return sanitize_email( $this->settings->get_params( 'result', 'email' )['from_address'] );
	}

	public function get_from_name() {
		return wp_specialchars_decode( esc_html( $this->settings->get_params( 'result', 'email' )['from_name'] ), ENT_QUOTES );
	}

	public function get_content_type() {
		return 'text/html';
	}

	/**
	 * @param $user_email
	 * @param $customer_name
	 * @param string $mobile
	 * @param string $value
	 * @param string $label
	 * @param string $language
	 *
	 * @throws Exception
	 */
	public function send_email( $user_email, $customer_name, $mobile = '', $value = '', $label = '', $language = '' ) {
		$label        = str_replace( '/n', ' ', $label );
		$label        = preg_replace( '/ +/', ' ', $label );
		$date_format  = get_option( 'date_format', 'F d, Y' );
		$date         = new DateTime();
		$now          = $date->format( $date_format );
		$email_design = $this->settings->get_params( 'result', 'email' );
		if ( sanitize_email( $email_design['from_address'] ) ) {
			add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		}
		if ( $email_design['from_address'] ) {
			add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		}
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
		$header        = 'Content-Type: text/html; charset=utf-8;';
		$email_temp    = $this->settings->get_params( 'result', 'email', $language );
		$content       = stripslashes( nl2br( $email_temp['content'] ) );
		$content       = str_replace( '{prize_label}', $label, $content );
		$content       = str_replace( '{customer_name}', $customer_name, $content );
		$content       = str_replace( '{customer_mobile}', $mobile, $content );
		$content       = str_replace( '{prize_value}', $value, $content );
		$content       = str_replace( '{today}', $now, $content );
		$subject       = stripslashes( $email_temp['subject'] );
		$email_heading = isset( $email_temp['heading'] ) ? $email_temp['heading'] : '';
		$bg            = isset( $email_design['background_color'] ) ? $email_design['background_color'] : '';
		$body          = isset( $email_design['body_background_color'] ) ? $email_design['body_background_color'] : '';
		$base          = isset( $email_design['base_color'] ) ? $email_design['base_color'] : '';
		$text          = isset( $email_design['body_text_color'] ) ? $email_design['body_text_color'] : '';
		ob_start();
		?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>"/>
            <title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
        </head>
        <body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0"
        marginheight=
        "0" offset="0">
        <div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr' ?>"
             style="background-color: <?php echo esc_attr( $bg ); ?>;
                     margin: 0;
                     padding: 70px 0 70px 0;
                     -webkit-text-size-adjust: none !important;
                     width: 100%;">
            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
                <tr>
                    <td align="center" valign="top">
                        <div id="template_header_image">
							<?php
							if ( $img = isset( $email_design['header_image'] ) ? $email_design['header_image'] : '' ) {
								echo '<p style="margin-top:0;"><img src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" /></p>';
							}
							?>
                        </div>
                        <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container"
                               style="box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
                                       background-color: <?php echo esc_attr( $body ); ?>;
                                       border-radius: 3px !important;">
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Header -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="600"
                                           id="template_header"
                                           style="background-color: <?php echo esc_attr( $base ); ?>;
                                                   border-radius: 3px 3px 0 0 !important;
                                                   border-bottom: 0;
                                                   font-weight: bold;
                                                   line-height: 100%;
                                                   vertical-align: middle;
                                                   font-family: Helvetica, Roboto, Arial, sans-serif;">
                                        <tr>
                                            <td id="header_wrapper" style="padding: 36px 48px;
	display: block;">
                                                <h1><?php echo $email_heading; ?></h1>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Header -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Body -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="600"
                                           id="template_body">
                                        <tr>
                                            <td valign="top" id="body_content"
                                                style="background-color: <?php echo esc_attr( $body ); ?>;">
                                                <!-- Content -->
                                                <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top" style="padding: 48px;">
                                                            <div id="body_content_inner" style="
                                                                    font-family: Helvetica, Roboto, Arial, sans-serif;
                                                                    font-size: 14px;
                                                                    line-height: 150%;
                                                                    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;">
                                                                <div class="text"
                                                                     style="color: <?php echo esc_attr( $text ); ?>;
                                                                             font-family: Helvetica, Roboto, Arial, sans-serif;">
																	<?php
																	echo $content;
																	?>

                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- End Content -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Body -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Footer -->
                                    <table border="0" cellpadding="10" cellspacing="0" width="600"
                                           id="template_footer">
                                        <tr>
                                            <td valign="top">
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td colspan="2" valign="middle" id="credit" style="border:0;
                                                                font-family: Arial;
                                                                font-size:12px;
                                                                line-height:125%;
                                                                text-align:center;
                                                                padding: 0 48px 48px 48px;">
															<?php echo wpautop( wp_kses_post( wptexturize( isset( $email_temp['footer_text'] ) ? $email_temp['footer_text'] : '' ) ) ); ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Footer -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        </body>
        </html>
		<?php
		$content = ob_get_clean();
		wp_mail( $user_email, $subject, $content, $header );
		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
	}

	public function frontend_enqueue() {
		if ( $this->settings->get_params( 'general', 'enable' ) != 'on' ) {
			return;
		}
		$show = true;
		if ( $this->settings->get_params( 'notify', 'show_only_front' ) == 'on' || $this->settings->get_params( 'notify', 'show_only_blog' ) == 'on' || $this->settings->get_params( 'notify', 'show_only_shop' ) == 'on' ) {
			$show = false;
			if ( is_front_page() && $this->settings->get_params( 'notify', 'show_only_front' ) == 'on' ) {
				$show = true;
			}
			if ( is_home() && $this->settings->get_params( 'notify', 'show_only_blog' ) == 'on' ) {
				$show = true;
			}
		}
		if ( ! $show ) {
			return;
		}
		$logic_value = $this->settings->get_params( 'notify', 'conditional_tags' );
		if ( $logic_value ) {
			if ( stristr( $logic_value, "return" ) === false ) {
				$logic_value = "return (" . $logic_value . ");";
			}
			if ( ! eval( $logic_value ) ) {
				return;
			}
		}
		if ( isset( $_COOKIE['wplwl_cookie'] ) ) {
			return;
		}
		$this->detect = new VillaTheme_Mobile_Detect();
		if ( $this->detect->isMobile() && ! $this->detect->isTablet() ) {
			$this->is_mobile = true;
		} else {
			$this->is_mobile = false;
		}
		if ( $this->is_mobile && $this->settings->get_params( 'general', 'mobile' ) != 'on' ) {
			return;
		}
		if ( $this->is_mobile ) {
			wp_enqueue_script( 'wp-lucky-wheel-frontend-javascript', VI_WP_LUCKY_WHEEL_JS . 'wp-lucky-wheel-mobile.js', array( 'jquery' ), VI_WP_LUCKY_WHEEL_VERSION );
		} else {
			wp_enqueue_script( 'wp-lucky-wheel-frontend-javascript', VI_WP_LUCKY_WHEEL_JS . 'wp-lucky-wheel.js', array( 'jquery' ), VI_WP_LUCKY_WHEEL_VERSION );
		}
		$font = '';
		if ( $this->settings->get_params( 'wheel_wrap', 'font' ) ) {
			$font = $this->settings->get_params( 'wheel_wrap', 'font' );
			wp_enqueue_style( 'wp-lucky-wheel-google-font-' . strtolower( str_replace( '+', '-', $font ) ), '//fonts.googleapis.com/css?family=' . $font . ':300,400,700' );
			$font = str_replace( '+', ' ', $font );
		}

		wp_enqueue_style( 'wp-lucky-wheel-frontend-style', VI_WP_LUCKY_WHEEL_CSS . 'wp-lucky-wheel.css', array(), VI_WP_LUCKY_WHEEL_VERSION );

		$inline_css = '.wplwl_lucky_wheel_content {';
		if ( $this->settings->get_params( 'wheel_wrap', 'bg_image' ) ) {
			$bg_image_url = wp_get_attachment_url( $this->settings->get_params( 'wheel_wrap', 'bg_image' ) );
			$inline_css   .= 'background-image:url("' . $bg_image_url . '");background-repeat: no-repeat;background-size:cover;background-position:center;';
		}
		if ( $this->settings->get_params( 'wheel_wrap', 'bg_color' ) ) {
			$inline_css .= 'background-color:' . $this->settings->get_params( 'wheel_wrap', 'bg_color' ) . ';';
		}
		if ( $this->settings->get_params( 'wheel_wrap', 'text_color' ) ) {
			$inline_css .= 'color:' . $this->settings->get_params( 'wheel_wrap', 'text_color' ) . ';';
		}
		$inline_css .= '}';
		if ( 'on' == $this->settings->get_params( 'wheel', 'show_full_wheel' ) ) {
			$inline_css .= '.wplwl_lucky_wheel_content .wheel_content_left{margin-left:0 !important;}';
			$inline_css .= '.wplwl_lucky_wheel_content .wheel_content_right{width:48% !important;}';
//			$inline_css .= '.wplwl_lucky_wheel_content .wheel_content_right .wplwl_user_lucky{max-width:300px !important;}';
		}
		$inline_css .= '.wplwl_wheel_icon{';
		switch ( $this->settings->get_params( 'notify', 'position' ) ) {
			case 'top-left':
				$inline_css .= 'top:15px;left:0;margin-left: -100%;';
				break;
			case 'top-right':
				$inline_css .= 'top:15px;right:0;margin-right: -100%;';
				break;
			case 'bottom-left':
				$inline_css .= 'bottom:5px;left:5px;margin-left: -100%;';
				break;
			case 'bottom-right':
				$inline_css .= 'bottom:5px;right:5px;margin-right: -100%;';
				break;
			case 'middle-left':
				$inline_css .= 'bottom:45%;left:0;margin-left: -100%;';
				break;
			case 'middle-right':
				$inline_css .= 'bottom:45%;right:0;margin-right: -100%;';
				break;
		}
		$inline_css .= '}';

		if ( $this->settings->get_params( 'wheel_wrap', 'pointer_color' ) ) {
			$inline_css .= '.wplwl_pointer:before{color:' . $this->settings->get_params( 'wheel_wrap', 'pointer_color' ) . ';}';
		}
		//wheel wrap design
		$inline_css .= '.wheel_content_right>.wplwl_user_lucky>.wplwl_spin_button{';
		if ( $this->settings->get_params( 'wheel_wrap', 'spin_button_color' ) ) {
			$inline_css .= 'color:' . $this->settings->get_params( 'wheel_wrap', 'spin_button_color' ) . ';';
		}

		if ( $this->settings->get_params( 'wheel_wrap', 'spin_button_bg_color' ) ) {
			$inline_css .= 'background-color:' . $this->settings->get_params( 'wheel_wrap', 'spin_button_bg_color' ) . ';';
		}
		$inline_css .= '}';
		if ( $font ) {
			$inline_css .= '.wplwl_lucky_wheel_content .wheel-content-wrapper .wheel_content_right,.wplwl_lucky_wheel_content .wheel-content-wrapper .wheel_content_right input,.wplwl_lucky_wheel_content .wheel-content-wrapper .wheel_content_right span,.wplwl_lucky_wheel_content .wheel-content-wrapper .wheel_content_right a,.wplwl_lucky_wheel_content .wheel-content-wrapper .wheel_content_right .wplwl-frontend-result{font-family:' . $font . ' !important;}';
		}
		$inline_css .= $this->settings->get_params( 'wheel_wrap', 'custom_css' );
		wp_add_inline_style( 'wp-lucky-wheel-frontend-style', $inline_css );
		$wheel         = $this->settings->get_params( 'wheel' );
		$time_if_close = $this->settings->get_params( 'notify', 'time_on_close' );
		if ( $this->settings->get_params( 'notify', 'time_on_close_unit' ) ) {
			switch ( $this->settings->get_params( 'notify', 'time_on_close_unit' ) ) {
				case 'm':
					$time_if_close *= 60;
					break;
				case 'h':
					$time_if_close *= 3600;
					break;
				case 'd':
					$time_if_close *= 86400;
					break;
				default:
			}
		}
		$intent = $this->settings->get_params( 'notify', 'intent' );
		if ( $intent == 'random' ) {
			$ran = rand( 1, 4 );
			switch ( $ran ) {
				case 1:
					$intent = 'popup_icon';
					break;
				case 2:
					$intent = 'show_wheel';
					break;
				case 3:
					$intent = 'on_scroll';
					break;
				case 4:
					$intent = 'on_exit';
					break;
			}
		}
		$limit_time_warning = esc_html__( 'You have to wait until your next spin.', 'wp-lucky-wheel' );
		switch ( $this->settings->get_params( 'notify', 'show_again_unit' ) ) {
			case 's':
				$limit_time_warning = sprintf( esc_html__( 'You can only spin 1 time every %s seconds', 'wp-lucky-wheel' ), $this->settings->get_params( 'notify', 'show_again' ) );
				break;
			case 'm':
				$limit_time_warning = sprintf( esc_html__( 'You can only spin 1 time every %s minutes', 'wp-lucky-wheel' ), $this->settings->get_params( 'notify', 'show_again' ) );
				break;
			case 'h':
				$limit_time_warning = sprintf( esc_html__( 'You can only spin 1 time every %s hours', 'wp-lucky-wheel' ), $this->settings->get_params( 'notify', 'show_again' ) );
				break;
			case 'd':
				$limit_time_warning = sprintf( esc_html__( 'You can only spin 1 time every %s days', 'wp-lucky-wheel' ), $this->settings->get_params( 'notify', 'show_again' ) );
				break;

		}
		wp_localize_script( 'wp-lucky-wheel-frontend-javascript', '_wplwl_get_email_params', array(
			'ajaxurl'            => $this->settings->get_params( 'ajax_endpoint' ) == 'ajax' ? ( admin_url( 'admin-ajax.php?action=wplwl_get_email' ) ) : site_url() . '/wp-json/wordpress_lucky_wheel/spin',
			'pointer_position'   => 'center',
			'wheel_dot_color'    => '#000000',
			'wheel_border_color' => '#ffffff',
			'wheel_center_color' => $this->settings->get_params( 'wheel_wrap', 'wheel_center_color' ),
			'gdpr'               => $this->settings->get_params( 'wheel_wrap', 'gdpr' ),
			'gdpr_warning'       => esc_html__( 'Please agree with our term and condition.', 'wp-lucky-wheel' ),

			'position'        => $this->settings->get_params( 'notify', 'position' ),
			'show_again'      => $this->settings->get_params( 'notify', 'show_again' ),
			'scroll_amount'   => $this->settings->get_params( 'notify', 'scroll_amount' ),
			'show_again_unit' => $this->settings->get_params( 'notify', 'show_again_unit' ),
			'intent'          => $intent,
			'hide_popup'      => $this->settings->get_params( 'notify', 'hide_popup' ),

			'slice_text_color'                => ( isset( $wheel['slice_text_color'] ) && $wheel['slice_text_color'] ) ? $wheel['slice_text_color'] : '#fff',
			'bg_color'                        => $this->settings->get_params( 'wheel', 'random_color' ) == 'on' ? $this->get_random_color() : $wheel['bg_color'],
			'slices_text_color'               => $this->settings->get_params( 'wheel', 'slices_text_color' ),
			'label'                           => $wheel['custom_label'],
			'prize_type'                      => $wheel['prize_type'],
			'spinning_time'                   => 8,
			'wheel_speed'                     => 5,
			'auto_close'                      => $this->settings->get_params( 'result', 'auto_close' ),
			'show_wheel'                      => wplwl_get_explode( ',', $this->settings->get_params( 'notify', 'show_wheel' ) ),
			'time_if_close'                   => $time_if_close,
			'empty_email_warning'             => esc_html__( '*Please enter your email', 'wp-lucky-wheel' ),
			'invalid_email_warning'           => esc_html__( '*Please enter a valid email address', 'wp-lucky-wheel' ),
			'limit_time_warning'              => $limit_time_warning,
			'custom_field_name_enable'        => $this->settings->get_params( 'custom_field_name_enable' ),
			'custom_field_name_enable_mobile' => $this->settings->get_params( 'custom_field_name_enable_mobile' ),
			'custom_field_name_required'      => $this->settings->get_params( 'custom_field_name_required' ),
			'custom_field_name_message'       => esc_html__( 'Name is required!', 'wp-lucky-wheel' ),
			'show_full_wheel'                 => $this->settings->get_params( 'wheel', 'show_full_wheel' ),
			'font_size'                       => 100,
			'wheel_size'                      => 100,
			'is_mobile'                       => $this->is_mobile,
			'congratulations_effect'          => '',
			'images_dir'                      => VI_WP_LUCKY_WHEEL_IMAGES,
			'language'                        => '',
		) );
		add_action( 'wp_footer', array( $this, 'draw_wheel' ) );
	}

	/**
	 * Register API json
	 */
	public function register_api() {
		register_rest_route(
			'wordpress_lucky_wheel', '/spin', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_email' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function draw_wheel() {

		if ( isset( $_COOKIE['wplwl_cookie'] ) ) {
			return;
		}
		if ( $this->is_mobile && $this->settings->get_params( 'general', 'mobile' ) != 'on' ) {
			return;
		}
		$spin_button = $this->settings->get_params( 'wheel_wrap', 'spin_button' );
		if ( empty( $spin_button ) ) {
			$spin_button = esc_html__( 'Try Your Lucky', 'wp-lucky-wheel' );
		}
		wp_nonce_field( 'wordpress_lucky_wheel_nonce_action', '_wordpress_lucky_wheel_nonce' );
		?>
        <div class="wplwl-overlay"></div>
		<?php
		if ( $this->is_mobile ) {
			?>
            <div class="wplwl_lucky_wheel_content wplwl_lucky_wheel_content_mobile">
            <div class="wheel-content-wrapper">

                <div class="wheel_content_right">

                    <div class="wheel_description">
						<?php echo do_shortcode( $this->settings->get_params( 'wheel_wrap', 'description' ) ); ?>
                    </div>
                    <div class="wplwl_user_lucky">
						<?php
						if ( 'on' == $this->settings->get_params( 'custom_field_name_enable' ) && 'on' == $this->settings->get_params( 'custom_field_name_enable_mobile' ) ) {
							?>
                            <div class="wplwl_field_name_wrap">
                                <span id="wplwl_error_name"></span>
                                <input type="text" class="wplwl_field_input wplwl_field_name" name="wplwl_player_name"
                                       placeholder="<?php esc_html_e( 'Please enter your name', 'wp-lucky-wheel' ) ?>"
                                       id="wplwl_player_name">
                            </div>
							<?php
						}
						if ( 'on' == $this->settings->get_params( 'custom_field_mobile_enable' ) && 'on' == $this->settings->get_params( 'custom_field_mobile_enable_mobile' ) ) {
							?>
                            <div class="wplwl_field_mobile_wrap">
                                <span id="wplwl_error_mobile"></span>
                                <input type="tel" class="wplwl_field_input wplwl_field_mobile"
                                       name="wplwl_player_mobile"
                                       placeholder="<?php esc_html_e( 'Please enter your mobile', 'wp-lucky-wheel' ) ?>"
                                       id="wplwl_player_mobile">
                            </div>
							<?php
						}
						?>
                        <div class="wplwl_field_email_wrap">
                            <span id="wplwl_error_mail"></span>
                            <input type="email" class="wplwl_field_input wplwl_field_email" name="wplwl_player_mail"
                                   placeholder="<?php esc_html_e( 'Please enter your email', 'wp-lucky-wheel' ) ?>"
                                   id="wplwl_player_mail">
                        </div>
                        <span class="wplwl_chek_mail wplwl_spin_button button-primary"
                              id="wplwl_chek_mail"><?php echo $spin_button ?></span>
						<?php
						if ( 'on' == $this->settings->get_params( 'wheel_wrap', 'gdpr' ) ) {
							$gdpr_message = $this->settings->get_params( 'wheel_wrap', 'gdpr_message' );
							if ( empty( $gdpr_message ) ) {
								$gdpr_message = esc_html__( 'I agree with the term and condition', 'wp-lucky-wheel' );
							}
							?>
                            <div class="wplwl-gdpr-checkbox-wrap">
                                <input type="checkbox">
                                <span><?php echo $gdpr_message ?></span>
                            </div>
							<?php
						}
						if ( 'on' === $this->settings->get_params( 'wheel_wrap', 'close_option' ) ) {
							?>
                            <div class="wplwl-show-again-option">
                                <div class="wplwl-never-again">
                                    <span><?php esc_html_e( 'Never', 'wp-lucky-wheel' ); ?></span>
                                </div>
                                <div class="wplwl-reminder-later">
                                    <span class="wplwl-reminder-later-a"><?php esc_html_e( 'Remind later', 'wp-lucky-wheel' ); ?></span>
                                </div>
                                <div class="wplwl-close">
                                    <span><?php esc_html_e( 'No thanks', 'wp-lucky-wheel' ); ?></span>
                                </div>
                            </div>
							<?php
						}
						?>
                    </div>
                    <div class="wheel_content_left">
                        <div class="wplwl-frontend-result"></div>
                        <div class="wplwl_wheel_spin">
                            <canvas id="wplwl_canvas">
                            </canvas>
                            <canvas id="wplwl_canvas1">
                            </canvas>
                            <canvas id="wplwl_canvas2">
                            </canvas>
                            <div class="wplwl_wheel_spin_container">
                                <div class="wplwl_pointer_before"></div>
                                <div class="wplwl_pointer_content">
                                    <span class="wplwl-location wplwl_pointer"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
			<?php
		} else {
			?>
            <div class="wplwl_lucky_wheel_content <?php
			if ( $this->detect->isTablet() ) {
				echo 'lucky_wheel_content_tablet ';
			}
			?>">
            <div class="wheel-content-wrapper">
                <div class="wheel_content_left">
                    <div class="wplwl_wheel_spin">
                        <canvas id="wplwl_canvas">
                        </canvas>
                        <canvas id="wplwl_canvas1">
                        </canvas>
                        <canvas id="wplwl_canvas2">
                        </canvas>
                        <div class="wplwl_wheel_spin_container">
                            <div class="wplwl_pointer_before"></div>
                            <div class="wplwl_pointer_content">
                                <span class="wplwl-location wplwl_pointer"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wheel_content_right">

                    <div class="wheel_description">
						<?php echo do_shortcode( $this->settings->get_params( 'wheel_wrap', 'description' ) ); ?>
                    </div>
                    <div class="wplwl_user_lucky">
						<?php
						if ( 'on' == $this->settings->get_params( 'custom_field_name_enable' ) ) {
							?>
                            <div class="wplwl_field_name_wrap">
                                <span id="wplwl_error_name"></span>
                                <input type="text" class="wplwl_field_input wplwl_field_name" name="wplwl_player_name"
                                       placeholder="<?php esc_html_e( 'Please enter your name', 'wp-lucky-wheel' ) ?>"
                                       id="wplwl_player_name">
                            </div>
							<?php
						}
						if ( 'on' == $this->settings->get_params( 'custom_field_mobile_enable' ) ) {
							?>
                            <div class="wplwl_field_mobile_wrap">
                                <span id="wplwl_error_mobile"></span>
                                <input type="tel" class="wplwl_field_input wplwl_field_mobile"
                                       name="wplwl_player_mobile"
                                       placeholder="<?php esc_html_e( 'Please enter your mobile', 'wp-lucky-wheel' ) ?>"
                                       id="wplwl_player_mobile">
                            </div>
							<?php
						}
						?>
                        <div class="wplwl_field_email_wrap">
                            <span id="wplwl_error_mail"></span>
                            <input type="email" class="wplwl_field_input wplwl_field_email" name="wplwl_player_mail"
                                   placeholder="<?php esc_html_e( 'Please enter your email', 'wp-lucky-wheel' ) ?>"
                                   id="wplwl_player_mail">
                        </div>
                        <span class="wplwl_chek_mail wplwl_spin_button button-primary"
                              id="wplwl_chek_mail"><?php echo $spin_button ?></span>
						<?php
						if ( 'on' == $this->settings->get_params( 'wheel_wrap', 'gdpr' ) ) {
							$gdpr_message = $this->settings->get_params( 'wheel_wrap', 'gdpr_message' );
							if ( empty( $gdpr_message ) ) {
								$gdpr_message = esc_html__( 'I agree with the term and condition', 'wp-lucky-wheel' );
							}
							?>
                            <div class="wplwl-gdpr-checkbox-wrap">
                                <input type="checkbox">
                                <span><?php echo $gdpr_message ?></span>
                            </div>
							<?php
						}
						if ( 'on' === $this->settings->get_params( 'wheel_wrap', 'close_option' ) ) {
							?>
                            <div class="wplwl-show-again-option">
                                <div class="wplwl-never-again">
                                    <span><?php esc_html_e( 'Never', 'wp-lucky-wheel' ); ?></span>
                                </div>
                                <div class="wplwl-reminder-later">
                                    <span class="wplwl-reminder-later-a"><?php esc_html_e( 'Remind later', 'wp-lucky-wheel' ); ?></span>
                                </div>
                                <div class="wplwl-close">
                                    <span><?php esc_html_e( 'No thanks', 'wp-lucky-wheel' ); ?></span>
                                </div>
                            </div>
							<?php
						}
						?>
                    </div>
                </div>
            </div>
			<?php
		}
		?>
        <div class="wplwl-close-wheel"><span class="wplwl-cancel"></span></div>

        <div class="wplwl-hide-after-spin">
            <span class="wplwl-cancel">
            </span>
        </div>
        </div>
		<?php
		$wheel_icon_class = 'wplwl_wheel_icon wp-lucky-wheel-popup-icon wplwl-wheel-position-' . $this->settings->get_params( 'notify', 'position' );
		?>
        <canvas id="wplwl_popup_canvas" class="<?php echo esc_attr( $wheel_icon_class ) ?>" width="64"
                height="64"></canvas>
		<?php
	}

	public function get_email() {
		if ( $this->settings->get_params( 'ajax_endpoint' ) === 'rest_api' ) {
			header( "Access-Control-Allow-Origin: *" );
			header( 'Access-Control-Allow-Methods: POST' );
		}
		$language = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : '';
		$email    = isset( $_POST['user_email'] ) ? sanitize_email( strtolower( $_POST['user_email'] ) ) : '';
		$name     = ( isset( $_POST['user_name'] ) && $_POST['user_name'] ) ? sanitize_text_field( $_POST['user_name'] ) : 'Sir/Madam';
		if ( ! $email || ! is_email( $email ) ) {
			wp_send_json(
				array(
					'allow_spin' => esc_html__( 'Email is invalid', 'wp-lucky-wheel' ),
				)
			);
		}
		if ( ! $name && 'on' == $this->settings->get_params( 'custom_field_name_required' ) ) {
			wp_send_json(
				array(
					'allow_spin' => esc_html__( 'Name is required', 'wp-lucky-wheel' ),
				)
			);
		}
		$date_format = get_option( 'date_format', 'F d, Y' );
		$date        = new DateTime();
		$today       = $date->format( $date_format );
		$allow       = 'no';
		$email_delay = $this->settings->get_params( 'general', 'delay' );
		switch ( $this->settings->get_params( 'general', 'delay_unit' ) ) {
			case 'm':
				$email_delay *= 60;
				break;
			case 'h':
				$email_delay *= 60 * 60;
				break;
			case 'd':
				$email_delay *= 60 * 60 * 24;
				break;
			default:
		}
		$stop                = - 1;
		$result              = 'lost';
		$result_notification = $this->settings->get_params( 'result', 'notification', $language )['lost'];
		$now                 = time();
		$wheel               = $this->settings->get_params( 'wheel' );
		$weigh               = $wheel['probability'];
		if ( $this->settings->get_params( 'general', 'enable' ) != 'on' ) {
			$allow = 'Wrong email.';
			$data  = array( 'allow_spin' => $allow );
			wp_send_json( $data );
		}
		$trash_email = new WP_Query( array(
			'post_type'      => 'wplwl_email',
			'posts_per_page' => - 1,
			'title'          => $email,
			'post_status'    => array( // (string | array) - use post status. Retrieves posts by Post Status, default value i'publish'.
				'trash', // - post is in trashbin (available with Version 2.9).
			)
		) );
		if ( $trash_email->have_posts() ) {
			$allow = esc_html__( 'Sorry, this email is marked as spam now. Please enter another email to continue.', 'wp-lucky-wheel' );
			wp_reset_postdata();
			$data = array( 'allow_spin' => $allow );
			wp_send_json( $data );
			die;
		}
		$wplwl_emails_args = array(
			'post_type'      => 'wplwl_email',
			'posts_per_page' => - 1,
			'title'          => $email,
			'post_status'    => array( // (string | array) - use post status. Retrieves posts by Post Status, default value i'publish'.
				'publish', // - a published post or page.
				'pending', // - post is pending review.
				'draft',  // - a post in draft status.
				'auto-draft', // - a newly created post, with no content.
				'future', // - a post to publish in the future.
				'private', // - not visible to users who are not logged in.
				'inherit', // - a revision. see get_children.
				'trash', // - post is in trashbin (available with Version 2.9).
			)
		);
		$the_query         = new WP_Query( $wplwl_emails_args );
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$email_id                  = get_the_ID();
				$post_data                 = (array) get_post();
				$post_data['post_content'] = $name;
				wp_update_post( $post_data );
				$spin_meta = get_post_meta( $email_id, 'wplwl_spin_times', true );
				if ( $spin_meta['spin_num'] >= $this->settings->get_params( 'general', 'spin_num' ) ) {
					$allow = esc_html__( 'This email has reach the maximum spins.', 'wp-lucky-wheel' );
				} elseif ( ( $now - $spin_meta['last_spin'] ) < $email_delay ) {
					$wait      = $email_delay + $spin_meta['last_spin'] - $now;
					$wait_day  = floor( $wait / 86400 );
					$wait_hour = floor( ( $wait - $wait_day * 86400 ) / 3600 );
					$wait_min  = floor( ( $wait - $wait_day * 86400 - $wait_hour * 3600 ) / 60 );
					$wait_sec  = $wait - $wait_day * 86400 - $wait_hour * 3600 - $wait_min * 60;

					$wait_return = $wait_sec . esc_html__( ' seconds', 'wp-lucky-wheel' );
					if ( $wait_day ) {
						$wait_return = sprintf( esc_html__( '%s days %s hours %s minutes %s seconds', 'wp-lucky-wheel' ), $wait_day, $wait_hour, $wait_min, $wait_sec );
					} elseif ( $wait_hour ) {
						$wait_return = sprintf( esc_html__( '%s hours %s minutes %s seconds', 'wp-lucky-wheel' ), $wait_hour, $wait_min, $wait_sec );
					} elseif ( $wait_min ) {
						$wait_return = sprintf( esc_html__( '%s minutes %s seconds', 'wp-lucky-wheel' ), $wait_min, $wait_sec );
					}
					$allow = esc_html__( 'You have to wait ', 'wp-lucky-wheel' ) . ( $wait_return ) . esc_html__( ' to be able to spin again.', 'wp-lucky-wheel' );
				} else {
					$allow = 'yes';
					$spin_meta['spin_num'] ++;
					update_post_meta(
						$email_id, 'wplwl_spin_times', array(
							'spin_num'  => $spin_meta['spin_num'],
							'last_spin' => $now,
							'gdpr'      => 1
						)
					);
					for ( $i = 1; $i < sizeof( $weigh ); $i ++ ) {
						$weigh[ $i ] += $weigh[ $i - 1 ];
					}
					for ( $i = 0; $i < sizeof( $weigh ); $i ++ ) {
						if ( $wheel['probability'] == 0 ) {
							$weigh[ $i ] = 0;
						}
					}
					$random = rand( 1, 100 );
					$stop   = 0;
					foreach ( $weigh as $v ) {
						if ( $random <= $v ) {
							break;
						}
						$stop ++;
					}
					if ( $wheel['prize_type'][ $stop ] != 'non' ) {
						$result              = 'win';
						$result_notification = $this->settings->get_params( 'result', 'notification', $language )['win'];
						$wheel_label         = $wheel['custom_label'][ $stop ];

						$code = $wheel['custom_value'][ $stop ];
						$this->send_email( $email, $name, '', $code, $wheel_label, $language );
						$email_coupons   = is_array( get_post_meta( $email_id, 'wplwl_email_coupons', true ) ) ? get_post_meta( $email_id, 'wplwl_email_coupons', true ) : array();
						$email_coupons[] = $code;
						update_post_meta( $email_id, 'wplwl_email_coupons', $email_coupons );
						$email_labels   = is_array( get_post_meta( $email_id, 'wplwl_email_labels', true ) ) ? get_post_meta( $email_id, 'wplwl_email_labels', true ) : array();
						$email_labels[] = $wheel_label;
						update_post_meta( $email_id, 'wplwl_email_labels', $email_labels );
						$result_notification = str_replace( '{prize_value}', '<strong>' . $code . '</strong>', $result_notification );
						$result_notification = str_replace( '{prize_label}', '<strong>' . $wheel_label . '</strong>', $result_notification );
						$result_notification = str_replace( '{customer_name}', '<strong>' . ( isset( $_POST['user_name'] ) ? $_POST['user_name'] : '' ) . '</strong>', $result_notification );
						$result_notification = str_replace( '{customer_email}', '<strong>' . $email . '</strong>', $result_notification );
						$result_notification = str_replace( '{today}', '<strong>' . $today . '</strong>', $result_notification );
					}
				}
			}
			wp_reset_postdata();
		} else {
			$allow = 'yes';
			//save email
			$email_id = wp_insert_post(
				array(
					'post_title'   => $email,
					'post_name'    => $email,
					'post_content' => $name,
					'post_author'  => 1,
					'post_status'  => 'publish',
					'post_type'    => 'wplwl_email',
				)
			);
			update_post_meta( $email_id, 'wplwl_spin_times', array(
				'spin_num'  => 1,
				'last_spin' => $now,
				'gdpr'      => 1
			) );
			//get stop position
			for ( $i = 1; $i < sizeof( $weigh ); $i ++ ) {
				$weigh[ $i ] += $weigh[ $i - 1 ];
			}
			for ( $i = 0; $i < sizeof( $weigh ); $i ++ ) {
				if ( $wheel['probability'] == 0 ) {
					$weigh[ $i ] = 0;
				}
			}
			$random = rand( 1, 100 );
			$stop   = 0;
			foreach ( $weigh as $v ) {
				if ( $random <= $v ) {
					break;
				}
				$stop ++;
			}
			$email_coupons = array();
			$email_labels  = array();
			$wheel_label   = $wheel['custom_label'][ $stop ];

			if ( $wheel['prize_type'][ $stop ] != 'non' ) {
				$result = 'win';

				$result_notification = $this->settings->get_params( 'result', 'notification', $language )['win'];

				$code = $wheel['custom_value'][ $stop ];
				$this->send_email( $email, $name, '', $code, $wheel_label, $language );
				$email_coupons[] = $code;
				update_post_meta( $email_id, 'wplwl_email_coupons', $email_coupons );
				$email_labels[] = $wheel_label;
				update_post_meta( $email_id, 'wplwl_email_labels', $email_labels );
				$result_notification = str_replace( '{prize_value}', '<strong>' . $code . '</strong>', $result_notification );
				$result_notification = str_replace( '{prize_label}', '<strong>' . $wheel_label . '</strong>', $result_notification );
				$result_notification = str_replace( '{customer_name}', '<strong>' . ( isset( $_POST['user_name'] ) ? $_POST['user_name'] : '' ) . '</strong>', $result_notification );
				$result_notification = str_replace( '{customer_email}', '<strong>' . $email . '</strong>', $result_notification );
				$result_notification = str_replace( '{today}', '<strong>' . $today . '</strong>', $result_notification );
			}
		}

		$data = array(
			'allow_spin'          => $allow,
			'stop_position'       => $stop,
			'result_notification' => do_shortcode( $result_notification ),
			'result'              => $result,
		);
		wp_send_json( $data );
	}

	public function get_random_color() {
		$colors_array = array(
			array(
				"#ffcdd2",
				"#b71c1c",
				"#e57373",
				"#e53935",
				"#ffcdd2",
				"#b71c1c",
				"#e57373",
				"#e53935",
				"#ffcdd2",
				"#b71c1c",
				"#e57373",
				"#e53935",
				"#ffcdd2",
				"#b71c1c",
				"#e57373",
				"#e53935",
				"#ffcdd2",
				"#b71c1c",
				"#e57373",
				"#e53935",
			),
			array(
				"#e1bee7",
				"#4a148c",
				"#ba68c8",
				"#8e24aa",
				"#e1bee7",
				"#4a148c",
				"#ba68c8",
				"#8e24aa",
				"#e1bee7",
				"#4a148c",
				"#ba68c8",
				"#8e24aa",
				"#e1bee7",
				"#4a148c",
				"#ba68c8",
				"#8e24aa",
				"#e1bee7",
				"#4a148c",
				"#ba68c8",
				"#8e24aa",
			),
			array(
				"#d1c4e9",
				"#311b92",
				"#9575cd",
				"#5e35b1",
				"#d1c4e9",
				"#311b92",
				"#9575cd",
				"#5e35b1",
				"#d1c4e9",
				"#311b92",
				"#9575cd",
				"#5e35b1",
				"#d1c4e9",
				"#311b92",
				"#9575cd",
				"#5e35b1",
				"#d1c4e9",
				"#311b92",
				"#9575cd",
				"#5e35b1",
			),
			array(
				"#c5cae9",
				"#1a237e",
				"#7986cb",
				"#3949ab",
				"#c5cae9",
				"#1a237e",
				"#7986cb",
				"#3949ab",
				"#c5cae9",
				"#1a237e",
				"#7986cb",
				"#3949ab",
				"#c5cae9",
				"#1a237e",
				"#7986cb",
				"#3949ab",
				"#c5cae9",
				"#1a237e",
				"#7986cb",
				"#3949ab",
			),
			array(
				"#bbdefb",
				"#64b5f6",
				"#1e88e5",
				"#0d47a1",
				"#bbdefb",
				"#64b5f6",
				"#1e88e5",
				"#0d47a1",
				"#bbdefb",
				"#64b5f6",
				"#1e88e5",
				"#0d47a1",
				"#bbdefb",
				"#64b5f6",
				"#1e88e5",
				"#0d47a1",
				"#bbdefb",
				"#64b5f6",
				"#1e88e5",
				"#0d47a1",
			),
			array(
				"#b2dfdb",
				"#004d40",
				"#4db6ac",
				"#00897b",
				"#b2dfdb",
				"#004d40",
				"#4db6ac",
				"#00897b",
				"#b2dfdb",
				"#004d40",
				"#4db6ac",
				"#00897b",
				"#b2dfdb",
				"#004d40",
				"#4db6ac",
				"#00897b",
				"#b2dfdb",
				"#004d40",
				"#4db6ac",
				"#00897b",
			),
			array(
				"#c8e6c9",
				"#1b5e20",
				"#81c784",
				"#43a047",
				"#c8e6c9",
				"#1b5e20",
				"#81c784",
				"#43a047",
				"#c8e6c9",
				"#1b5e20",
				"#81c784",
				"#43a047",
				"#c8e6c9",
				"#1b5e20",
				"#81c784",
				"#43a047",
				"#c8e6c9",
				"#1b5e20",
				"#81c784",
				"#43a047",
			),
			array(
				"#f0f4c3",
				"#827717",
				"#dce775",
				"#c0ca33",
				"#f0f4c3",
				"#827717",
				"#dce775",
				"#c0ca33",
				"#f0f4c3",
				"#827717",
				"#dce775",
				"#c0ca33",
				"#f0f4c3",
				"#827717",
				"#dce775",
				"#c0ca33",
				"#f0f4c3",
				"#827717",
				"#dce775",
				"#c0ca33",
			),
			array(
				"#fff9c4",
				"#f57f17",
				"#fff176",
				"#fdd835",
				"#fff9c4",
				"#f57f17",
				"#fff176",
				"#fdd835",
				"#fff9c4",
				"#f57f17",
				"#fff176",
				"#fdd835",
				"#fff9c4",
				"#f57f17",
				"#fff176",
				"#fdd835",
				"#fff9c4",
				"#f57f17",
				"#fff176",
				"#fdd835",
			),
			array(
				"#ffe0b2",
				"#e65100",
				"#ffb74d",
				"#fb8c00",
				"#ffe0b2",
				"#e65100",
				"#ffb74d",
				"#fb8c00",
				"#ffe0b2",
				"#e65100",
				"#ffb74d",
				"#fb8c00",
				"#ffe0b2",
				"#e65100",
				"#ffb74d",
				"#fb8c00",
				"#ffe0b2",
				"#e65100",
				"#ffb74d",
				"#fb8c00",
			),
			array(
				"#d7ccc8",
				"#3e2723",
				"#a1887f",
				"#6d4c41",
				"#d7ccc8",
				"#3e2723",
				"#a1887f",
				"#6d4c41",
				"#d7ccc8",
				"#3e2723",
				"#a1887f",
				"#6d4c41",
				"#d7ccc8",
				"#3e2723",
				"#a1887f",
				"#6d4c41",
				"#d7ccc8",
				"#3e2723",
				"#a1887f",
				"#6d4c41",
			),
			array(
				"#cfd8dc",
				"#263238",
				"#90a4ae",
				"#546e7a",
				"#cfd8dc",
				"#263238",
				"#90a4ae",
				"#546e7a",
				"#cfd8dc",
				"#263238",
				"#90a4ae",
				"#546e7a",
				"#cfd8dc",
				"#263238",
				"#90a4ae",
				"#546e7a",
				"#cfd8dc",
				"#263238",
				"#90a4ae",
				"#546e7a",
			),
		);
		$index        = rand( 0, 11 );
		$colors       = $colors_array[ $index ];
		$slices       = $this->settings->get_params( 'wheel', 'bg_color' );

		return array_slice( $colors, 0, count( $slices ) );
	}
}