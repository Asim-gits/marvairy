<?php

/* /setup/ready.twig */
class __TwigTemplate_c453b92fab7a865873e3e4b01813e9ccada6450d37bb82ab65c18e84a29da757 extends Twig_Template
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
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "heading", array()), "html", null, true);
        echo "</h1>

<p>";
        // line 3
        echo sprintf($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description1", array()), "<strong>", "</strong>");
        echo "</p>

<p>";
        // line 5
        echo sprintf($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description2", array()), "<strong>", "</strong>");
        echo "</p>


<p class=\"wcml-setup-actions step\">
    <a href=\"";
        // line 9
        echo twig_escape_filter($this->env, (isset($context["continue_url"]) ? $context["continue_url"] : null), "html", null, true);
        echo "\" class=\"button button-primary button-large\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "continue", array()), "html", null, true);
        echo "</a>
</p>


";
    }

    public function getTemplateName()
    {
        return "/setup/ready.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 9,  30 => 5,  25 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "/setup/ready.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/setup/ready.twig");
    }
}
