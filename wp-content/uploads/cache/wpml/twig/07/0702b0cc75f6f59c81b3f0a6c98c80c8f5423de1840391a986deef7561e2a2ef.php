<?php

/* currency-switcher-options-dialog.twig */
class __TwigTemplate_379deb1dcf0918a7e89b372a96a5013ebc2f77e5f3cf9e97d0ba3ab598f0d675 extends Twig_Template
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
        echo "<div class=\"wcml-dialog hidden\" id=\"wcml_currency_switcher_options_";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_switcher", array()), "html", null, true);
        echo "\" title=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "title", array()), "html", null, true);
        echo "\">

    <div id=\"wcml_currency_switcher_options_form_";
        // line 3
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_switcher", array()), "html", null, true);
        echo "\" class=\"wcml-currency-switcher-options-form\">

        <div id=\"wcml_curr_sel_preview_wrap\" class=\"wcml-currency-preview-wrapper\">
            <strong class=\"wcml-currency-preview-label\">";
        // line 6
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "preview", array()), "html", null, true);
        echo "</strong>
            <input type=\"hidden\" id=\"wcml_currencies_switcher_preview_nonce\" value=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "preview_nonce", array()), "html", null, true);
        echo "\"/>
            <div id=\"wcml_curr_sel_preview\" class=\"wcml-currency-preview ";
        // line 8
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_switcher", array()), "html", null, true);
        echo "\">
                ";
        // line 9
        echo $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "switcher_preview", array());
        echo "
            </div>
        </div>

        <div id=\"wcml_curr_options_wrap\" class=\"wcml-currency-switcher-options\">
            ";
        // line 14
        if (($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_switcher", array()) == "new_widget")) {
            // line 15
            echo "
                    <h4>";
            // line 16
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "widgets", array()), "widget_area", array()), "html", null, true);
            echo "</h4>
                    <select id=\"wcml-cs-widget\">
                        <option selected disabled>";
            // line 18
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "widgets", array()), "choose_label", array()), "html", null, true);
            echo "</option>
                        ";
            // line 19
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "widgets", array()), "available_sidebars", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["sidebar"]) {
                // line 20
                echo "                            <option value=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["sidebar"], "id", array(), "array"), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["sidebar"], "name", array(), "array"), "html", null, true);
                echo "</option>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sidebar'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 22
            echo "                    </select>

            ";
        }
        // line 25
        echo "
            <h4>";
        // line 26
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "switcher_style", array()), "label", array()), "html", null, true);
        echo "</h4>
            <ul class=\"wcml_curr_style\">
                <li>
                    <label>
                        <select id=\"currency_switcher_style\">
                            <optgroup label=\"";
        // line 31
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "switcher_style", array()), "core", array()), "html", null, true);
        echo "\">
                                ";
        // line 32
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "switcher_templates", array()), "core", array(), "array"));
        foreach ($context['_seq'] as $context["switcher_template_id"] => $context["switcher_template"]) {
            // line 33
            echo "                                    <option value=\"";
            echo twig_escape_filter($this->env, $context["switcher_template_id"], "html", null, true);
            echo "\" ";
            if (($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "switcher_style", array()) == $context["switcher_template_id"])) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["switcher_template"], "name", array(), "array"), "html", null, true);
            echo "</option>
                                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['switcher_template_id'], $context['switcher_template'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 35
        echo "                            </optgroup>
                            <optgroup label=\"";
        // line 36
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "switcher_style", array()), "custom", array()), "html", null, true);
        echo "\">
                                ";
        // line 37
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "switcher_templates", array()), "custom", array(), "array"));
        foreach ($context['_seq'] as $context["switcher_template_id"] => $context["switcher_template"]) {
            // line 38
            echo "                                    <option value=\"";
            echo twig_escape_filter($this->env, $context["switcher_template_id"], "html", null, true);
            echo "\" ";
            if (($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "switcher_style", array()) == $context["switcher_template_id"])) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["switcher_template"], "name", array(), "array"), "html", null, true);
            echo "</option>
                                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['switcher_template_id'], $context['switcher_template'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 40
        echo "                            </optgroup>
                        </select>
                    </label>
                </li>
            </ul>

            <h4>";
        // line 46
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "template", array()), "label", array()), "html", null, true);
        echo "</h4>
            <input type=\"text\" name=\"wcml_curr_template\" size=\"50\" value=\"";
        // line 47
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "template", array()), "html", null, true);
        echo "\"/>
            <p class=\"explanation-text\">
                <span class=\"display-block\">";
        // line 49
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "template", array()), "template_tip", array()), "html", null, true);
        echo "</span>
                <span class=\"display-block\">";
        // line 50
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "template", array()), "parameters", array()), "html", null, true);
        echo ": ";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "template", array()), "parameters_list", array()), "html", null, true);
        echo "</span>
                <span class=\"display-block js-toggle-cs-style\" ";
        // line 51
        if (($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "style", array()) != "list")) {
            echo "style=\"display: none;\"";
        }
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "switcher_style", array()), "allowed_tags", array()), "html", null, true);
        echo "</span>
            </p>
            <input type=\"hidden\" id=\"currency_switcher_default\" value=\"";
        // line 53
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "template_default", array()), "html", null, true);
        echo "\"/>

            ";
        // line 55
        if (($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_switcher", array()) != "product")) {
            // line 56
            echo "                <h4>";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "widgets", array()), "widget_title", array()), "html", null, true);
            echo "</h4>
                <input type=\"text\" name=\"wcml_cs_widget_title\" size=\"50\" value=\"";
            // line 57
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "widget_title", array()), "html", null, true);
            echo "\"/>
            ";
        }
        // line 59
        echo "
            <div class=\"js-wcml-cs-panel-colors wcml-cs-panel-colors\">
                <h4>";
        // line 61
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "colors", array()), "label", array()), "html", null, true);
        echo "</h4>

                <label for=\"wcml-cs-";
        // line 63
        echo twig_escape_filter($this->env, (isset($context["id"]) ? $context["id"] : null), "html", null, true);
        echo "-colorpicker-preset\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "colors", array()), "theme", array()), "html", null, true);
        echo "</label>
                <select name=\"wcml-cs-";
        // line 64
        echo twig_escape_filter($this->env, (isset($context["id"]) ? $context["id"] : null), "html", null, true);
        echo "-colorpicker-preset\" class=\"js-wcml-cs-colorpicker-preset\">
                    <option selected disabled>-- ";
        // line 65
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "colors", array()), "select_option_choose", array()), "html", null, true);
        echo " --</option>
                    ";
        // line 66
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["color_schemes"]) ? $context["color_schemes"] : null));
        foreach ($context['_seq'] as $context["scheme_id"] => $context["scheme"]) {
            // line 67
            echo "                        <option value=\"";
            echo twig_escape_filter($this->env, $context["scheme_id"], "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["scheme"], "label", array()), "html", null, true);
            echo "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['scheme_id'], $context['scheme'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 69
        echo "                </select>

                <div>
                    <table>
                        <tr>
                            <td>
                            </td>
                            <th>";
        // line 76
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "colors", array()), "normal", array()), "html", null, true);
        echo "</th>
                            <th>";
        // line 77
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "colors", array()), "hover", array()), "html", null, true);
        echo "</th>
                        </tr>
                        ";
        // line 79
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["options"]) ? $context["options"] : null));
        foreach ($context['_seq'] as $context["option_id"] => $context["option"]) {
            // line 80
            echo "                            <tr>
                                <td>";
            // line 81
            echo twig_escape_filter($this->env, $context["option"], "html", null, true);
            echo "</td>
                                ";
            // line 82
            if ( !(null === $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "options", array()), ($context["option_id"] . "_normal"), array(), "array"))) {
                // line 83
                echo "                                    <td class=\"js-wcml-cs-colorpicker-wrapper\">
                                        <input class=\"js-wcml-cs-colorpicker js-wcml-cs-color-";
                // line 84
                echo twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "_normal\" type=\"text\" size=\"7\"
                                               id=\"wcml-cs-";
                // line 85
                echo twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "-normal\" name=\"";
                echo twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "_normal\"
                                               value=\"";
                // line 86
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "options", array()), ($context["option_id"] . "_normal"), array(), "array"), "html", null, true);
                echo "\" style=\"\">
                                    </td>
                                ";
            }
            // line 89
            echo "                                ";
            if ( !(null === $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "options", array()), ($context["option_id"] . "_hover"), array(), "array"))) {
                // line 90
                echo "                                    <td class=\"js-wcml-cs-colorpicker-wrapper\">
                                        <input class=\"js-wcml-cs-colorpicker js-wcml-cs-color-";
                // line 91
                echo twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "_hover\" type=\"text\" size=\"7\"
                                               id=\"wcml-cs-";
                // line 92
                echo twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "-hover\" name=\"";
                echo twig_escape_filter($this->env, $context["option_id"], "html", null, true);
                echo "_hover\"
                                               value=\"";
                // line 93
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["args"]) ? $context["args"] : null), "options", array()), ($context["option_id"] . "_hover"), array(), "array"), "html", null, true);
                echo "\" style=\"\">
                                    </td>
                                ";
            }
            // line 96
            echo "                            </tr>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['option_id'], $context['option'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 98
        echo "                    </table>

                </div>
            </div>
        </div>

    </div>
        <footer class=\"wpml-dialog-footer\">
            <input type=\"button\" class=\"cancel wcml-dialog-close-button wpml-dialog-close-button alignleft\" value=\"";
        // line 106
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cancel", array()), "html", null, true);
        echo "\"/>&nbsp;
            <input type=\"submit\" class=\"wcml-dialog-close-button wpml-dialog-close-button button-primary currency_switcher_save alignright\"
                   value=\"";
        // line 108
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "save", array()), "html", null, true);
        echo "\" data-switcher=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "current_switcher", array()), "html", null, true);
        echo "\" data-stay=\"1\" />
            <input type=\"hidden\" id=\"wcml_currencies_switcher_save_settings_nonce\" value=\"";
        // line 109
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "save_settings_nonce", array()), "html", null, true);
        echo "\"/>
            <input type=\"hidden\" id=\"wcml_currencies_switcher_id\" value=\"";
        // line 110
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["args"]) ? $context["args"] : null), "currency_switcher", array()), "html", null, true);
        echo "\"/>
        </footer>

    </div>

";
    }

    public function getTemplateName()
    {
        return "currency-switcher-options-dialog.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  342 => 110,  338 => 109,  332 => 108,  327 => 106,  317 => 98,  310 => 96,  304 => 93,  298 => 92,  294 => 91,  291 => 90,  288 => 89,  282 => 86,  276 => 85,  272 => 84,  269 => 83,  267 => 82,  263 => 81,  260 => 80,  256 => 79,  251 => 77,  247 => 76,  238 => 69,  227 => 67,  223 => 66,  219 => 65,  215 => 64,  209 => 63,  204 => 61,  200 => 59,  195 => 57,  190 => 56,  188 => 55,  183 => 53,  174 => 51,  168 => 50,  164 => 49,  159 => 47,  155 => 46,  147 => 40,  132 => 38,  128 => 37,  124 => 36,  121 => 35,  106 => 33,  102 => 32,  98 => 31,  90 => 26,  87 => 25,  82 => 22,  71 => 20,  67 => 19,  63 => 18,  58 => 16,  55 => 15,  53 => 14,  45 => 9,  41 => 8,  37 => 7,  33 => 6,  27 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "currency-switcher-options-dialog.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/multi-currency/currency-switcher-options-dialog.twig");
    }
}
