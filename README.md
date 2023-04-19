# Mai URL Parameter Content
This plugin contains 2 main features:
1. A custom block to display or hide dynamic content based on URL parameters. Requires ACF Pro v6+. The block supports multiple URL parameters, optionally requiring specific values (case-insensitive).
2. A programmatic way to add URL parameters (mostly for UTM campaigns) to all URLs inside a specific block or blocks.

## Mai URL Parameter Content block
1. All parameters set in the block need to exist on a specific page (case-insensitive) for the inner block content to display.
1. If a block parameter has a value, it must match (case-insensitive) on the page.
1. If a block parameter has an empty value, it only needs to exist on the page. The value doesn't matter.
1. You can use `{param_name}` in the block content to display a parameter value in your content.

## Mai URL Parameter Adder class
In the below example, `tricky-footer` is a custom class added to the Advanced settings "Additional CSS Class(es)" field on most blocks. Any block with that class will have key => value pairs added as URL parameters to all `<a href>` tags within that element's children.

```
/**
 * Adds URL parameters to blocks with custom classes.
 *
 * @return void
 */
add_action( 'wp_head', function() {
	// Bail if Mai URL Parameter Adder is not available.
	if ( ! class_exists( 'Mai_URL_Parameter_Adder' ) ) {
		return;
	}

	// Build assocative array of 'custom-class' => [ 'param1 => 'value1, 'param2 => 'value2' ].
	$links = [
		'some-custom-class' => [
			'utm_source'   => 'website',   // e.g. newsletter, twitter, google, etc.
			'utm_medium'   => 'cta',       // e.g. email, social, cpc, etc.
			'utm_campaign' => 'affiliate', // e.g. promotion, sale, etc.
			'utm_content'  => '',          // (optional) Any call-to-action or headline, e.g. buy-now.
			'utm_term'     => '',          // (optional) Keywords for your paid search campaigns.
		],
	];

	$class = new Mai_URL_Parameter_Adder( $links );
	$class->run();
});
```
