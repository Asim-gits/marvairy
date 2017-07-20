<div class="clear"></div>
<div class="postbox rppfdcontainer" id="dashboard_right_now" >
    <h3 class="hndle"><?php echo __('Payment Fee/Discount Settings', 'rppfd') ?></h3>
    <div class="inside">
        <div class="main">
            <form method="post" action="" name="<?php echo self::$plugin_slug; ?>">
                <input type="hidden" name="<?php echo self::$plugin_slug; ?>" value="1"/>
                <table class="rp_table" >
                    <tr>
                        <td  width="20%" class="label"><?php echo __('Enable?', 'rppfd') ?></td>
                        <td>
                            <input type="checkbox" name="enable" <?php echo ($this->getSetting("enable")) ? "checked=checked" : ""; ?> value="1" />
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><?php echo __('Payment Fee/Discount Text', 'rppfd') ?></td>
                        <td>
                            <input type="text" name="discount_text" value="<?php echo $this->getSetting('discount_text') ?>" /><br>
                            <small><i>Add {payment_method} for display payment method</i></small>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><?php echo __('Taxable?', 'rppfd') ?></td>
                        <td>
                            <input type="checkbox" name="taxable" <?php echo ($this->getSetting("taxable")) ? "checked=checked" : ""; ?> value="1" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table>
                                <tr>
                                    <th><?php echo __('Payment Method', 'rppfd') ?></th> 
                                    <th><?php echo __('Type', 'rppfd') ?></th> 
                                    <th><?php echo __('Amount Type', 'rppfd') ?></th> 
                                    <th><?php echo __('Amount', 'rppfd') ?></th> 
                                    <th><?php echo __('User Group', 'rppfd') ?></th> 
                                </tr>
                                <?php
                                $paymentSettings=$this->getSetting('payment_setting');
                                foreach (self::getPaymentGetway() as $key => $value):
                                    $currentData=$paymentSettings[$key];
                                    ?>
                                    <tr>
                                        <td><?php echo __($value, 'rppfd'); ?></td>
                                        <td>
                                            <select name="payment_setting[<?php echo $key; ?>][type]">
                                                <option value='0' <?php echo (isset($currentData['type']) && $currentData['type']==0)?"selected=selected":""; ?> ><?php echo __('Fee', 'rppfd'); ?></option>
                                                <option value='1' <?php echo (isset($currentData['type']) && $currentData['type']==1)?"selected=selected":""; ?> ><?php echo __('Discount', 'rppfd'); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="payment_setting[<?php echo $key; ?>][amount_type]">
                                                <option value='0' <?php echo (isset($currentData['amount_type']) && $currentData['amount_type']==1)?"selected=selected":""; ?> ><?php echo get_woocommerce_currency_symbol(); ?></option>
                                                <option value='1' <?php echo (isset($currentData['amount_type']) && $currentData['amount_type']==1)?"selected=selected":""; ?> ><?php echo __('%', 'rppfd'); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type='number' name="payment_setting[<?php echo $key; ?>][amount]" value="<?php echo $currentData['amount'];  ?>" />
                                        </td>
                                        <td>
                                            <?php echo self::getUserRoleDropdown($key,$currentData['user']); ?>
                                        </td>
                                    </tr>
                                    <?php
                                endforeach;
                                ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input type="submit" class="button button-primary" name="btn-rppfd-submit" value="<?php echo __("Save Settings", "rppfd") ?>" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
