<?php

/* settings-ui.twig */
class __TwigTemplate_4b411d1f490f09ef7e6a7b74293ceffa84f8f9480b2d2893f5ab2baa85259866 extends Twig_Template
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
        echo "<form method=\"post\" action=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "action", array()), "html", null, true);
        echo "\">

    <div class=\"wcml-section\">
        <div class=\"wcml-section-header\">
            <h3>
                ";
        // line 6
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "translation_interface", array()), "heading", array()), "html", null, true);
        echo "
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "translation_interface", array()), "tip", array()), "html", null, true);
        echo "\"></i>
            </h3>
        </div>
        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"radio\" name=\"trnsl_interface\" value=\"";
        // line 14
        echo twig_escape_filter($this->env, (isset($context["wpml_translation"]) ? $context["wpml_translation"] : null), "html", null, true);
        echo "\"
                        ";
        // line 15
        if (($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "translation_interface", array()), "controls_value", array()) == (isset($context["wpml_translation"]) ? $context["wpml_translation"] : null))) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_trsl_interface_wcml\" />
                    <label for=\"wcml_trsl_interface_wcml\">";
        // line 16
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "translation_interface", array()), "wcml", array()), "label", array()), "html", null, true);
        echo "</label>
                </li>
                <li>
                    <input type=\"radio\" name=\"trnsl_interface\" value=\"";
        // line 19
        echo twig_escape_filter($this->env, (isset($context["native_translation"]) ? $context["native_translation"] : null), "html", null, true);
        echo "\"
                        ";
        // line 20
        if (($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "translation_interface", array()), "controls_value", array()) == (isset($context["native_translation"]) ? $context["native_translation"] : null))) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_trsl_interface_native\" />
                    <label for=\"wcml_trsl_interface_native\">";
        // line 21
        echo $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "translation_interface", array()), "native", array()), "label", array());
        echo "</label>
                </li>
            </ul>

        </div> <!-- .wcml-section-content -->

    </div> <!-- .wcml-section -->

    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                ";
        // line 33
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "synchronization", array()), "heading", array()), "html", null, true);
        echo "
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 34
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "synchronization", array()), "tip", array()), "html", null, true);
        echo "\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"checkbox\" name=\"products_sync_date\" value=\"1\"
                        ";
        // line 43
        if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "synchronization", array()), "sync_date", array()), "value", array()) == 1)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_products_sync_date\" />
                    <label for=\"wcml_products_sync_date\">";
        // line 44
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "synchronization", array()), "sync_date", array()), "label", array()), "html", null, true);
        echo "</label>
                </li>
                <li>
                    <input type=\"checkbox\" name=\"products_sync_order\" value=\"1\"
                        ";
        // line 48
        if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "synchronization", array()), "sync_order", array()), "value", array()) == 1)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_products_sync_order\" />
                    <label for=\"wcml_products_sync_order\">";
        // line 49
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "synchronization", array()), "sync_order", array()), "label", array()), "html", null, true);
        echo "</label>
                </li>
            </ul>

        </div>

    </div>


    <div class=\"wcml-section\">

        <div class=\"wcml-section-header\">
            <h3>
                ";
        // line 62
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "file_sync", array()), "heading", array()), "html", null, true);
        echo "
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 63
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "file_sync", array()), "tip", array()), "html", null, true);
        echo "\"></i>
            </h3>
        </div>

        <div class=\"wcml-section-content\">

            <ul>
                <li>
                    <input type=\"radio\" name=\"wcml_file_path_sync\" value=\"1\"
                        ";
        // line 72
        if (($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "file_sync", array()), "value", array()) == 1)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_file_path_sync_auto\" />
                    <label for=\"wcml_file_path_sync_auto\">";
        // line 73
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "file_sync", array()), "label_same", array()), "html", null, true);
        echo "</label>
                </li>
                <li>
                    <input type=\"radio\" name=\"wcml_file_path_sync\" value=\"0\"
                        ";
        // line 77
        if (($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "file_sync", array()), "value", array()) == 0)) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_file_path_sync_self\" />
                    <label for=\"wcml_file_path_sync_self\">";
        // line 78
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "file_sync", array()), "label_diff", array()), "html", null, true);
        echo "</label>
                </li>
            </ul>


        </div> <!-- .wcml-section-content -->

    </div> <!-- .wcml-section -->


    <div class=\"wcml-section\">
        <div class=\"wcml-section-header\">
            <h3>
                ";
        // line 91
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "heading", array()), "html", null, true);
        echo "
                <i class=\"otgs-ico-help wcml-tip\" data-tip=\"";
        // line 92
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "tip", array()), "html", null, true);
        echo "\"></i>
            </h3>
        </div>
        <div class=\"wcml-section-content\">
            <div class=\"wcml-section-content-inner\">
                <h4>
                    ";
        // line 98
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "lang_switch", array()), "heading", array()), "html", null, true);
        echo "
                </h4>
                <ul>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_lang\" value=\"";
        // line 102
        echo twig_escape_filter($this->env, (isset($context["wcml_cart_sync"]) ? $context["wcml_cart_sync"] : null), "html", null, true);
        echo "\"
                                ";
        // line 103
        if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "lang_switch", array()), "value", array()) == (isset($context["wcml_cart_sync"]) ? $context["wcml_cart_sync"] : null))) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_cart_sync_lang_sync\" />
                        <label for=\"wcml_cart_sync_lang_sync\">";
        // line 104
        echo $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "lang_switch", array()), "sync_label", array());
        echo "</label>
                    </li>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_lang\" value=\"";
        // line 107
        echo twig_escape_filter($this->env, (isset($context["wcml_cart_clear"]) ? $context["wcml_cart_clear"] : null), "html", null, true);
        echo "\"
                                ";
        // line 108
        if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "lang_switch", array()), "value", array()) == (isset($context["wcml_cart_clear"]) ? $context["wcml_cart_clear"] : null))) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_cart_sync_lang_clear\" />
                        <label for=\"wcml_cart_sync_lang_clear\">";
        // line 109
        echo $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "lang_switch", array()), "clear_label", array());
        echo "</label>
                    </li>
                </ul>
            </div>
            <div class=\"wcml-section-content-inner\">
                <h4>
                    ";
        // line 115
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "currency_switch", array()), "heading", array()), "html", null, true);
        echo "
                </h4>
                <ul>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_currencies\" value=\"";
        // line 119
        echo twig_escape_filter($this->env, (isset($context["wcml_cart_sync"]) ? $context["wcml_cart_sync"] : null), "html", null, true);
        echo "\"
                                ";
        // line 120
        if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "currency_switch", array()), "value", array()) == (isset($context["wcml_cart_sync"]) ? $context["wcml_cart_sync"] : null))) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_cart_sync_curr_sync\" />
                        <label for=\"wcml_cart_sync_curr_sync\">";
        // line 121
        echo $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "currency_switch", array()), "sync_label", array());
        echo "</label>
                    </li>
                    <li>
                        <input type=\"radio\" name=\"cart_sync_currencies\" value=\"";
        // line 124
        echo twig_escape_filter($this->env, (isset($context["wcml_cart_clear"]) ? $context["wcml_cart_clear"] : null), "html", null, true);
        echo "\"
                                ";
        // line 125
        if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "currency_switch", array()), "value", array()) == (isset($context["wcml_cart_clear"]) ? $context["wcml_cart_clear"] : null))) {
            echo " checked=\"checked\"";
        }
        echo " id=\"wcml_cart_sync_curr_clear\" />
                        <label for=\"wcml_cart_sync_curr_clear\">";
        // line 126
        echo $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "currency_switch", array()), "clear_label", array());
        echo "</label>
                    </li>
                </ul>
                <p>
                    ";
        // line 130
        echo $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "cart_sync", array()), "doc_link", array());
        echo "
                </p>
            </div>
        </div> <!-- .wcml-section-content -->

    </div> <!-- .wcml-section -->

    ";
        // line 137
        echo $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "nonce", array());
        echo "
    <p class=\"wpml-margin-top-sm\">
        <input type='submit' name=\"wcml_save_settings\" value='";
        // line 139
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["form"]) ? $context["form"] : null), "save_label", array()), "html", null, true);
        echo "' class='button-primary'/>
    </p>
</form>
<a class=\"alignright\" href=\"";
        // line 142
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["troubleshooting"]) ? $context["troubleshooting"] : null), "url", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["troubleshooting"]) ? $context["troubleshooting"] : null), "label", array()), "html", null, true);
        echo "</a>
";
    }

    public function getTemplateName()
    {
        return "settings-ui.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  301 => 142,  295 => 139,  290 => 137,  280 => 130,  273 => 126,  267 => 125,  263 => 124,  257 => 121,  251 => 120,  247 => 119,  240 => 115,  231 => 109,  225 => 108,  221 => 107,  215 => 104,  209 => 103,  205 => 102,  198 => 98,  189 => 92,  185 => 91,  169 => 78,  163 => 77,  156 => 73,  150 => 72,  138 => 63,  134 => 62,  118 => 49,  112 => 48,  105 => 44,  99 => 43,  87 => 34,  83 => 33,  68 => 21,  62 => 20,  58 => 19,  52 => 16,  46 => 15,  42 => 14,  32 => 7,  28 => 6,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "settings-ui.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/settings-ui.twig");
    }
}
