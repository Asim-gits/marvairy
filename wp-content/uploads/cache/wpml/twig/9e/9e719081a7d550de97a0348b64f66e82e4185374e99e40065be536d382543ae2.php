<?php

/* /setup/attributes.twig */
class __TwigTemplate_c55da4ecce5f2b8419f2350fefbaf7efe99a2c8b7bedff9f7e194031c6731e68 extends Twig_Template
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
        echo "<h1>";
        echo $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "heading", array());
        echo "</h1>

";
        // line 3
        if ((isset($context["attributes"]) ? $context["attributes"] : null)) {
            // line 4
            echo "    <ul class=\"no-bullets\" >
    ";
            // line 5
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["attributes"]) ? $context["attributes"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["attribute"]) {
                // line 6
                echo "    <li>
        <label>
            <input type=\"hidden\" name=\"attributes[";
                // line 8
                echo twig_escape_filter($this->env, $this->getAttribute($context["attribute"], "name", array()), "html", null, true);
                echo "]\" value=\"0\" />
            <input type=\"checkbox\" name=\"attributes[";
                // line 9
                echo twig_escape_filter($this->env, $this->getAttribute($context["attribute"], "name", array()), "html", null, true);
                echo "]\" value=\"1\" ";
                if ($this->getAttribute($context["attribute"], "translated", array())) {
                    echo "checked=\"cehcked\"";
                }
                echo " />
            ";
                // line 10
                echo twig_escape_filter($this->env, $this->getAttribute($context["attribute"], "label", array()), "html", null, true);
                echo "
        </label>
    </li>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['attribute'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 14
            echo "    </ul>
";
        } else {
            // line 16
            echo "    <p><i>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "no_attributes", array()), "html", null, true);
            echo "</i></p>
";
        }
        // line 18
        echo "
<p class=\"wcml-setup-actions step\">
    <a href=\"";
        // line 20
        echo twig_escape_filter($this->env, (isset($context["continue_url"]) ? $context["continue_url"] : null), "html", null, true);
        echo "\" class=\"button button-large button-primary submit\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "continue", array()), "html", null, true);
        echo "</a>
</p>
";
    }

    public function getTemplateName()
    {
        return "/setup/attributes.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  74 => 20,  70 => 18,  64 => 16,  60 => 14,  50 => 10,  42 => 9,  38 => 8,  34 => 6,  30 => 5,  27 => 4,  25 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "/setup/attributes.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/setup/attributes.twig");
    }
}
