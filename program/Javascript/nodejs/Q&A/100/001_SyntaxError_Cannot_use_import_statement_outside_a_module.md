# SyntaxError: Cannot use import statement outside a module
[stackoverflow](https://stackoverflow.com/questions/58384179/syntaxerror-cannot-use-import-statement-outside-a-module)
需要认真学习

Verify that you have the latest version of Node.js installed (or, at least 13.2.0+). Then do one of the following, as described in the documentation:

## Option 1

In the nearest parent package.json file, add the top-level "type" field with a value of "module". This will ensure that all .js and .mjs files are interpreted as ES modules. You can interpret individual files as CommonJS by using the .cjs extension.
```javascript
// package.json
{
 "type": "module"
}
```

## Option 2

Explicitly name files with the .mjs extension. All other files, such as .js will be interpreted as CommonJS, which is the default if type is not defined in package.json.