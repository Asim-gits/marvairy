<?php

/* languages-notice.twig */
class __TwigTemplate_062c07bef51add47e3a4f927943b4fe68ae297edaa4ef488baa020686c479d61 extends Twig_Template
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
        echo "<div id=\"wcml_translations_message\" class=\"message error\">
    <p>";
        // line 2
        echo $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "trnsl_available", array());
        echo "</p>

    <p>
        ";
        // line 5
        if ((isset($context["is_multisite"]) ? $context["is_multisite"] : null)) {
            // line 6
            echo "            <a href=\"";
            echo $this->getAttribute((isset($context["nonces"]) ? $context["nonces"] : null), "debug_action", array());
            echo "\" class=\"button-primary\">
                ";
            // line 7
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "update_trnsl", array()), "html", null, true);
            echo "
            </a>
        ";
        } else {
            // line 10
            echo "            <a href=\"";
            echo $this->getAttribute((isset($context["nonces"]) ? $context["nonces"] : null), "upgrade_translations", array());
            echo "\" class=\"button-primary\">
                ";
            // line 11
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "update_trnsl", array()), "html", null, true);
            echo "
            </a>
        ";
        }
        // line 14
        echo "        <a href=\"\" class=\"button\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "hide", array()), "html", null, true);
        echo "</a>
        <input type=\"hidden\" id=\"wcml_hide_languages_notice\" value=\"";
        // line 15
        echo $this->getAttribute((isset($context["nonces"]) ? $context["nonces"] : null), "hide_notice", array());
        echo "\" />
    </p>
</div>";
    }

    public function getTemplateName()
    {
        return "languages-notice.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 15,  52 => 14,  46 => 11,  41 => 10,  35 => 7,  30 => 6,  28 => 5,  22 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "languages-notice.twig", "D:\\wamp64\\www\\marvairy\\wp-content\\plugins\\woocommerce-multilingual\\templates\\languages-notice.twig");
    }
}
