# RDCriticalCSS
ExpressionEngine3 plugin for inlining critical css files and providing polyfill support for rel=preload

## How to Use

Invoke the {exp:rd_critical_css} tag with a file parameter that points to your critical css file, i.e.

```
{exp:rd_critical_css file='path/to/critical.css'}
```

RDCriticalCSS will inject the contents of your critical css file (removing any sourceMap info) into the `head` so that above-the-fold content will be styled without blocking the rendering of the page.

Immediately following this `link` element, it will inject a `script` tag with compressed loadCSS and cssrelpreload scripts (currently v1.2.0) to load stylesheets asynchronously (see https://github.com/filamentgroup/loadCSS)