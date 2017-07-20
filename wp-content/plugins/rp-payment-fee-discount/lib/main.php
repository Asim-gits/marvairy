<?php

class rp_payment_fee_discount {

    private static $plugin_url;
    private static $plugin_dir;
    private static $plugin_title = "Payment Fee/Discount";
    private static $plugin_slug = "rppfd-setting";
    private static $rppfd_option_key = "rppfd-setting";
    private $rppfd_settings;

    public function __construct()
    {
        global $rppfd_plugin_url, $rppfd_plugin_dir;

        /* plugin url and directory variable */
        self::$plugin_dir = $rppfd_plugin_dir;
        self::$plugin_url = $rppfd_plugin_url;

        /* load donation  setting */
        $this->rppfd_settings = get_option(self::$rppfd_option_key);

        $this->initAction();
    }

    public function initAction()
    {
        add_action("admin_menu", array($this, "adminMenu"));
        add_action("admin_init", array($this, "adminInit"));
        add_action("init",array($this,"Init"));
        add_action('woocommerce_cart_calculate_fees', array(&$this, 'addPaymentFeeDiscount'), 10);
    }
    
    public function Init(){
        wp_enqueue_script('jquery');
        wp_enqueue_script('rpwcf-script', self::$plugin_url . "assets/js/rppfd.js");
    }

    public function addPaymentFeeDiscount($cart)
    {
        $currentGateway = self::getCurrentGetway();
        
        
        if (!empty($currentGateway) && $currentGateway != "" && isset($currentGateway)) {
            $paymentFee = $this->getFeeDiscount($currentGateway);
            if ($paymentFee != 0) {
                $taxable = ($this->getSetting('taxable') == 1) ? true : false;
                $feeText = str_replace('{payment_method}', $currentGateway->title, $this->getSetting('discount_text'));
                $cart->add_fee(__($feeText, 'woocommerce'), $paymentFee, $taxable);
            }
        }
    }

    public function getFeeDiscount($objMethod)
    {
        $paymentMethod = $objMethod->id;
        $fee = 0;
        $paymentSettings = $this->getSetting('payment_setting');
        $currentPaymentSetting = $paymentSettings[$paymentMethod];
        if (isset($currentPaymentSetting['amount']) && $currentPaymentSetting['amount'] > 0 && self::applyForCurrentUser($currentPaymentSetting['user']) === true) {
            $fee = self::getFeeTotal($currentPaymentSetting['amount_type'], $currentPaymentSetting['amount']);
            if ($currentPaymentSetting['type'] == 1) {
                $fee = $fee * -1;
            }
        }
        return $fee;
    }

    private static function applyForCurrentUser($userrole)
    {
        if(empty($userrole)){
            return true;
        }
        
        if (!is_user_logged_in() && in_array('rpgst', $userrole)) {
            return true;
        } if (is_user_logged_in()) {
            $currentUserRole = self::getCurrentUserRole();
            if (in_array($currentUserRole, $userrole)) {
                return true;
            }
        }
        return false;
    }

    private static function getCurrentUserRole()
    {
        global $current_user;

        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);

        return $user_role;
    }

    /* function for calculate fee */

    public static function getFeeTotal($type, $amount)
    {
        global $woocommerce;

        if ($type == 1):
            $total = $woocommerce->cart->subtotal;
            $amount = ($amount / 100) * $total;
        endif;

        return $amount;
    }

    public static function getCurrentGetway()
    {

        global $woocommerce;

        $available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
        $current_gateway = '';
        if (!empty($available_gateways)) {
            // Chosen Method
            if (isset($woocommerce->session->chosen_payment_method) && isset($available_gateways[$woocommerce->session->chosen_payment_method])) {
                $current_gateway = $available_gateways[$woocommerce->session->chosen_payment_method];
            } elseif (isset($available_gateways[get_option('woocommerce_default_gateway')])) {
                $current_gateway = $available_gateways[get_option('woocommerce_default_gateway')];
            } else {
                $current_gateway = current($available_gateways);
            }
        }
        return $current_gateway;
    }

    public function adminInit()
    {

        wp_enqueue_script('jquery');
        wp_enqueue_script('rppfd-select2', self::$plugin_url . "assets/js/select2.min.js");
        wp_enqueue_script('rppfd-cartfee', self::$plugin_url . "assets/js/admin.js");
        wp_enqueue_style('rppfd-select2', self::$plugin_url . "assets/css/select2.css");
        wp_enqueue_style('rppfd-admin', self::$plugin_url . "assets/css/admin.css");
    }

    public function adminMenu()
    {
        $wc_page = 'woocommerce';
        add_submenu_page($wc_page, self::$plugin_title, self::$plugin_title, "install_plugins", self::$plugin_slug, array($this, "settingOptions"));
    }

    public function settingOptions()
    {
        /* save donation setting */
        if (isset($_POST[self::$plugin_slug])) {
            $this->saveSetting();
        }

        /* include admin   setting file */
        include_once self::$plugin_dir . "view/setting.php";
    }

    public function saveSetting()
    {
        $arrayRemove = array(self::$plugin_slug, "btn-rppfd-submit");
        $saveData = array();
        foreach ($_POST as $key => $value):
            if (in_array($key, $arrayRemove)) {
                continue;
            }
            $saveData[$key] = $value;
        endforeach;
        $this->rppfd_settings = $saveData;
        update_option(self::$rppfd_option_key, $saveData);
    }

    private static function getUserRoleDropdown($method, $value)
    {
        global $wp_roles;

        $dropdown = "";
        $dropdown.='<select name="payment_setting[' . $method . '][user][]" class="rpwcf_select2" multiple="multiple" >';
        $selected = (is_array($value) && in_array('rpgst', $value)) ? "selected=selected" : "";
        $dropdown.='<option value="rpgst" ' . $selected . ' >' . __("Guest User", 'rppfd') . '</option>';
        if (isset($wp_roles->roles) && !empty($wp_roles->roles) && count($wp_roles->roles) > 0):
            foreach ($wp_roles->roles as $key => $val):
                $selected = (is_array($value) && in_array($key, $value)) ? "selected=selected" : "";
                $dropdown.='<option value="' . $key . '" ' . $selected . ' >' . __($val["name"], "woocommerce") . '</option>';
            endforeach;
        endif;
        $dropdown.='<select>';
        return $dropdown;
    }

    public function getSetting($key)
    {

        if (!$key || $key == "") {
            return;
        }

        if (!isset($this->rppfd_settings[$key])) {
            return;
        }

        return $this->rppfd_settings[$key];
    }

    private static function getPaymentGetway()
    {
        global $woocommerce;
        $response = array();
        $paymentMethod = $woocommerce->payment_gateways->payment_gateways();
        if (!empty($paymentMethod) && count($paymentMethod) > 0):
            foreach ($paymentMethod as $id => $method):
                $response[$id] = $method->title;
            endforeach;
        endif;
        return $response;
    }

}

/* load plugin if woocommerce plugin is activated */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    new rp_payment_fee_discount();
}