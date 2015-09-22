<?php

if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

require_once PATH_THIRD . 'mobile_detect/config.php';

/**
 *  MX Mobile Detect Class for ExpressionEngine2
 *
 * @package  ExpressionEngine
 * @subpackage Plugins
 * @category Plugins
 * @author    Max Lazar <max@eec.ms>
 *
 */

$plugin_info = array(
	'pi_name'        => MX_MOBILE_DETECT_NAME,
	'pi_version'     => MX_MOBILE_DETECT_VER,
	'pi_author'      => MX_MOBILE_DETECT_AUTHOR,
	'pi_author_url'  => MX_MOBILE_DETECT_DOCS,
	'pi_description' => MX_MOBILE_DETECT_DESC,
	'pi_usage'       => mobile_detect::usage()
);


class Mobile_detect {
	var $return_data = "";
	var $location = '';
	var $conds = array ();
	var $cache;
	var $enable;
	var $cookie_value;
	var $redirect;
	var $cookie_name;
	var $client_request;
	var $ignore_cookies;
	var $refresh;
	var $agent;
	var $cookie_expire = 86500;

	/**
	 * Mobile_detect function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->cache =& ee()->session->cache[__CLASS__];

		ee()->load->helper( 'url' );

		$uri_string = ee()->uri->uri_string();

		$this->location = ( !ee()->TMPL->fetch_param( 'location' ) ) ? false : str_replace( "{uri}", ee()->uri->uri_string(), ee()->TMPL->fetch_param( 'location' ) );
		$this->redirect   = ( !ee()->TMPL->fetch_param( 'redirect' ) ) ? ( 'mobile' ) : ee()->TMPL->fetch_param( 'redirect' );
		$this->client_request = ( !ee()->TMPL->fetch_param( 'client' ) ) ? false : ee()->TMPL->fetch_param( 'client' );
		$this->cookie_name = ( !ee()->TMPL->fetch_param( 'cookie_name' ) ) ? 'mobile_redirect' : ee()->TMPL->fetch_param( 'cookie_name' );
		$this->cookie_value  = ( !ee()->TMPL->fetch_param( 'cookie_value' ) ) ? 'on' : ee()->TMPL->fetch_param( 'cookie_value' );
		$this->enable   = ( !ee()->TMPL->fetch_param( 'enable' ) ) ? false : ( ( ee()->TMPL->fetch_param( 'enable' ) == 'yes' ) ? 'on' : 'off' );
		$this->ignore_cookies = ( !ee()->TMPL->fetch_param( 'ignore_cookies' ) ) ? false :
		( ( ee()->TMPL->fetch_param( 'ignore_cookies' ) == 'yes' ) ? true : false );
		$this->refresh = ( !ee()->TMPL->fetch_param( 'refresh' ) ) ? false :
		( ( ee()->TMPL->fetch_param( 'refresh' ) == 'yes' ) ? true : false );

		$this->device_detect( $this->refresh );

		$this->conds['mobile'] = ( $this->client_request ) ? ( ( $this->_is()) ? true : false ) :
		$this->cache['mx_mobile_device'];
		$this->conds['not_mobile'] = ( $this->conds['mobile'] !== FALSE ) ? FALSE : true;
		$this->conds['device']  = ( $this->conds['mobile'] ) ? $this->cache['mx_mobile_device'] : 'not_mobile';

		return;
	}


	/**
	 * pair function.
	 *
	 * @access public
	 * @return void
	 */
	public function pair() {
		$tagdata = ee()->TMPL->tagdata;
		$tagdata = ee()->functions->prep_conditionals( $tagdata, $this->conds );
		$tagdata = ee()->TMPL->parse_variables_row( $tagdata,  $this->conds['mobile'] );
		return $this->return_data = $tagdata;
	}

	/**
	 * screen_detect function.
	 *
	 * @access public
	 * @return void
	 */
	public function screen_detect() {
		$r = '';
		if ( ee()->input->cookie( 'screen_width', false ) === FALSE ) {
			$r = '<script language="javascript">
	        		ScreenDetect();
	        		function ScreenDetect()
	        		{
	        			dpr = 1;

		                if( window.devicePixelRatio !== undefined ){
		                   dpr = window.devicePixelRatio;
		                }

		                var screen_r =  getWindowSize();

		                Set_Cookie( "exp"+"_"+"screen_width", screen_r.width, "", "/", "", "");
		                Set_Cookie( "exp"+"_"+"screen_height", screen_r.height, "", "/", "", "");
 						Set_Cookie( "exp"+"_"+"pixel_ratio", dpr, "", "/", "", "");

		                location = "'.$_SERVER['PHP_SELF'].'";
	                }

					function getWindowSize() {
					    var wW, wH;
					    if (window.outerWidth) {
					        wW = window.outerWidth;
					        wH = window.outerHeight;
					    } else {
					        var cW = document.body.offsetWidth;
					        var cH = document.body.offsetHeight;
					        window.resizeTo(500,500);
					        var barsW = 500 - document.body.offsetWidth;
					        var barsH = 500 - document.body.offsetHeight;
					        wW = barsW + cW;
					        wH = barsH + cH;
					        window.resizeTo(wW,wH);
					    }
					    return { width: wW, height: wH };
					}

					function Set_Cookie( name, value, expires, path, domain, secure)
					{

					var today = new Date();
					today.setTime( today.getTime() );

					if ( expires )
					{
					expires = expires * 1000 * 60 * 60 * 24;
					}
					var expires_date = new Date( today.getTime() + (expires) );

					document.cookie = name + "=" +escape( value ) +
					( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
					( ( path ) ? ";path=" + path : "" ) +
					( ( domain ) ? ";domain=" + domain : "" ) +
					( ( secure ) ? ";secure" : "" );

					}
	            </script>';
		}
		return $r;
	}

	/**
	 * screen_size function.
	 *
	 * @access public
	 * @return void
	 */
	public function screen_size() {
		return ee()->input->cookie( 'screen_width', false ).'='.ee()->input->cookie( 'screen_height', false ).'='.
			ee()->input->cookie( 'pixel_ratio', '1' );
	}

	/**
	 * redirect function.
	 *
	 * @access public
	 * @return void
	 */
	public function redirect() {
		if ( $this->enable ) {
			if ( $enable == 'on' ) {
				ee()->functions->set_cookie( $cookie_name, 'on', $this->cookie_expire );
			} else {
				ee()->functions->set_cookie( $cookie_name, 'off', $this->cookie_expire );
			}

			ee()->functions->redirect( str_replace( '&#47;', '/', $this->location ) );
		}

		if ( ( ee()->input->cookie( $this->cookie_name, 'on' ) != $this->cookie_value ) || $this->ignore_cookies ) {


			if ( ee()->TMPL->fetch_param( $this->cache['mx_mobile_device'] ) == 'no' ) {return;}

			$this->location = ( !ee()->TMPL->fetch_param( $this->cache['mx_mobile_device'] ) ) ? $this->location : str_replace( "{uri}", ee()->uri->uri_string(), ee()->TMPL->fetch_param( $this->cache['mx_mobile_device'] ) );


			if ( $this->location && ( $this->conds['mobile'] !== FALSE || ( $this->conds['mobile'] === FALSE && $this->redirect == "not_mobile" ) ) ) {

				ee()->functions->redirect( str_replace( '&#47;', '/', $this->location ) );
				return;
			}
		}
	}

	/**
	 * device function.
	 *
	 * @access public
	 * @return void
	 */
	public function device() {
		return $this->conds['device'];
	}

	/**
	 * [is description]
	 * @return boolean [description]
	 */
	private function _is() {
		if ( ! class_exists( 'Mobile_Detect_ex' ) ) {
			require_once PATH_THIRD.'mobile_detect/libraries/Mobile_Detect.php';
		}

		$client = new Mobile_Detect_ex;

		return $client->is($this->client_request);
	}

	/**
	 * device_detect function.
	 *
	 * @access public
	 * @param bool    $refresh (default: false)
	 * @return void
	 */
	public function device_detect( $refresh = false ) {
		if ( isset( $this->cache['mx_mobile_device'] ) && !$refresh ) {
			return true;
		};

		if ( ee()->input->cookie( 'mx_mobile_device', false ) && !$refresh ) {
			$this->cache['mx_mobile_device'] = ee()->input->cookie( 'mx_mobile_device' );
			return true;
		}

		if ( ! class_exists( 'Mobile_Detect_ex' ) ) {
			require_once PATH_THIRD.'mobile_detect/libraries/Mobile_Detect.php';
		}

		$client = new Mobile_Detect_ex;

		$this->agent = $_SERVER['HTTP_USER_AGENT'];

		$this->cache['mx_mobile_device'] = ($client->isTablet()) ? 'mobile' : ( $client->isMobile() ? 'tablet' : false);

		ee()->input->set_cookie( 'mx_mobile_device', $this->cache['mx_mobile_device'], $this->cookie_expire );

		return true;
	}

	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------

	// This function describes how the plugin is used.
	//  Make sure and use output buffering

	function usage() {
		ob_start();
?>


more information - http://www.eec.ms/user_guide/mobile-device-detect/

<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
	/* END */

}


/* End of file pi.mobile_detect.php */
/* Location: ./system/expressionengine/third_party/mobile_detect/pi.mobile_detect.php */
