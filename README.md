### DOMWorks

DOMWorks is a Document Object Model ([DOM](https://developer.mozilla.org/en-US/docs/DOM)) wrapper to simplify and enhance working with HTML in a similar fashion to [jQuery](http://jquery.com).

### Example

```php
$dx = DOMWorks\DOMWorks::loadHTMLFile('index.html');

// by default, the context is set to document
$body = $dx('body')->id('awesomesauce');

// once you query through a NodeList, the context becomes the list
$p = $body('p')->style('color: #333;');

// the context remains
$body->style('background-color: #ccc;');

// you can use closures
$body->style(function($body)
{
    return $body->style() . 'padding: 20px;';
});

// you can use setters
$body->style .= 'padding-top: 60px;';

// and getters
$bodyStyle = $body->style();
$bodyStyleAlternative = $body->style;
```

### TODO
- Pseudo selectors [(:parent, :first, :last, ...)](http://api.jquery.com/category/selectors/jquery-selector-extensions/)
- Dynamic lists for special, stackable properties like `style` & `class`, to allow: `$dx('div')->style['color'] = 'blue'`, `$dx('div')->class[] = 'extra'` and `unlink($dx('div')->style['color']`.

### License

DOMWorks is open-sourced software licensed under the [MIT license](LICENSE.txt).