<?php

namespace MX\Mobile_detect;

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 *  MX Mobile Detect Class for ExpressionEngine
 *
 * @package  ExpressionEngine
 * @subpackage Plugins
 * @category Plugins
 * @author    Max Lazar <max@eec.ms>
 */

class Mobile_detect_ext
{

    var $settings       = array();
    var $name           = MX_MOBILE_DETECT_NAME;
    var $version        = MX_MOBILE_DETECT_VERSION;
    var $description    = MX_MOBILE_DETECT_DESCRIPTION;
    var $settings_exist = 'y';
    var $docs_url       = MX_MOBILE_DETECT_DOCS;
    var $gv_name        = "screen_size";
    /**
     * Defines the ExpressionEngine hooks that this extension will intercept.
     *
     * @since Version 1.0.1
     * @access private
     * @var mixed an array of strings that name defined hooks
     * @see http://codeigniter.com/user_guide/general/hooks.html
     * */

    private $hooks = array( 'template_fetch_template' => ['method' => 'sessions_start'] );

    // -------------------------------
    // Constructor
    // -------------------------------

    public function __construct($settings = false)
    {

        if (isset(ee()->mx_core) === false) {
            ee()->load->library('mx_core');
        }

        ee()->mx_core->set_options(array( 'class' => __CLASS__, 'version' => MX_MOBILE_DETECT_VERSION ));

        // define a constant for the current site_id rather than calling $PREFS->ini() all the time
        if (defined('SITE_ID') == false) {
            define('SITE_ID', ee()->config->item('site_id'));
        }

        // set the settings for all other methods to access
        $this->settings = ( $settings == false ) ? ee()->mx_core->_getSettings() : ee()->mx_core->_saveSettingsToSession($settings);
    }


    /**
     * Prepares and loads the settings form for display in the ExpressionEngine control panel.
     *
     * @since Version 1.0.0
     * @access public
     * @return void
     * */
    public function settings_form()
    {

        ee()->lang->loadfile('mobile_detect');

        // Create the variable array
        $vars = array(
            'addon_name'     => MX_MOBILE_DETECT_NAME,
            'error'          => false,
            'input_prefix'   => __CLASS__,
            'message'        => false,
            'settings_form'  =>false,
            'language_packs' => ''
        );

        $vars['settings'] = $this->settings;
        $vars['settings_form'] = true;

        if ($new_settings = ee()->input->post(__CLASS__)) {

            foreach ($new_settings['row_order'] as $key => $value) {

                if (isset($new_settings[$value]['delete']) || ( empty($new_settings[$value]['value']) && empty($new_settings[$value]['redirect']) )) {
                    unset($new_settings[$value]);
                    unset($new_settings['row_order'][$key]);
                }

            }

            $vars['settings'] = $new_settings;

            ee()->mx_core->_saveSettingsToDB($new_settings);

            $this->_ee_notice(ee()->lang->line('extension_settings_saved_success'));
        }

        return ee()->load->view('form_settings', $vars, true);

    }

    /**
     * _ee_notice function.
     *
     * @access private
     * @param mixed   $msg
     * @return void
     */
    function _ee_notice($msg)
    {
        ee()->javascript->output(array(
                '$.ee_notice("'.ee()->lang->line($msg).'",{type:"success",open:true});',
                'window.setTimeout(function(){$.ee_notice.destroy()}, 3000);'
            ));
    }


    // END


    /**
     * entry_submission_start function.
     *
     * @access public
     * @param mixed   $data
     * @return void
     */
    function sessions_start()
    {

        if (ee()->input->cookie('screen_width', false) && !empty($this->settings)) {
            if (ee()->input->cookie('screen_width', false)) {

            }
            $screen_width = ee()->input->cookie('screen_width', '');
            $screen_height = ee()->input->cookie('screen_height', '');
            $screen_pixel_ratio = ee()->input->cookie('pixel_ratio', '');

            unset($this->settings['row_order']);

            foreach ($this->settings as $k => $v) {
                if (( $v['width'] == '' || version_compare($screen_width, $v['width'], $v['pre_width']) )
                    && ( $v['height']  == '' || version_compare($screen_height, $v['height'], $v['pre_height']) )
                    && ( $v['pix_ratio']   == ''  || version_compare($screen_pixel_ratio, $v['pix_ratio'], $v['pre_pix_ratio']) )
                    && isset($v['disable']) ===  false ) {

                    if ($v['name'] != '') {
                        $this->gv_name = $v['name'];
                    }

                    ee()->config->_global_vars[$this->gv_name] = $v['value'];

                    break;
                }
            }

            ee()->config->_global_vars['screen_width']       = $screen_width;
            ee()->config->_global_vars['screen_height']      = $screen_height;
            ee()->config->_global_vars['screen_pixel_ratio'] = $screen_pixel_ratio;

            return;
        }

        ee()->config->_global_vars[$this->gv_name] = 'default';
        return;
    }

    // --------------------------------
    //  Activate Extension
    // --------------------------------

    function activate_extension()
    {
        ee()->mx_core->_createHooks($this->hooks);
    }

    // END

    // --------------------------------
    //  Update Extension
    // --------------------------------

    function update_extension($current = '')
    {

        if ($current == '' or $current == $this->version) {
            return false;
        }

        if ($current < '2.0.1') {
            // Update to next version
        }

        ee()->db->query("UPDATE exp_extensions SET version = '".ee()->db->escape_str($this->version)."' WHERE class = '".get_class($this)."'");
    }
    // END

    // --------------------------------
    //  Disable Extension
    // --------------------------------

    function disable_extension()
    {

        ee()->db->delete('exp_extensions', array( 'class' => get_class($this) ));
    }
    // END
}

/* End of file ext.mobile_detect.php */
/* Location: ./system/expressionengine/third_party/mobile_detect/ext.mobile_detect.php */
