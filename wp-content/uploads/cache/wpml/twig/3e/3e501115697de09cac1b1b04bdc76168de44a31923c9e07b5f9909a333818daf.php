<?php

/* trnsl-attributes.twig */
class __TwigTemplate_5c5f792e461d46f3fbe9fa3a3b86752171ac876b8025db14463a10423e7e7a95 extends Twig_Template
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
        if ((isset($context["edit_mode"]) ? $context["edit_mode"] : null)) {
            // line 2
            echo "    <div class=\"wcml-is-translatable-attr-block\" style=\"display: none\">
        <table>
            <tr class=\"form-field\">
                <th scope=\"row\" valign=\"top\">
                    <label for=\"wcml-is-translatable-attr\">";
            // line 6
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "label", array()), "html", null, true);
            echo "</label>
                </th>
                <td>
                    <input name=\"wcml-is-translatable-attr\" id=\"wcml-is-translatable-attr\" type=\"checkbox\" value=\"1\" ";
            // line 9
            if ((isset($context["checked"]) ? $context["checked"] : null)) {
                echo " checked=\"checked\" ";
            }
            echo " />
                    <p class=\"description\">";
            // line 10
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description", array()), "html", null, true);
            echo "</p>
                </td>
            </tr>
        </table>
    </div>
    <input type=\"hidden\" id=\"wcml-is-translatable-attr-notice\" value=\"";
            // line 15
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "notice", array()), "html", null, true);
            echo "\" />
";
        } else {
            // line 17
            echo "    <div class=\"wcml-is-translatable-attr-block\" style=\"display: none\">
        <div class=\"form-field\">
            <label for=\"wcml-is-translatable-attr\">
                <input name=\"wcml-is-translatable-attr\" id=\"wcml-is-translatable-attr\" type=\"checkbox\" value=\"1\" ";
            // line 20
            if ((isset($context["checked"]) ? $context["checked"] : null)) {
                echo " checked=\"checked\" ";
            }
            echo " />
                ";
            // line 21
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "label", array()), "html", null, true);
            echo "
            </label>
            <p class=\"description\">";
            // line 23
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["strings"]) ? $context["strings"] : null), "description", array()), "html", null, true);
            echo "</p>
        </div>
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "trnsl-attributes.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 23,  63 => 21,  57 => 20,  52 => 17,  47 => 15,  39 => 10,  33 => 9,  27 => 6,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "trnsl-attributes.twig", "/var/customers/webs/AprilJune/entwicklung/wallisch/wp-content/plugins/woocommerce-multilingual/templates/trnsl-attributes.twig");
    }
}
