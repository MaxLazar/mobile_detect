<?php

namespace MX\Mobile_detect;

require_once __DIR__.'/vendor/autoload.php';

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 *  MX Mobile Detect Class for ExpressionEngine5.
 *
 * @category Plugins
 *
 * @author    Max Lazar <max@eecms.dev>
 */
$plugin_info = array(
    'pi_name'        => MX_MOBILE_DETECT_NAME,
    'pi_version'     => MX_MOBILE_DETECT_VERSION,
    'pi_author'      => MX_MOBILE_DETECT_AUTHOR,
    'pi_author_url'  => MX_MOBILE_DETECT_DOCS,
    'pi_description' => MX_MOBILE_DETECT_DESCRIPTION,
    'pi_usage'       => mobile_detect::usage(),
);

class Mobile_detect
{
    public $return_data = '';
    public $location = '';
    public $conds = array();
    public $cache;
    public $enable;
    public $cookie_value;
    public $redirect;
    public $cookie_name;
    public $client_request;
    public $ignore_cookies;
    public $refresh;
    public $agent;
    public $cookie_expire = 86500;

    /**
     * Mobile_detect function.
     */
    public function __construct()
    {
      //  $this->cache = @ee()->session->cache[__CLASS__];

        ee()->load->helper('url');

        $uri_string           = ee()->uri->uri_string();
        $this->location       = (!ee()->TMPL->fetch_param('location')) ? false : str_replace('{uri}', ee()->uri->uri_string(), ee()->TMPL->fetch_param('location'));
        $this->redirect       = (!ee()->TMPL->fetch_param('redirect')) ? ('mobile') : ee()->TMPL->fetch_param('redirect');
        $this->client_request = (!ee()->TMPL->fetch_param('client')) ? false : ee()->TMPL->fetch_param('client');
        $this->cookie_name    = (!ee()->TMPL->fetch_param('cookie_name')) ? 'mobile_redirect' : ee()->TMPL->fetch_param('cookie_name');
        $this->cookie_value   = (!ee()->TMPL->fetch_param('cookie_value')) ? 'on' : ee()->TMPL->fetch_param('cookie_value');
        $this->enable         = (!ee()->TMPL->fetch_param('enable')) ? false : (('yes' == ee()->TMPL->fetch_param('enable')) ? 'on' : 'off');
        $this->ignore_cookies = (!ee()->TMPL->fetch_param('ignore_cookies')) ? false :
            (('yes'               == ee()->TMPL->fetch_param('ignore_cookies')) ? true : false);
        $this->refresh        = (!ee()->TMPL->fetch_param('refresh')) ? false :
            (('yes'               == ee()->TMPL->fetch_param('refresh')) ? true : false);

        $this->device_detect($this->refresh);

        $this->conds['mobile']     = ($this->client_request) ? (($this->_is()) ? true : false) : ee()->session->cache['mobile_detect']['device'];
        $this->conds['not_mobile'] = (false !== $this->conds['mobile']) ? false : true;
        $this->conds['device']     = ($this->conds['mobile']) ? ee()->session->cache['mobile_detect']['device'] : 'not_mobile';

        return;
    }

    /**
     * pair function.
     */
    public function pair()
    {
        $tagdata = ee()->TMPL->tagdata;
        $tagdata = ee()->functions->prep_conditionals($tagdata, $this->conds);
        $tagdata = ee()->TMPL->parse_variables_row($tagdata, $this->conds['mobile']);

        return $this->return_data = $tagdata;
    }

    /**
     * [istablet description]
     * @return [type] [description]
     */
    public function istablet()
    {
        return ee()->session->cache['mobile_detect']['device'] == 'tablet' ?  true :  false;
    }

    /**
     * [isphone description]
     * @return [type] [description]
     */
    public function isphone()
    {
        return ee()->session->cache['mobile_detect']['device'] == 'phone' ?  true :  false;
    }

    /**
     * [ismobile description]
     * @return [type] [description]
     */
    public function ismobile()
    {
        return ee()->session->cache['mobile_detect']['device'] == 'phone' ||  ee()->session->cache['mobile_detect']['device'] == 'tablet' ?  true :  false;
    }

    public function isnotmobile()
    {
        return $this->conds['device']  == 'not_mobile' ? true : false;
    }


    /**
     * screen_detect function.
     */
    public function screen_detect()
    {
        $r = '';
        if (false === ee()->input->cookie('screen_width', false)) {
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
     */
    public function screen_size()
    {
        return ee()->input->cookie('screen_width', false).'='.ee()->input->cookie('screen_height', false).'='.
            ee()->input->cookie('pixel_ratio', '1');
    }

    /**
     * redirect function.
     */
    public function redirect()
    {
        if ($this->enable) {
            if ('on' == $enable) {
                ee()->functions->set_cookie($cookie_name, 'on', $this->cookie_expire);
            } else {
                ee()->functions->set_cookie($cookie_name, 'off', $this->cookie_expire);
            }

            ee()->functions->redirect(str_replace('&#47;', '/', $this->location));
        }

        if ((ee()->input->cookie($this->cookie_name, 'on') != $this->cookie_value) || $this->ignore_cookies) {
            if ('no' == ee()->TMPL->fetch_param(ee()->session->cache['mobile_detect']['device'])) {
                return;
            }

            $this->location = (!ee()->TMPL->fetch_param(ee()->session->cache['mobile_detect']['device'])) ? $this->location : str_replace('{uri}', ee()->uri->uri_string(), ee()->TMPL->fetch_param(ee()->session->cache['mobile_detect']['device']));

            if ($this->location && (false !== $this->conds['mobile'] || (false === $this->conds['mobile'] && 'not_mobile' == $this->redirect))) {
                ee()->functions->redirect(str_replace('&#47;', '/', $this->location));

                return;
            }
        }
    }

    /**
     * device function.
     */
    public function device()
    {
        return $this->conds['device'];
    }

    /**
     * [is description].
     *
     * @return bool [description]
     */
    private function _is()
    {

        ee()->load->library('Mobile_Detect');

        return ee()->mobile_detect->is($this->client_request);
    }

    /**
     * device_detect function.
     *
     * @param bool $refresh (default: false)
     */
    public function device_detect($refresh = false)
    {
        if (isset(ee()->session->cache['mobile_detect']['device']) && !$refresh) {
            return true;
        }

        if (ee()->input->cookie('mx_mobile_device', false) && !$refresh) {
            ee()->session->cache['mobile_detect']['device'] = ee()->input->cookie('mx_mobile_device');
            return true;
        }

        ee()->load->library('Mobile_Detect');

        $this->agent = $_SERVER['HTTP_USER_AGENT'];
        ee()->session->cache['mobile_detect']['device'] = (ee()->mobile_detect->isMobile()) ? (ee()->mobile_detect->isTablet() ? 'tablet' : 'phone') : false; //
        ee()->input->set_cookie('mx_mobile_device', ee()->session->cache['mobile_detect']['device'], $this->cookie_expire);

        return true;
    }

    // ----------------------------------------
    //  Plugin Usage
    // ----------------------------------------

    // This function describes how the plugin is used.
    //  Make sure and use output buffering

    public static function usage()
    {
        // for performance only load README if inside control panel
        return REQ === 'CP' ? file_get_contents(dirname(__FILE__).'/README.md') : null;
    }

    /* END */
}

/* End of file pi.mobile_detect.php */
/* Location: ./system/expressionengine/third_party/mobile_detect/pi.mobile_detect.php */
