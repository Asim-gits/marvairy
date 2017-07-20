<?php

/* products.twig */
class __TwigTemplate_eb94febff57d026542fc92d4a8c784999324d2a0ce087037bc29c6414390fedd extends Twig_Template
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
        echo "<div class=\"wcml-section\">
    <div class=\"wcml-section-header\">
        <h3>
            ";
        // line 4
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "products_missing", array()), "html", null, true);
        echo "
        </h3>
    </div>
    <div class=\"wcml-section-content\">
        <ul class=\"wcml-status-list wcml-plugins-status-list\">
            ";
        // line 9
        if (twig_test_empty((isset($context["products"]) ? $context["products"] : null))) {
            // line 10
            echo "                <li>
                    <i class=\"otgs-ico-ok\"></i>
                    ";
            // line 12
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "not_to_trnsl", array()), "html", null, true);
            echo "
                </li>
            ";
        } else {
            // line 15
            echo "                ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["products"]) ? $context["products"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 16
                echo "                    <li>
                        <i class=\"otgs-ico-warning\"></i>
                        <span class=\"wpml-title-flag\">
                            ";
                // line 19
                echo $this->getAttribute($context["product"], "flag", array());
                echo "
                        </span>
                        ";
                // line 21
                if (($this->getAttribute($context["product"], "count", array()) == 1)) {
                    // line 22
                    echo "                            ";
                    echo twig_escape_filter($this->env, sprintf($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "miss_trnsl_one", array()), $this->getAttribute($context["product"], "count", array()), $this->getAttribute($context["product"], "display_name", array())), "html", null, true);
                    echo "
                        ";
                } else {
                    // line 24
                    echo "                            ";
                    echo twig_escape_filter($this->env, sprintf($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "miss_trnsl_more", array()), $this->getAttribute($context["product"], "count", array()), $this->getAttribute($context["product"], "display_name", array())), "html", null, true);
                    echo "
                        ";
                }
                // line 26
                echo "                    </li>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 28
            echo "
                <p>
                    <a class=\"button-secondary aligncenter\" href=\"";
            // line 30
            echo twig_escape_filter($this->env, (isset($context["trnsl_link"]) ? $context["trnsl_link"] : null), "html", null, true);
            echo "\">
                        ";
            // line 31
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "transl", array()), "html", null, true);
            echo "
                    </a>
                </p>
            ";
        }
        // line 35
        echo "        </ul>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "products.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  95 => 35,  88 => 31,  84 => 30,  80 => 28,  73 => 26,  67 => 24,  61 => 22,  59 => 21,  54 => 19,  49 => 16,  44 => 15,  38 => 12,  34 => 10,  32 => 9,  24 => 4,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "products.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/status/products.twig");
    }
}
