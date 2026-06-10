# Standalone Renderer

This example demonstrates using `HtmlRenderer` for per-instance rendering configuration without shared global state.

## What This Example Shows

- Creating renderer instances with different configurations
- Rendering the same node with different settings simultaneously
- Formatted vs compact output
- Quoted vs unquoted attributes
- Safe usage in concurrent/async contexts

## Files

- [`example.php`](example.php) - Main example code

## Running the Example

```bash
php example.php
```

## Expected Output

The same HTML node rendered with different formatting and quoting options, demonstrating that each renderer operates independently.

## Related Examples

- [01-basic-document](../01-basic-document/) - Document creation
- [08-xml-output](../08-xml-output/) - XML rendering
