<?php

/* multi-currency.twig */
class __TwigTemplate_fe832aafa9055c61ff5f6371687512bfeaab3f6f4578e8f252b8c8fd760c9c5a extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "
";
        // line 2
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('wp_do_action')->getCallable(), array("wcml_before_multi_currency_ui")), "html", null, true);
        echo "


<form method=\"post\" action=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "action", array()), "html", null, true);
        echo "\" id=\"wcml_mc_options\">
    ";
        // line 6
        echo $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "nonce", array());
        echo "
    <input type=\"hidden\" id=\"wcml_save_currency_nonce\" value=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "save_currency_nonce", array()), "html", null, true);
        echo "\"/>
    <input type=\"hidden\" id=\"del_currency_nonce\" value=\"";
        // line 8
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "del_currency_nonce", array()), "html", null, true);
        echo "\" />
    <input type=\"hidden\" id=\"currencies_list_nonce\" value=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "currencies_list_nonce", array()), "html", null, true);
        echo "\" />
    <input type=\"hidden\" name=\"action\" value=\"save-mc-options\" />

    <div class=\"wcml-section \">
        <div class=\"wcml-section-header\">
            <h3>";
        // line 14
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "headers", array()), "enable_disable", array()), "html", null, true);
        echo "</h3>
        </div>
        <div class=\"wcml-section-content wcml-section-content-wide\">
            <p>
                <input type=\"checkbox\" name=\"multi_currency\" id=\"multi_currency_independent\" value=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "multi_currency_option", array()), "html", null, true);
        echo "\"
                    ";
        // line 19
        if ((isset($context["multi_currency_on"]) ? $context["multi_currency_on"] : null)) {
            echo "checked=\"checked\"";
        }
        echo " ";
        if ((isset($context["mco_disabled"]) ? $context["mco_disabled"] : null)) {
            echo "disabled=\"disabled\"";
        }
        echo " />
                <label for=\"multi_currency_independent\">
                    ";
        // line 21
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "label_mco", array()), "html", null, true);
        echo "
                    &nbsp;
                    <a href=\"";
        // line 23
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "label_mco_learn_url", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "label_mco_learn_txt", array()), "html", null, true);
        echo "</a>
                </label>
            </p>

            ";
        // line 27
        if (twig_test_empty((isset($context["wc_currency"]) ? $context["wc_currency"] : null))) {
            // line 28
            echo "            <p>
                <i class=\"icon-warning-sign\"></i>
                ";
            // line 30
            echo (isset($context["wc_currency_empty_warn"]) ? $context["wc_currency_empty_warn"] : null);
            echo "
            </p>
            ";
        }
        // line 33
        echo "
        </div>
    </div>

    ";
        // line 37
        if ((isset($context["wc_currency"]) ? $context["wc_currency"] : null)) {
            // line 38
            echo "    <div class=\"wcml-section\" id=\"multi-currency-per-language-details\" ";
            if (($this->getAttribute((isset($context["wcml_settings"]) ? $context["wcml_settings"] : null), "enable_multi_currency", array()) != $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "multi_currency_option", array()))) {
                echo "style=\"display:none\"";
            }
            echo ">

        <div class=\"wcml-section-header\">
            <h3>";
            // line 41
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "headers", array()), "currencies", array()), "html", null, true);
            echo "</h3>
        </div>

        <div class=\"wcml-section-content wcml-section-content-wide\">
            <div>
                <div class=\"currencies-table-content\">

                    <div class=\"tablenav top clearfix\">
                        <button type=\"button\" class=\"button-secondary wcml_add_currency alignright js-wcml-dialog-trigger\"
                                data-dialog=\"wcml_currency_options_\" data-content=\"wcml_currency_options_\" data-width=\"480\" data-height=\"580\">
                            <i class=\"otgs-ico-add otgs-ico-sm\"></i>
                            ";
            // line 52
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "add_currency_button", array()), "html", null, true);
            echo "
                        </button>
                    </div>
                    <input type=\"hidden\" id=\"update_currency_lang_nonce\" value=\"";
            // line 55
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "update_currency_lang_nonce", array()), "html", null, true);
            echo "\"/>

                    <table class=\"widefat currency_table\" id=\"currency-table\">
                        <thead>
                            <tr>
                                <th class=\"wcml-col-currency\">";
            // line 60
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "head_currency", array()), "html", null, true);
            echo "</th>
                                <th class=\"wcml-col-rate\">";
            // line 61
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "head_rate", array()), "html", null, true);
            echo "</th>
                                <th class=\"wcml-col-edit\"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class=\"wcml-row-currency\">
                                <td class=\"wcml-col-currency\">
                                    <span class=\"truncate\">";
            // line 68
            echo $this->getAttribute((isset($context["wc_currencies"]) ? $context["wc_currencies"] : null), (isset($context["wc_currency"]) ? $context["wc_currency"] : null));
            echo "</span>
                                    <small>";
            // line 69
            echo (isset($context["positioned_price"]) ? $context["positioned_price"] : null);
            echo "</small>
                                </td>
                                <td class=\"wcml-col-rate\"><span class=\"truncate\">";
            // line 71
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "default", array()), "html", null, true);
            echo "</span></td>
                                <td class=\"wcml-col-edit\">
                                    <a href=\"#\" title=\"";
            // line 73
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "edit", array()), "html", null, true);
            echo "\" class=\"edit_currency js-wcml-dialog-trigger hidden\" data-height=\"530\" data-width=\"480\">
                                        <i class=\"otgs-ico-edit\" title=\"";
            // line 74
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "edit", array()), "html", null, true);
            echo "\"></i>
                                    </a>

                                </td>
                            </tr>

                            ";
            // line 80
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["currencies"]) ? $context["currencies"] : null));
            foreach ($context['_seq'] as $context["code"] => $context["currency"]) {
                // line 81
                echo "                            <tr id=\"currency_row_";
                echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                echo "\" class=\"wcml-row-currency\">
                                <td class=\"wcml-col-currency\">
                                    <span class=\"truncate\">";
                // line 83
                echo $this->getAttribute((isset($context["wc_currencies"]) ? $context["wc_currencies"] : null), $context["code"]);
                echo "</span>
                                    <small>";
                // line 84
                echo $this->getAttribute((isset($context["currencies_positions"]) ? $context["currencies_positions"] : null), $context["code"]);
                echo "</small>
                                </td>
                                <td class=\"wcml-col-rate\">
                                    1 ";
                // line 87
                echo twig_escape_filter($this->env, (isset($context["wc_currency"]) ? $context["wc_currency"] : null), "html", null, true);
                echo " = <span class=\"rate\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["currency"], "rate", array()), "html", null, true);
                echo "</span> ";
                echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                echo "
                                </td>

                                <td class=\"wcml-col-edit\">
                                    <a href=\"#\" title=\"";
                // line 91
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "edit", array()), "html", null, true);
                echo "\" class=\"edit_currency js-wcml-dialog-trigger\"
                                            data-currency=\"";
                // line 92
                echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                echo "\" data-content=\"wcml_currency_options_";
                echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                echo "\"  data-dialog=\"wcml_currency_options_";
                echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                echo "\"
                                            data-height=\"530\" data-width=\"480\">
                                        <i class=\"otgs-ico-edit\" title=\"";
                // line 94
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "edit", array()), "html", null, true);
                echo "\"></i>
                                    </a>
                                </td>
                            </tr>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['code'], $context['currency'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 99
            echo "
                            <tr class=\"default_currency\">
                                <td colspan=\"3\">";
            // line 101
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "default_currency", array()), "html", null, true);
            echo "
                                    <i class=\"wcml-tip otgs-ico-help\" data-tip=\"";
            // line 102
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "default_cur_tip", array()), "html", null, true);
            echo "\"></i>

                                </td>
                            </tr>

                        </tbody>
                    </table>

                    <div class=\"currency_wrap\">
                        <div class=\"currency_inner\">
                            <table class=\"widefat currency_lang_table\" id=\"currency-lang-table\">
                                <thead>
                                    <tr>
                                        <td colspan=\"";
            // line 115
            echo twig_escape_filter($this->env, twig_length_filter($this->env, (isset($context["active_languages"]) ? $context["active_languages"] : null)), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "help_title", array()), "html", null, true);
            echo "</td>
                                    </tr>
                                    <tr>
                                        ";
            // line 118
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["active_languages"]) ? $context["active_languages"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                // line 119
                echo "                                        <th>
                                            <img src=\"";
                // line 120
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('get_flag_url')->getCallable(), array($this->getAttribute($context["language"], "code", array()))), "html", null, true);
                echo "\" width=\"18\" height=\"12\"/>
                                        </th>
                                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 123
            echo "                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class=\"wcml-row-currency-lang\">
                                        ";
            // line 127
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["active_languages"]) ? $context["active_languages"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                // line 128
                echo "                                        <td class=\"currency_languages\">
                                            <ul>
                                                ";
                // line 130
                $context["title_yes"] = sprintf($this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "disable_for", array()), call_user_func_array($this->env->getFunction('get_currency_name')->getCallable(), array((isset($context["wc_currency"]) ? $context["wc_currency"] : null))), $this->getAttribute($context["language"], "display_name", array()));
                // line 131
                echo "                                                ";
                $context["title_no"] = sprintf($this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "enable_for", array()), call_user_func_array($this->env->getFunction('get_currency_name')->getCallable(), array((isset($context["wc_currency"]) ? $context["wc_currency"] : null))), $this->getAttribute($context["language"], "display_name", array()));
                // line 132
                echo "                                                <li class=\"on\" data-lang=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", array()), "html", null, true);
                echo "\">
                                                    <a class=\"";
                // line 133
                if (call_user_func_array($this->env->getFunction('is_currency_on')->getCallable(), array((isset($context["wc_currency"]) ? $context["wc_currency"] : null), $this->getAttribute($context["language"], "code", array())))) {
                    echo "otgs-ico-yes";
                } else {
                    echo " otgs-ico-no";
                }
                echo "\"
                                                       data-language=\"";
                // line 134
                echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", array()), "html", null, true);
                echo "\" data-currency=\"";
                echo twig_escape_filter($this->env, (isset($context["wc_currency"]) ? $context["wc_currency"] : null), "html", null, true);
                echo "\" href=\"#\"
                                                        ";
                // line 135
                if (call_user_func_array($this->env->getFunction('is_currency_on')->getCallable(), array((isset($context["wc_currency"]) ? $context["wc_currency"] : null), $this->getAttribute($context["language"], "code", array())))) {
                    // line 136
                    echo "                                                            title=\"";
                    echo twig_escape_filter($this->env, (isset($context["title_yes"]) ? $context["title_yes"] : null), "html", null, true);
                    echo "\" data-title-alt=\"";
                    echo twig_escape_filter($this->env, (isset($context["title_no"]) ? $context["title_no"] : null), "html", null, true);
                    echo "\"
                                                        ";
                } else {
                    // line 138
                    echo "                                                            title=\"";
                    echo twig_escape_filter($this->env, (isset($context["title_no"]) ? $context["title_no"] : null), "html", null, true);
                    echo "\" data-title-alt=\"";
                    echo twig_escape_filter($this->env, (isset($context["title_yes"]) ? $context["title_yes"] : null), "html", null, true);
                    echo "\"
                                                        ";
                }
                // line 140
                echo "                                                    ></a>
                                                </li>
                                            </ul>
                                        </td>
                                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 145
            echo "                                    </tr>

                                    ";
            // line 147
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["currencies"]) ? $context["currencies"] : null));
            foreach ($context['_seq'] as $context["code"] => $context["currency"]) {
                // line 148
                echo "                                    <tr id=\"currency_row_langs_";
                echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                echo "\" class=\"wcml-row-currency-lang\">
                                        ";
                // line 149
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["active_languages"]) ? $context["active_languages"] : null));
                foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                    // line 150
                    echo "                                        <td class=\"currency_languages\">
                                            <ul>
                                                ";
                    // line 152
                    $context["title_yes"] = sprintf($this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "disable_for", array()), call_user_func_array($this->env->getFunction('get_currency_name')->getCallable(), array($context["code"])), $this->getAttribute($context["language"], "display_name", array()));
                    // line 153
                    echo "                                                ";
                    $context["title_no"] = sprintf($this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "enable_for", array()), call_user_func_array($this->env->getFunction('get_currency_name')->getCallable(), array($context["code"])), $this->getAttribute($context["language"], "display_name", array()));
                    // line 154
                    echo "                                                <li class=\"on\" data-lang=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", array()), "html", null, true);
                    echo "\">
                                                    <a class=\"";
                    // line 155
                    if (call_user_func_array($this->env->getFunction('is_currency_on')->getCallable(), array($context["code"], $this->getAttribute($context["language"], "code", array())))) {
                        echo "otgs-ico-yes";
                    } else {
                        echo " otgs-ico-no";
                    }
                    echo "\"
                                                       data-language=\"";
                    // line 156
                    echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", array()), "html", null, true);
                    echo "\" data-currency=\"";
                    echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                    echo "\" href=\"#\"
                                                       ";
                    // line 157
                    if (call_user_func_array($this->env->getFunction('is_currency_on')->getCallable(), array((isset($context["wc_currency"]) ? $context["wc_currency"] : null), $this->getAttribute($context["language"], "code", array())))) {
                        // line 158
                        echo "                                                           title=\"";
                        echo twig_escape_filter($this->env, (isset($context["title_yes"]) ? $context["title_yes"] : null), "html", null, true);
                        echo "\" data-title-alt=\"";
                        echo twig_escape_filter($this->env, (isset($context["title_no"]) ? $context["title_no"] : null), "html", null, true);
                        echo "\"
                                                       ";
                    } else {
                        // line 160
                        echo "                                                           title=\"";
                        echo twig_escape_filter($this->env, (isset($context["title_no"]) ? $context["title_no"] : null), "html", null, true);
                        echo "\" data-title-alt=\"";
                        echo twig_escape_filter($this->env, (isset($context["title_yes"]) ? $context["title_yes"] : null), "html", null, true);
                        echo "\"
                                                       ";
                    }
                    // line 162
                    echo "                                                    ></a>
                                                </li>
                                            </ul>
                                        </td>
                                        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 167
                echo "                                    </tr>
                                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['code'], $context['currency'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 169
            echo "
                                    <tr class=\"default_currency\">
                                        ";
            // line 171
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["active_languages"]) ? $context["active_languages"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                // line 172
                echo "                                        <td align=\"center\">
                                            <select rel=\"";
                // line 173
                echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", array()), "html", null, true);
                echo "\">
                                                <option value=\"0\" ";
                // line 174
                if ((call_user_func_array($this->env->getFunction('get_language_currency')->getCallable(), array($this->getAttribute($context["language"], "code", array()))) == 0)) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "keep_currency", array()), "html", null, true);
                echo "</option>
                                                ";
                // line 175
                if (call_user_func_array($this->env->getFunction('is_currency_on')->getCallable(), array((isset($context["wc_currency"]) ? $context["wc_currency"] : null), $this->getAttribute($context["language"], "code", array())))) {
                    // line 176
                    echo "                                                <option value=\"";
                    echo twig_escape_filter($this->env, (isset($context["wc_currency"]) ? $context["wc_currency"] : null), "html", null, true);
                    echo "\" ";
                    if ((call_user_func_array($this->env->getFunction('get_language_currency')->getCallable(), array($this->getAttribute($context["language"], "code", array()))) == (isset($context["wc_currency"]) ? $context["wc_currency"] : null))) {
                        echo "selected=\"selected\"";
                    }
                    echo ">";
                    echo twig_escape_filter($this->env, (isset($context["wc_currency"]) ? $context["wc_currency"] : null), "html", null, true);
                    echo "</option>
                                                ";
                }
                // line 178
                echo "
                                                ";
                // line 179
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["currencies"]) ? $context["currencies"] : null));
                foreach ($context['_seq'] as $context["code"] => $context["currency"]) {
                    // line 180
                    echo "                                                ";
                    if (call_user_func_array($this->env->getFunction('is_currency_on')->getCallable(), array($context["code"], $this->getAttribute($context["language"], "code", array())))) {
                        // line 181
                        echo "                                                <option value=\"";
                        echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                        echo "\" ";
                        if ((call_user_func_array($this->env->getFunction('get_language_currency')->getCallable(), array($this->getAttribute($context["language"], "code", array()))) == $context["code"])) {
                            echo "selected=\"selected\"";
                        }
                        echo ">";
                        echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                        echo "</option>
                                                ";
                    }
                    // line 183
                    echo "                                                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['code'], $context['currency'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 184
                echo "                                            </select>
                                        </td>
                                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 187
            echo "                                    </tr>

                                </tbody>
                            </table>
                            <input type=\"hidden\" id=\"wcml_update_default_currency_nonce\" value=\"";
            // line 191
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "wpdate_default_cur_nonce", array()), "html", null, true);
            echo "\" />

                        </div>
                    </div>

                    <table class=\"widefat currency_delete_table\" id=\"currency-delete-table\">
                        <thead>
                            <tr>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class=\"currency_default wcml-row-currency-del\">
                                <td class=\"wcml-col-delete\">
                                    <a title=\"";
            // line 205
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "delete", array()), "html", null, true);
            echo "\" class=\"delete_currency hidden\">
                                    <i class=\"otgs-ico-delete\" title=\"";
            // line 206
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "delete", array()), "html", null, true);
            echo "\"></i>
                                    </a>
                                </td>
                            </tr>

                            ";
            // line 211
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["currencies"]) ? $context["currencies"] : null));
            foreach ($context['_seq'] as $context["code"] => $context["currency"]) {
                // line 212
                echo "                            <tr id=\"currency_row_del_";
                echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                echo "\" class=\"wcml-row-currency-del\">
                                <td class=\"wcml-col-delete\">
                                    <a title=\"";
                // line 214
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "delete", array()), "html", null, true);
                echo "\" class=\"delete_currency\"
                                        data-currency_name=\"";
                // line 215
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["wc_currencies"]) ? $context["wc_currencies"] : null), $context["code"]), "html", null, true);
                echo "\"
                                        data-currency_symbol=\"";
                // line 216
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('get_currency_symbol')->getCallable(), array($context["code"])), "html", null, true);
                echo "\"
                                        data-currency=\"";
                // line 217
                echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                echo "\" href=\"#\">
                                        <i class=\"otgs-ico-delete\" title=\"";
                // line 218
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "currencies_table", array()), "delete", array()), "html", null, true);
                echo "\"></i>
                                    </a>
                                </td>
                            </tr>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['code'], $context['currency'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 223
            echo "
                            <tr class=\"default_currency\">
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <ul id=\"display_custom_prices_select\">
                    <li>
                        <input type=\"checkbox\" name=\"display_custom_prices\" id=\"display_custom_prices\"
                           value=\"1\" ";
            // line 235
            if ($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "custom_prices_select", array()), "checked", array())) {
                echo " checked=\"checked\"";
            }
            echo ">
                        <label for=\"display_custom_prices\">";
            // line 236
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "custom_prices_select", array()), "label", array()), "html", null, true);
            echo "</label>
                        <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
            // line 237
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "custom_prices_select", array()), "tip", array()), "html", null, true);
            echo "\"></i>
                    </li>
                </ul>

            </div>
        </div>

    </div>

    ";
            // line 246
            $this->loadTemplate("exchange-rates.twig", "multi-currency.twig", 246)->display(array_merge($context, (isset($context["exchange_rates"]) ? $context["exchange_rates"] : null)));
            // line 247
            echo "
    ";
            // line 248
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('wp_do_action')->getCallable(), array("wcml_before_currency_switcher_options")), "html", null, true);
            echo "

    ";
            // line 250
            $this->loadTemplate("currency-switcher-options.twig", "multi-currency.twig", 250)->display($context);
            // line 251
            echo "
    <input type=\"hidden\" id=\"wcml_warn_message\" value=\"";
            // line 252
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "navigate_warn", array()), "html", null, true);
            echo "\" />
    <input type=\"hidden\" id=\"wcml_warn_disable_language_massage\" value=\"";
            // line 253
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cur_lang_warn", array()), "html", null, true);
            echo "\" />

    <p class=\"wpml-margin-top-sm\">
        <input id=\"wcml_mc_options_submit\" type='submit' value='";
            // line 256
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "submit", array()), "html", null, true);
            echo "' class='button-primary'/>
    </p>

    ";
        }
        // line 260
        echo "
</form>
";
    }

    public function getTemplateName()
    {
        return "multi-currency.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  648 => 260,  641 => 256,  635 => 253,  631 => 252,  628 => 251,  626 => 250,  621 => 248,  618 => 247,  616 => 246,  604 => 237,  600 => 236,  594 => 235,  580 => 223,  569 => 218,  565 => 217,  561 => 216,  557 => 215,  553 => 214,  547 => 212,  543 => 211,  535 => 206,  531 => 205,  514 => 191,  508 => 187,  500 => 184,  494 => 183,  482 => 181,  479 => 180,  475 => 179,  472 => 178,  460 => 176,  458 => 175,  450 => 174,  446 => 173,  443 => 172,  439 => 171,  435 => 169,  428 => 167,  418 => 162,  410 => 160,  402 => 158,  400 => 157,  394 => 156,  386 => 155,  381 => 154,  378 => 153,  376 => 152,  372 => 150,  368 => 149,  363 => 148,  359 => 147,  355 => 145,  345 => 140,  337 => 138,  329 => 136,  327 => 135,  321 => 134,  313 => 133,  308 => 132,  305 => 131,  303 => 130,  299 => 128,  295 => 127,  289 => 123,  280 => 120,  277 => 119,  273 => 118,  265 => 115,  249 => 102,  245 => 101,  241 => 99,  230 => 94,  221 => 92,  217 => 91,  206 => 87,  200 => 84,  196 => 83,  190 => 81,  186 => 80,  177 => 74,  173 => 73,  168 => 71,  163 => 69,  159 => 68,  149 => 61,  145 => 60,  137 => 55,  131 => 52,  117 => 41,  108 => 38,  106 => 37,  100 => 33,  94 => 30,  90 => 28,  88 => 27,  79 => 23,  74 => 21,  63 => 19,  59 => 18,  52 => 14,  44 => 9,  40 => 8,  36 => 7,  32 => 6,  28 => 5,  22 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "multi-currency.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/multi-currency/multi-currency.twig");
    }
}
