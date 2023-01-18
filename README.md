# Mai URL Parameter Content
A custom block to display dynamic content based on URL parameters. Requires ACF Pro v6+. The block supports multiple URL parameters, optionally requiring specific values (case-insensitive).

1. All parameters set in the block need to exist on a specific page (case-insensitive) for the inner block content to display.
1. If a block parameter has a value, it must match (case-insensitive) on the page.
1. If a block parameter has an empty value, it only needs to exist on the page. The value doesn't matter.
1. You can use `{param_name}` in the block content to display a parameter value in your content.
