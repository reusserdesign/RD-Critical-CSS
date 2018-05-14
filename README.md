# RD Critical CSS

![supports ExpressionEngine 3](https://img.shields.io/badge/ExpressionEngine-3-3784B0.svg) ![supports ExpressionEngine 4](https://img.shields.io/badge/ExpressionEngine-4-3784B0.svg)

ExpressionEngine 3 and 4 plugin for inlining critical css files and providing polyfill support for rel=preload

## How to Use

Invoke the `{exp:rd_critical_css}` tag with a `critical` parameter that points to your critical css file, and a `styles` parameter that points to your preloaded stylesheets (separate multiple with a pipe '|' character) i.e.

```html
{exp:rd_critical_css critical='/path/to/critical.css' styles='/path/to/stylesheet1.css|/path/to/stylesheet2.css'}
```

Optionally add an `external_fonts` parameter to additionally preload Google Fonts, TypeKit, etc i.e.

```html
{exp:rd_critical_css external_fonts='https://fonts.googleapis.com/css?family=Open+Sans' critical='/path/to/critical.css' styles='/path/to/stylesheet1.css|/path/to/stylesheet2.css'}
```

If the stylesheets have not already been loaded, RD Critical CSS will inject the contents of your critical css file (removing any sourceMap info) and external font files into the `<head>` so that above-the-fold content will be styled without blocking the rendering of the page.

Immediately following this `<style>` element, it will inject a `<link>` element for each file specified in the 'external_fonts' and 'styles' parameters using `rel=preload`. Following these, it embeds a `<script>` tag with compressed loadCSS and cssrelpreload scripts (currently v1.3.1) to load stylesheets asynchronously (see [https://github.com/filamentgroup/loadCSS](https://github.com/filamentgroup/loadCSS)).

If the stylesheets have already been loaded and cached, it will instead inject normal `<link>` elements using the normal `rel=stylesheet` parameter/value pair.