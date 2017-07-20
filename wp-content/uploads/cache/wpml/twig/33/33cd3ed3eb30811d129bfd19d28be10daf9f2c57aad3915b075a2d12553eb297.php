<?php

/* attribute-translation.twig */
class __TwigTemplate_6e0f55b33735d3c7e0c1a576e9a5718baddeb377709503c87855a5a6f96cb851 extends Twig_Template
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
        if (twig_test_empty((isset($context["attributes"]) ? $context["attributes"] : null))) {
            // line 2
            echo "
    <p>";
            // line 3
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "no_attributes", array()), "html", null, true);
            echo "</p>

";
        } else {
            // line 6
            echo "
\t<div class=\"wpml-loading-taxonomy\"><span class=\"spinner is-active\"></span>";
            // line 7
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "loading", array()), "html", null, true);
            echo "</div>
\t<div class=\"wpml_taxonomy_loaded wcml_attributes_wrap\">
\t\t<h3 class=\"wcml-product-attributes-selector\">
\t\t\t<label>";
            // line 10
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "select_label", array()), "html", null, true);
            echo "</label>
\t\t\t<select id=\"wcml_product_attributes\">
\t\t\t\t";
            // line 12
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["attributes"]) ? $context["attributes"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["attribute"]) {
                // line 13
                echo "\t\t\t\t\t<option
\t\t\t\t\t\t\tvalue=\"pa_";
                // line 14
                echo twig_escape_filter($this->env, $this->getAttribute($context["attribute"], "attribute_name", array()), "html", null, true);
                echo "\"
\t\t\t\t\t\t\t";
                // line 15
                if (($this->getAttribute($context["attribute"], "attribute_name", array()) == $this->getAttribute((isset($context["selected_attribute"]) ? $context["selected_attribute"] : null), "attribute_name", array()))) {
                    echo "selected=\"selected\"";
                }
                // line 16
                echo "\t\t\t\t\t\t\t";
                if (($this->getAttribute($context["attribute"], "attribute_name", array()) == "")) {
                    echo "disabled=\"disabled\"";
                }
                // line 17
                echo "\t\t\t\t\t\t\t>
\t\t\t\t\t\t";
                // line 18
                echo twig_escape_filter($this->env, $this->getAttribute($context["attribute"], "attribute_label", array()), "html", null, true);
                echo "
\t\t\t\t\t</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['attribute'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 21
            echo "\t\t\t</select>
\t\t</h3>
\t\t";
            // line 23
            echo (isset($context["translation_ui"]) ? $context["translation_ui"] : null);
            echo "
\t</div>

";
        }
    }

    public function getTemplateName()
    {
        return "attribute-translation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  80 => 23,  76 => 21,  67 => 18,  64 => 17,  59 => 16,  55 => 15,  51 => 14,  48 => 13,  44 => 12,  39 => 10,  33 => 7,  30 => 6,  24 => 3,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "attribute-translation.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/attribute-translation.twig");
    }
}
