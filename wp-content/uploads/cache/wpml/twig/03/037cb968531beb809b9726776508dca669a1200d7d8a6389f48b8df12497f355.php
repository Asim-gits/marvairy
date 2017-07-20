<?php

/* custom-currency-options.twig */
class __TwigTemplate_e0047ca8ce3b54fa89c1bd609e58986afc4300d0658db1b1bd45aa1ed397e90d extends Twig_Template
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
        echo "<div class=\"wcml-dialog hidden\" id=\"wcml_currency_options_";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\" title=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "title", array()), "html", null, true);
        echo "\">

    <div class=\"wcml_currency_options wcml-co-dialog\">

        <form id=\"wcml_currency_options_form_";
        // line 5
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\" method=\"post\" action=\"\">

            ";
        // line 7
        if (twig_test_empty($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()))) {
            // line 8
            echo "            <div class=\"wpml-form-row currency_code\">
                <label for=\"wcml_currency_options_code_";
            // line 9
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "select", array()), "html", null, true);
            echo "</label>
                <select name=\"currency_options[code]\" id=\"wcml_currency_options_code_";
            // line 10
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
            echo "\">
                    ";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "wc_currencies", array()));
            foreach ($context['_seq'] as $context["code"] => $context["name"]) {
                // line 12
                echo "                    ";
                if (((null === $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currencies", array()), $context["code"])) && ($context["code"] != $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "default_currency", array())))) {
                    // line 13
                    echo "                    <option value=\"";
                    echo twig_escape_filter($this->env, $context["code"], "html", null, true);
                    echo "\" ";
                    if (($context["code"] == $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()))) {
                        echo "selected=\"selected\"";
                    }
                    echo " data-symbol=\"";
                    echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('get_currency_symbol')->getCallable(), array($context["code"])), "html", null, true);
                    echo "\" >";
                    echo $context["name"];
                    echo " (";
                    echo call_user_func_array($this->env->getFunction('get_currency_symbol')->getCallable(), array($context["code"]));
                    echo " )</option>
                    ";
                }
                // line 15
                echo "                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['code'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 16
            echo "                </select>
            </div>
            ";
        } else {
            // line 19
            echo "            <input type=\"hidden\" name=\"currency_options[code]\" value=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
            echo "\" />
            ";
        }
        // line 21
        echo "
            <div class=\"wpml-form-row wcml-co-exchange-rate\">
                <label for=\"wcml_currency_options_rate_";
        // line 23
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rate", array()), "label", array()), "html", null, true);
        echo "</label>
                <div class=\"wcml-co-set-rate\">
                    1 ";
        // line 25
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "default_currency", array()), "html", null, true);
        echo " = <input name=\"currency_options[rate]\" size=\"5\" type=\"number\"
                                                           class=\"wcml-exchange-rate";
        // line 26
        if ((isset($context["automatic_rates"]) ? $context["automatic_rates"] : null)) {
            echo " wcml-tip";
        }
        echo "\"
                                                           min=\"";
        // line 27
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rate", array()), "min", array()), "html", null, true);
        echo "\" step=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rate", array()), "step", array()), "html", null, true);
        echo "\"  value=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "rate", array()), "html", null, true);
        echo "\" data-message=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rate", array()), "only_numeric", array()), "html", null, true);
        echo "\"
                                                           id=\"wcml_currency_options_rate_";
        // line 28
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\"
                                                            ";
        // line 29
        if ((isset($context["automatic_rates"]) ? $context["automatic_rates"] : null)) {
            echo "readonly=\"readonly\" data-tip=\"";
            echo twig_escape_filter($this->env, (isset($context["automatic_rates_tip"]) ? $context["automatic_rates_tip"] : null), "html", null, true);
            echo "\"";
        }
        echo "/>
                    <span class=\"this-currency\">";
        // line 30
        echo twig_escape_filter($this->env, (isset($context["current_currency"]) ? $context["current_currency"] : null), "html", null, true);
        echo "</span>
                    <span class=\"wcml-error\" style=\"display: none\">";
        // line 31
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "number_error", array()), "html", null, true);
        echo "</span>
                    ";
        // line 32
        if ($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "updated", array())) {
            // line 33
            echo "                    <small>
                        ";
            // line 34
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rate", array()), "set_on", array()), "html", null, true);
            echo " <i>";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rate", array()), "previous", array()), "html", null, true);
            echo "</i>
                    </small>
                    ";
        }
        // line 37
        echo "                </div>
            </div>

            <hr>

            <div class=\"wpml-form-row wcml-co-preview\">
                <label><strong>";
        // line 43
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "preview", array()), "label", array()), "html", null, true);
        echo "</strong></label>
                <p class=\"wcml-co-preview-value\">
                    ";
        // line 45
        echo $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "preview", array()), "value", array());
        echo "
                </p>
            </div>

            <div class=\"wpml-form-row\">
                <label for=\"wcml_currency_options_position_";
        // line 50
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "position", array()), "label", array()), "html", null, true);
        echo "</label>
                <select class=\"currency_option_position\" name=\"currency_options[position]\" id=\"wcml_currency_options_position_";
        // line 51
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">
                    <option value=\"left\" ";
        // line 52
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "position", array()) == "left")) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "position", array()), "left", array()), "html", null, true);
        echo "</option>
                    <option value=\"right\" ";
        // line 53
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "position", array()) == "right")) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "position", array()), "right", array()), "html", null, true);
        echo "</option>
                    <option value=\"left_space\" ";
        // line 54
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "position", array()) == "left_space")) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "position", array()), "left_space", array()), "html", null, true);
        echo "</option>
                    <option value=\"right_space\" ";
        // line 55
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "position", array()) == "right_space")) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "position", array()), "right_space", array()), "html", null, true);
        echo "</option>
                </select>
            </div>

            <div class=\"wpml-form-row\">
                <label for=\"wcml_currency_options_thousand_";
        // line 60
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "thousand_sep", array()), "label", array()), "html", null, true);
        echo "</label>
                <input name=\"currency_options[thousand_sep]\" type=\"text\"
                    class=\"currency_option_input currency_option_thousand_sep\" value=\"";
        // line 62
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "thousand_sep", array()), "html", null, true);
        echo "\"
                    id=\"wcml_currency_options_thousand_";
        // line 63
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\" />
            </div>

            <div class=\"wpml-form-row\">
                <label for=\"wcml_currency_options_decimal_";
        // line 67
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "decimal_sep", array()), "label", array()), "html", null, true);
        echo "</label>
                <input name=\"currency_options[decimal_sep]\" type=\"text\"
                    class=\"currency_option_input currency_option_decimal_sep\" value=\"";
        // line 69
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "decimal_sep", array()), "html", null, true);
        echo "\"
                    id=\"wcml_currency_options_decimal_";
        // line 70
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\" />
            </div>

            <div class=\"wpml-form-row\">
                <label for=\"wcml_currency_options_decimals_";
        // line 74
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\" id=\"wcml_currency_options_decimals_";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "num_decimals", array()), "label", array()), "html", null, true);
        echo "</label>
                <input name=\"currency_options[num_decimals]\" type=\"number\" class=\"currency_option_decimals\"
                    value=\"";
        // line 76
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "num_decimals", array()), "html", null, true);
        echo "\" min=\"0\" step=\"1\" max=\"5\" data-message=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "num_decimals", array()), "only_numeric", array()), "html", null, true);
        echo "\" />
            </div>

            <hr/>

            <div class=\"wpml-form-row\">
                <label for=\"wcml_currency_options_rounding_";
        // line 82
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rounding", array()), "label", array()), "html", null, true);
        echo " <i class=\"wcml-tip otgs-ico-help\" data-tip=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rounding", array()), "rounding_tooltip", array()), "html", null, true);
        echo "\"></i></label>
                <select name=\"currency_options[rounding]\" id=\"wcml_currency_options_rounding_";
        // line 83
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">
                    <option value=\"disabled\" ";
        // line 84
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "rounding", array()) == "disabled")) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rounding", array()), "disabled", array()), "html", null, true);
        echo "</option>
                    <option value=\"up\" ";
        // line 85
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "rounding", array()) == "up")) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rounding", array()), "up", array()), "html", null, true);
        echo "</option>
                    <option value=\"down\" ";
        // line 86
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "rounding", array()) == "down")) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rounding", array()), "down", array()), "html", null, true);
        echo "</option>
                    <option value=\"nearest\" ";
        // line 87
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "rounding", array()) == "nearest")) {
            echo "selected=\"nearest\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rounding", array()), "nearest", array()), "html", null, true);
        echo "</option>
                </select>
            </div>

            <div class=\"wpml-form-row\">
                <label for=\"wcml_currency_options_increment_";
        // line 92
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rounding", array()), "increment", array()), "html", null, true);
        echo " <i class=\"wcml-tip otgs-ico-help\" data-tip=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rounding", array()), "increment_tooltip", array()), "html", null, true);
        echo "\"></i></label>
                <select name=\"currency_options[rounding_increment]\" id=\"wcml_currency_options_increment_";
        // line 93
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">
                    <option value=\"1\" ";
        // line 94
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "rounding_increment", array()) == "1")) {
            echo "selected=\"selected\"";
        }
        echo ">1</option>
                    <option value=\"10\" ";
        // line 95
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "rounding_increment", array()) == "10")) {
            echo "selected=\"selected\"";
        }
        echo ">10</option>
                    <option value=\"100\" ";
        // line 96
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "rounding_increment", array()) == "100")) {
            echo "selected=\"selected\"";
        }
        echo ">100</option>
                    <option value=\"1000\" ";
        // line 97
        if (($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "rounding_increment", array()) == "1000")) {
            echo "selected=\"selected\"";
        }
        echo ">1000</option>
                </select>
            </div>

            <div class=\"wpml-form-row\">
                <label for=\"wcml_currency_options_subtract_";
        // line 102
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "autosubtract", array()), "label", array()), "html", null, true);
        echo " <i class=\"wcml-tip otgs-ico-help\" data-tip=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "rounding", array()), "autosubtract_tooltip", array()), "html", null, true);
        echo "\"></i></label>

                <input name=\"currency_options[auto_subtract]\" class=\"abstract_amount\"
                    value=\"";
        // line 105
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency", array()), "auto_subtract", array()), "html", null, true);
        echo "\" type=\"number\" data-message=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "autosbtract", array()), "only_numeric", array()), "html", null, true);
        echo "\"
                    id=\"wcml_currency_options_subtract_";
        // line 106
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_code", array()), "html", null, true);
        echo "\"/>
            </div>


            <footer class=\"wpml-dialog-footer\">
                <input type=\"button\" class=\"cancel wcml-dialog-close-button wpml-dialog-close-button alignleft\"
                    value=\"";
        // line 112
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cancel", array()), "html", null, true);
        echo "\" data-currency=\"";
        echo twig_escape_filter($this->env, (isset($context["current_currency"]) ? $context["current_currency"] : null), "html", null, true);
        echo "\" data-symbol=\"";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('get_currency_symbol')->getCallable(), array((isset($context["current_currency"]) ? $context["current_currency"] : null))), "html", null, true);
        echo "\" />&nbsp;
                <input type=\"submit\" class=\"wcml-dialog-close-button wpml-dialog-close-button button-primary currency_options_save alignright\"
                    value=\"";
        // line 114
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "save", array()), "html", null, true);
        echo "\" data-currency=\"";
        echo twig_escape_filter($this->env, (isset($context["current_currency"]) ? $context["current_currency"] : null), "html", null, true);
        echo "\" data-symbol=\"";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('get_currency_symbol')->getCallable(), array((isset($context["current_currency"]) ? $context["current_currency"] : null))), "html", null, true);
        echo "\" data-stay=\"1\" />
            </footer>

        </form>

    </div>

</div>

";
    }

    public function getTemplateName()
    {
        return "custom-currency-options.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  398 => 114,  389 => 112,  380 => 106,  374 => 105,  364 => 102,  354 => 97,  348 => 96,  342 => 95,  336 => 94,  332 => 93,  324 => 92,  312 => 87,  304 => 86,  296 => 85,  288 => 84,  284 => 83,  276 => 82,  265 => 76,  256 => 74,  249 => 70,  245 => 69,  238 => 67,  231 => 63,  227 => 62,  220 => 60,  208 => 55,  200 => 54,  192 => 53,  184 => 52,  180 => 51,  174 => 50,  166 => 45,  161 => 43,  153 => 37,  145 => 34,  142 => 33,  140 => 32,  136 => 31,  132 => 30,  124 => 29,  120 => 28,  110 => 27,  104 => 26,  100 => 25,  93 => 23,  89 => 21,  83 => 19,  78 => 16,  72 => 15,  56 => 13,  53 => 12,  49 => 11,  45 => 10,  39 => 9,  36 => 8,  34 => 7,  29 => 5,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "custom-currency-options.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/multi-currency/custom-currency-options.twig");
    }
}
