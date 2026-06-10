# HTML Templates

This example demonstrates loading HTML templates with slot-based variable substitution.

## What This Example Shows

- Creating HTML template files with `{{slot}}` syntax
- Loading templates with `fromFileAsNode()`
- Passing variables to fill slots
- Reusing templates with different data

## Files

- [`example.php`](example.php) - Main example code
- [`templates/card.html`](templates/card.html) - Card component template
- [`templates/nav.html`](templates/nav.html) - Navigation template

## Running the Example

```bash
php example.php
```

## Expected Output

HTML cards and navigation rendered from templates with injected content.

## Related Examples

- [06-php-templates](../06-php-templates/) - PHP-based templates with logic
- [01-basic-document](../01-basic-document/) - Building documents programmatically
