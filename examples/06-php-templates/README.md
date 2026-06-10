# PHP Templates

This example demonstrates loading PHP templates with variable injection and logic.

## What This Example Shows

- Creating PHP template files with access to passed variables
- Using loops and conditionals in templates
- Loading PHP templates with `fromFileAsNode()`
- Combining template output with programmatic DOM building

## Files

- [`example.php`](example.php) - Main example code
- [`templates/list.php`](templates/list.php) - Dynamic list template
- [`templates/user-card.php`](templates/user-card.php) - User card with conditional logic

## Running the Example

```bash
php example.php
```

## Expected Output

A dynamic list and user cards rendered from PHP templates.

## Related Examples

- [05-html-templates](../05-html-templates/) - Simpler slot-based templates
- [07-standalone-renderer](../07-standalone-renderer/) - Controlling render output
