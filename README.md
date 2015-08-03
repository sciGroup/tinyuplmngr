Minimalistic file manager for content editing
=============================================

Don't install it. The bundle is under development!

## Current state
Only Doctrine2 is supported.

## Installation

### Configuration

After installing the bundle, make sure you add this route to your routing:

``` yaml
# app/config/routing.yml

_sci_group_tinymce_plupload_file_manager:
    resource: '@SciGroupTinymcePluploadFileManagerBundle/Resources/config/routing.xml'
```

``` twig
{# Template file with form #}
{% import "SciGroupTinymcePluploadFileManagerBundle::_macro.html.twig" as tpfm_macro %}

{% block head_javascripts %}
    {{ parent() }}
    {{ tpfm_macro.plupload(app.environment, app.session.getId(), eventPage.eventPageIdMappingType, max_uploaded_filesize_mb) }}
{% endblock %}
```