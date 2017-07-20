<?php

/* menus-wrap.twig */
class __TwigTemplate_23645d3cecd12ba23051009d139750114a434d9ab59bcd27100f475bb801c7b1 extends Twig_Template
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
        echo "<div class=\"wrap\">
    <h1>";
        // line 2
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "title", array()), "html", null, true);
        echo "</h1>
    <nav class=\"wcml-tabs wpml-tabs\">
        <a class=\"nav-tab ";
        // line 4
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "products", array()), "active", array()), "html", null, true);
        echo "\" href=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "products", array()), "url", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "products", array()), "title", array()), "html", null, true);
        echo "</a>
        ";
        // line 5
        if ((isset($context["can_operate_options"]) ? $context["can_operate_options"] : null)) {
            // line 6
            echo "            ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "taxonomies", array()));
            foreach ($context['_seq'] as $context["key"] => $context["taxonomy"]) {
                // line 7
                echo "            <a class=\"js-tax-tab-";
                echo twig_escape_filter($this->env, $context["key"], "html", null, true);
                echo " nav-tab ";
                echo twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "active", array()), "html", null, true);
                echo "\" href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "url", array()), "html", null, true);
                echo "\" title=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "title", array()), "html", null, true);
                echo "\">
            ";
                // line 8
                echo twig_escape_filter($this->env, $this->getAttribute($context["taxonomy"], "name", array()), "html", null, true);
                echo "
            ";
                // line 9
                if (($this->getAttribute($context["taxonomy"], "translated", array()) == false)) {
                    echo "<i class=\"otgs-ico-warning\"></i>";
                }
                // line 10
                echo "            </a>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['taxonomy'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 12
            echo "            ";
            if ($this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "custom_taxonomies", array()), "show", array())) {
                // line 13
                echo "            <a class=\"nav-tab tax-custom-taxonomies ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "custom_taxonomies", array()), "active", array()), "html", null, true);
                echo "\" href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "custom_taxonomies", array()), "url", array()), "html", null, true);
                echo "\">
                ";
                // line 14
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "custom_taxonomies", array()), "name", array()), "html", null, true);
                echo "
                ";
                // line 15
                if (($this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "custom_taxonomies", array()), "translated", array()) == false)) {
                    echo "<i class=\"otgs-ico-warning\"></i>";
                }
                // line 16
                echo "            </a>
            ";
            }
            // line 18
            echo "            <a class=\"nav-tab tax-product-attributes ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "attributes", array()), "active", array()), "html", null, true);
            echo "\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "attributes", array()), "url", array()), "html", null, true);
            echo "\">
                ";
            // line 19
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "attributes", array()), "name", array()), "html", null, true);
            echo "
                ";
            // line 20
            if (($this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "attributes", array()), "translated", array()) == false)) {
                echo "<i class=\"otgs-ico-warning\"></i>";
            }
            // line 21
            echo "            </a>

            <a class=\"js-tax-tab-product_shipping_class nav-tab ";
            // line 23
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "shipping_classes", array()), "active", array()), "html", null, true);
            echo "\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "shipping_classes", array()), "url", array()), "html", null, true);
            echo "\"
               title=\"";
            // line 24
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "shipping_classes", array()), "title", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "shipping_classes", array()), "name", array()), "html", null, true);
            echo "
               ";
            // line 25
            if (($this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "shipping_classes", array()), "translated", array()) == false)) {
                echo "<i class=\"otgs-ico-warning\"></i>";
            }
            // line 26
            echo "            </a>
        ";
        }
        // line 28
        echo "
        ";
        // line 29
        if ((isset($context["can_manage_options"]) ? $context["can_manage_options"] : null)) {
            // line 30
            echo "            <a class=\"nav-tab ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "settings", array()), "active", array()), "html", null, true);
            echo "\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "settings", array()), "url", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "settings", array()), "name", array()), "html", null, true);
            echo "</a>
        ";
        }
        // line 32
        echo "        ";
        if ((isset($context["can_operate_options"]) ? $context["can_operate_options"] : null)) {
            // line 33
            echo "            <a class=\"nav-tab ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "multi_currency", array()), "active", array()), "html", null, true);
            echo "\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "multi_currency", array()), "url", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "multi_currency", array()), "name", array()), "html", null, true);
            echo "</a>
            <a class=\"nav-tab ";
            // line 34
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "slugs", array()), "active", array()), "html", null, true);
            echo "\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "slugs", array()), "url", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "slugs", array()), "name", array()), "html", null, true);
            echo "</a>
        ";
        }
        // line 36
        echo "        ";
        if ((isset($context["can_manage_options"]) ? $context["can_manage_options"] : null)) {
            // line 37
            echo "            <a class=\"nav-tab ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "status", array()), "active", array()), "html", null, true);
            echo "\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "status", array()), "url", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "status", array()), "name", array()), "html", null, true);
            echo "</a>
            ";
            // line 38
            if ($this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "troubleshooting", array()), "active", array())) {
                // line 39
                echo "                <a class=\"nav-tab troubleshooting ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "troubleshooting", array()), "active", array()), "html", null, true);
                echo "\" href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "troubleshooting", array()), "url", array()), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["menu"]) ? $context["menu"] : null), "troubleshooting", array()), "name", array()), "html", null, true);
                echo "</a>
            ";
            }
            // line 41
            echo "        ";
        }
        // line 42
        echo "    </nav>

    <div class=\"wcml-wrap\">
    ";
        // line 45
        echo (isset($context["content"]) ? $context["content"] : null);
        echo "
    </div>

    <div class=\"wcml-wrap wcml-notice otgs-is-dismissible\">
        <p>";
        // line 49
        echo $this->getAttribute((isset($context["rate"]) ? $context["rate"] : null), "message", array());
        echo "</p>
        <button class=\"notice-dismiss hide-rate-block\" data-setting=\"rate-block\">
                <span class=\"screen-reader-text\">";
        // line 51
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["rate"]) ? $context["rate"] : null), "hide_text", array()), "html", null, true);
        echo "</span>
        </button>
        ";
        // line 53
        echo $this->getAttribute((isset($context["rate"]) ? $context["rate"] : null), "nonce", array());
        echo "
    </div>

</div>";
    }

    public function getTemplateName()
    {
        return "menus-wrap.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  214 => 53,  209 => 51,  204 => 49,  197 => 45,  192 => 42,  189 => 41,  179 => 39,  177 => 38,  168 => 37,  165 => 36,  156 => 34,  147 => 33,  144 => 32,  134 => 30,  132 => 29,  129 => 28,  125 => 26,  121 => 25,  115 => 24,  109 => 23,  105 => 21,  101 => 20,  97 => 19,  90 => 18,  86 => 16,  82 => 15,  78 => 14,  71 => 13,  68 => 12,  61 => 10,  57 => 9,  53 => 8,  42 => 7,  37 => 6,  35 => 5,  27 => 4,  22 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "menus-wrap.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/menus-wrap.twig");
    }
}
