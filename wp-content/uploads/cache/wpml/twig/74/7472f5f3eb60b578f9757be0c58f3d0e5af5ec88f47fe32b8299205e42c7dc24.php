<?php

/* /setup/introduction.twig */
class __TwigTemplate_872b2155b0e39d52e21f43039a1ed14cdaefbef7387f1c87efe721b2ee60d330 extends Twig_Template
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
        echo "

<h1>";
        // line 3
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "heading", array()), "html", null, true);
        echo "</h1>

<p>";
        // line 5
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description1", array()), "html", null, true);
        echo "</p>
<div>";
        // line 6
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description2", array()), "title", array()), "html", null, true);
        echo "</div>
<ul>
    <li>";
        // line 8
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description2", array()), "step1", array()), "html", null, true);
        echo "</li>
    <li>";
        // line 9
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description2", array()), "step2", array()), "html", null, true);
        echo "</li>
    <li>";
        // line 10
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description2", array()), "step3", array()), "html", null, true);
        echo "</li>
</ul>
<p>";
        // line 12
        echo $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description3", array());
        echo "</p>

<p class=\"wcml-setup-actions step\">
    <a href=\"";
        // line 15
        echo twig_escape_filter($this->env, (isset($context["later_url"]) ? $context["later_url"] : null), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "later", array()), "html", null, true);
        echo "</a>
    <a href=\"";
        // line 16
        echo twig_escape_filter($this->env, (isset($context["continue_url"]) ? $context["continue_url"] : null), "html", null, true);
        echo "\" class=\"button button-primary button-large\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "continue", array()), "html", null, true);
        echo "</a>
</p>

";
    }

    public function getTemplateName()
    {
        return "/setup/introduction.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 16,  56 => 15,  50 => 12,  45 => 10,  41 => 9,  37 => 8,  32 => 6,  28 => 5,  23 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "/setup/introduction.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/setup/introduction.twig");
    }
}
