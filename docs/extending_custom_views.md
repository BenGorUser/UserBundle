# Custom views

The views of this bundle are written using **Twig** template engine. BenGorUser is an *"CSS agnostic"* package so, it
does not have any interest to provide a front-end solution. However, the template architecture is very extensible to
provide an easy way of overriding the default templates. The following is diagram shows the default template hierarchy:

```
views
   |
   |---- change_password
   |       |
   |       |---- by_request_remember_password.html.twig
   |       |---- by_request_remember_password_content.html.twig
   |       |
   |       |---- default.html.twig
   |       |---- default_content.html.twig
   |
   |---- invite
   |       |
   |       |---- invite.html.twig
   |       |---- invite_content.html.twig
   |
   |---- remove
   |        |
   |        |---- remove.html.twig
   |        |---- remove_content.html.twig
   |
   |---- request_remember_password
   |        |
   |        |---- request_remember_password.html.twig
   |        |---- request_remember_password_content.html.twig
   |
   |---- security
   |        |
   |        |---- login.html.twig
   |        |---- login_content.html.twig
   |
   |---- sign_up
   |        |
   |        |---- by_invitation.html.twig
   |        |---- by_invitation_content.html.twig
   |        |
   |        |---- default.html.twig
   |        |---- default_content.html.twig
   |
   |____ layout.html.twig 
```

The above diagram contains a significant feature: the uses cases are separated by folders as `/Application/Command/`
directory does in BenGorUser library; inside use cases has two templates: the first one *extends the base layout and
includes the child content template*, and the second one *contains the content of the view*. 

It is highly recommended that you override the `Resources/views/layout.html.twig` template so that the pages provided by
the BenGorUserBundle have a similar look and feel to the rest of your application. It is very common to extends your
application base layout as follows:
```twig
{# app/Resources/BenGorUserBundle/layout.html.twig #}

{% extends 'base.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    {% block bengor_user_stylesheets %}{% endblock %}
{% endblock %}

{% block content %}
    {% block bengor_user_content %}{% endblock %}
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    {% block bengor_user_javascripts %}{% endblock %}
{% endblock %}
```
The content block is where the main content of each page is rendered. This is why the `bengor_user_content` block has
been placed inside of it. This will lead to the desired effect of having the output from the BenGorUserBundle actions
integrated into our applications layout, preserving the look and feel of the application.

In this way you can easily change the content of, for example, by invitation sign up overriding only the content:
```twig
{# app/Resources/BenGorUserBundle/sign_up/by_invitation_content.html.twig #}

{% trans_default_domain 'BenGorUser' %}

<div class="row form__container">
    {{ form_start(form, {'attr': {'class': 'form'}}) }}

    {% if app.session.flashbag.peekAll|length > 0 %}
        {% for type, messages in app.session.flashbag.all %}
            {% for message in messages %}
                <div class="form__flash form__flash--{{ type ? type : '' }}">
                    {{ message|trans({}, domain|default('messages')) }}
                </div>
            {% endfor %}
        {% endfor %}
    {% endif %}

    <div class="form__group">
        {{ form_widget(form.password.first) }}
        {{ form_label(form.password.first) }}
    </div>
    <div class="form__group">
        {{ form_widget(form.password.second) }}
        {{ form_label(form.password.second) }}
    </div>

    {{ form_widget(form.submit, {
        'attr': {
            'class': 'button form__button'
        }
    }) }}

    {{ form_end(form) }}
</div>
```
> Note that the `/sign_up/by_invitation.html.twig` has not been overridden.

Form more info about **overriding bundle templates** visit this [link][1] of official Symfony docs.

- Back to the [index](index.md).

[1]: http://symfony.com/doc/current/book/templating.html#overriding-bundle-templates
