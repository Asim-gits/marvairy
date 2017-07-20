<?php

/* currency-switcher-options.twig */
class __TwigTemplate_11456098a6bd40887c85f6cddf58650f5a50e234be7ac341cddd078c8ced6d2b extends Twig_Template
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
        echo "<div class=\"wcml-section\" id=\"currency-switcher\" ";
        if (twig_test_empty((isset($context["multi_currency_on"]) ? $context["multi_currency_on"] : null))) {
            echo "style=\"display:none\"";
        }
        echo ">
    <div class=\"wcml-section-header\">
        <h3>";
        // line 3
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "main", array()), "html", null, true);
        echo "</h3>
        <p>";
        // line 4
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "main_desc", array()), "html", null, true);
        echo "</p>
    </div>

    <div class=\"wcml-section-content\">

        <div class=\"wcml-section-content-inner\">
            <h4>
                ";
        // line 11
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "order", array()), "html", null, true);
        echo "
                <span style=\"display:none;\" class=\"wcml_currencies_order_ajx_resp\"></span>
            </h4>
            <p class=\"explanation-text\">";
        // line 14
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "order_tip", array()), "html", null, true);
        echo "</p>
            <ul id=\"wcml_currencies_order\" class=\"wcml-cs-currencies-order\">
                ";
        // line 16
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "order", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["code"]) {
            // line 17
            echo "                    <li class=\"wcml_currencies_order_";
            echo twig_escape_filter($this->env, $context["code"], "html", null, true);
            echo "\" cur=\"";
            echo twig_escape_filter($this->env, $context["code"], "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["wc_currencies"]) ? $context["wc_currencies"] : null), $context["code"]), "html", null, true);
            echo " (";
            echo call_user_func_array($this->env->getFunction('get_currency_symbol')->getCallable(), array($context["code"]));
            echo ")</li>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['code'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 19
        echo "            </ul>
            <input type=\"hidden\" id=\"wcml_currencies_order_order_nonce\" value=\"";
        // line 20
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "order_nonce", array()), "html", null, true);
        echo "\"/>
        </div>

        <div class=\"wcml-section-content-inner\">
            <h4>";
        // line 24
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "additional_css", array()), "html", null, true);
        echo "</h4>
            <textarea class=\"large-text\" name=\"currency_switcher_additional_css\" rows=\"5\">";
        // line 25
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "additional_css", array()), "html", null, true);
        echo "</textarea>
        </div>
    </div>
</div>

<div class=\"wcml-section\" id=\"currency-switcher-widget\" ";
        // line 30
        if (twig_test_empty((isset($context["multi_currency_on"]) ? $context["multi_currency_on"] : null))) {
            echo "style=\"display:none\"";
        }
        echo ">
    <div class=\"wcml-section-header\">
        <h3>";
        // line 32
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "widget", array()), "html", null, true);
        echo "</h3>
    </div>
    <div class=\"wcml-section-content\">
        <div class=\"wcml-section-content-inner\">
            <table class=\"wcml-cs-list\" ";
        // line 36
        if (twig_test_empty($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "widget_currency_switchers", array()))) {
            echo " style=\"display: none;\" ";
        }
        echo ">
                <thead>
                    <tr>
                        <th>";
        // line 39
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "preview", array()), "html", null, true);
        echo "</th>
                        <th>";
        // line 40
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "position", array()), "html", null, true);
        echo "</th>
                        <th>";
        // line 41
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "actions", array()), "html", null, true);
        echo "</th>
                    </tr>
                </thead>
                <tbody>
                    ";
        // line 45
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "widget_currency_switchers", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["widget_currency_switcher"]) {
            // line 46
            echo "                        <tr>
                            <td class=\"wcml-cs-cell-preview\">
                                <div class=\"wcml-currency-preview-wrapper\">
                                    <div id=\"wcml_curr_sel_preview\" class=\"wcml-currency-preview ";
            // line 49
            echo twig_escape_filter($this->env, $this->getAttribute($context["widget_currency_switcher"], "id", array(), "array"), "html", null, true);
            echo "\">
                                        ";
            // line 50
            echo $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "preview", array()), $this->getAttribute($context["widget_currency_switcher"], "id", array(), "array"), array(), "array");
            echo "
                                    </div>
                                </div>
                            </td>
                            <td class=\"wcml-cs-widget-name\">
                               ";
            // line 55
            echo twig_escape_filter($this->env, $this->getAttribute($context["widget_currency_switcher"], "name", array(), "array"), "html", null, true);
            echo "
                            </td>
                            <td class=\"wcml-cs-actions\">
                                <a title=\"";
            // line 58
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "edit", array()), "html", null, true);
            echo "\"
                                   class=\"edit_currency_switcher js-wcml-cs-dialog-trigger\"
                                   data-switcher=\"";
            // line 60
            echo twig_escape_filter($this->env, $this->getAttribute($context["widget_currency_switcher"], "id", array(), "array"), "html", null, true);
            echo "\"
                                   data-dialog=\"wcml_currency_switcher_options_";
            // line 61
            echo twig_escape_filter($this->env, $this->getAttribute($context["widget_currency_switcher"], "id", array(), "array"), "html", null, true);
            echo "\"
                                   data-content=\"wcml_currency_switcher_options_";
            // line 62
            echo twig_escape_filter($this->env, $this->getAttribute($context["widget_currency_switcher"], "id", array(), "array"), "html", null, true);
            echo "\"
                                   data-height=\"800\" data-width=\"700\">
                                    <i class=\"otgs-ico-edit\"></i>
                                </a>
                                <a title=\"";
            // line 66
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "delete", array()), "html", null, true);
            echo "\" class=\"delete_currency_switcher\" data-switcher=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["widget_currency_switcher"], "id", array(), "array"), "html", null, true);
            echo "\" href=\"#\">
                                    <i class=\"otgs-ico-delete\" title=\"";
            // line 67
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "delete", array()), "html", null, true);
            echo "\"></i>
                                </a>
                            </td>
                        </tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['widget_currency_switcher'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 72
        echo "                    <tr class=\"wcml-cs-empty-row\" style=\"display: none\">
                        <td class=\"wcml-cs-cell-preview\">
                            <div class=\"wcml-currency-preview-wrapper\">
                                <div id=\"wcml_curr_sel_preview\" class=\"wcml-currency-preview\"></div>
                            </div>
                        </td>
                        <td class=\"wcml-cs-widget-name\">
                        </td>
                        <td class=\"wcml-cs-actions\">
                            <a title=\"";
        // line 81
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "edit", array()), "html", null, true);
        echo "\"
                               class=\"edit_currency_switcher js-wcml-cs-dialog-trigger\"
                               data-switcher=\"\"
                               data-dialog=\"\"
                               data-content=\"\"
                               data-height=\"800\" data-width=\"700\">
                                <i class=\"otgs-ico-edit\"></i>
                            </a>
                            <a title=\"";
        // line 89
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "delete", array()), "html", null, true);
        echo "\" class=\"delete_currency_switcher\" data-switcher=\"\" href=\"#\">
                                <i class=\"otgs-ico-delete\" title=\"";
        // line 90
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "delete", array()), "html", null, true);
        echo "\"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class=\"tablenav top clearfix\">
                <button type=\"button\" class=\"button button-secondary alignright wcml_add_cs_sidebar js-wcml-cs-dialog-trigger\"
                        data-switcher=\"new_widget\"
                        data-dialog=\"wcml_currency_switcher_options_new_widget\"
                        data-content=\"wcml_currency_switcher_options_new_widget\"
                        data-height=\"800\" data-width=\"700\"
                        ";
        // line 102
        if (twig_test_empty($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "available_sidebars", array()))) {
            echo "style=\"display:none\"";
        }
        echo ">
                    <i class=\"otgs-ico-add otgs-ico-sm\"></i>
                    ";
        // line 104
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "add_widget", array()), "html", null, true);
        echo "
                </button>
            </div>
            <input type=\"hidden\" id=\"wcml_delete_currency_switcher_nonce\" value=\"";
        // line 107
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "delete_nonce", array()), "html", null, true);
        echo "\"/>
        </div>
    </div>
</div>

<div class=\"wcml-section\" id=\"currency-switcher-product\" ";
        // line 112
        if (twig_test_empty((isset($context["multi_currency_on"]) ? $context["multi_currency_on"] : null))) {
            echo "style=\"display:none\"";
        }
        echo ">
    <div class=\"wcml-section-header\">
        <h3>";
        // line 114
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "product_page", array()), "html", null, true);
        echo "</h3>
    </div>
    <div class=\"wcml-section-content\">
        <div class=\"wcml-section-content-inner\">
            <ul class=\"wcml_curr_visibility\">
                <li>
                    <label>
                        <input type=\"checkbox\" name=\"currency_switcher_product_visibility\" value=\"1\" ";
        // line 121
        if ($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "visibility_on", array())) {
            echo "checked=\"checked\"";
        }
        echo ">
                        ";
        // line 122
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "visibility_label", array()), "html", null, true);
        echo "
                    </label>
                </li>
            </ul>
            <div>
                <table class=\"wcml-cs-list\" ";
        // line 127
        if ( !$this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "visibility_on", array())) {
            echo " style=\"display:none\" ";
        }
        echo ">
                    <thead>
                        <tr>
                            <th>";
        // line 130
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "preview", array()), "html", null, true);
        echo "</th>
                            <th>";
        // line 131
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "action", array()), "html", null, true);
        echo "</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class=\"wcml-cs-cell-preview\">
                                <div class=\"wcml-currency-preview-wrapper\">
                                    <div id=\"wcml_curr_sel_preview\" class=\"wcml-currency-preview product\">
                                        ";
        // line 139
        echo $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "preview", array()), "product", array(), "array");
        echo "
                                    </div>
                                </div>
                            </td>

                            <td class=\"wcml-cs-actions\">
                                <a title=\"";
        // line 145
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["currency_switcher"]) ? $context["currency_switcher"] : null), "headers", array()), "edit", array()), "html", null, true);
        echo "\"
                                   class=\"edit_currency_switcher js-wcml-cs-dialog-trigger\"
                                   data-switcher=\"product\"
                                   data-dialog=\"wcml_currency_switcher_options_product\"
                                   data-content=\"wcml_currency_switcher_options_product\"
                                   data-height=\"800\" data-width=\"700\">
                                    <i class=\"otgs-ico-edit\"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "currency-switcher-options.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  319 => 145,  310 => 139,  299 => 131,  295 => 130,  287 => 127,  279 => 122,  273 => 121,  263 => 114,  256 => 112,  248 => 107,  242 => 104,  235 => 102,  220 => 90,  216 => 89,  205 => 81,  194 => 72,  183 => 67,  177 => 66,  170 => 62,  166 => 61,  162 => 60,  157 => 58,  151 => 55,  143 => 50,  139 => 49,  134 => 46,  130 => 45,  123 => 41,  119 => 40,  115 => 39,  107 => 36,  100 => 32,  93 => 30,  85 => 25,  81 => 24,  74 => 20,  71 => 19,  56 => 17,  52 => 16,  47 => 14,  41 => 11,  31 => 4,  27 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "currency-switcher-options.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/multi-currency/currency-switcher-options.twig");
    }
}
