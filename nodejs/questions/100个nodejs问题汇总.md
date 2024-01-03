# 100个nodejs问题汇总
## 1、[Browser: Uncaught ReferenceError: require is not defined](https://stackoverflow.com/questions/19059580/browser-uncaught-referenceerror-require-is-not-defined)
`stack overflow 最佳解答`

 This is because require() does not exist in the browser/client-side JavaScript.

Now you're going to have to make some choices about your client-side JavaScript script management.

You have three options:

1. Use the `<script>` tag.
2. Use a [CommonJS](http://wiki.commonjs.org/wiki/CommonJS) implementation. It has synchronous dependencies like Node.js
3. Use an [asynchronous module definition](http://requirejs.org/docs/whyamd.html) (AMD) implementation.

CommonJS client side-implementations include (most of them require a build step before you deploy):

1. [Browserify](https://github.com/substack/node-browserify) - You can use most Node.js modules in the browser. This is my personal favorite.
2. [Webpack](https://webpack.github.io/) - Does everything (bundles JavaScript code, CSS, etc.). It was made popular by the surge of React, but it is notorious for its difficult learning curve.
3. [Rollup](http://rollupjs.org/) - a new contender. It leverages ES6 modules and includes tree-shaking abilities (removes unused code).

You can read more about my comparison of [Browserify vs (deprecated) Component](http://procbits.com/2013/06/17/client-side-javascript-management-browserify-vs-component).

AMD implementations include:

1. [RequireJS](http://requirejs.org/) - Very popular amongst client-side JavaScript developers. It is not my taste because of its asynchronous nature.

Note, in your search for choosing which one to go with, you'll read about Bower. Bower is only for package dependencies and is unopinionated on module definitions like CommonJS and AMD.

## 2、[require is not defined? Node.js [duplicate]](https://stackoverflow.com/questions/31931614/require-is-not-defined-node-js)

Just remove "type":"module" from your package.json.

## 3、["Uncaught SyntaxError: Cannot use import statement outside a module" when importing ECMAScript 6](https://stackoverflow.com/questions/58211880/uncaught-syntaxerror-cannot-use-import-statement-outside-a-module-when-import)

- [ ] 方案一
Add "type": "module" to your package.json file.
- [ ] 方案二
```html
<html>
<header>
    <script type="module" src="whatever.js"></script>
</header>
<body></body>
</html>

```
