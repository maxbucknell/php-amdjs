# AmdJs

## What?

AmdJs is a framework agnostic compiler for javascript modules written according to the [AMD specification][AMDspec]. In many ways, it works similarly to [require.js'][require.js] [r.js optimiser][r.js], except that it allows more flexibility with which modules are loaded onto the page at one time.

## Why?

I originally wrote a tool to do this in Magento, but it was unclear what I did when I wanted a model that didn't talk to the database. I was also concerned with the notion of siloing my work into one framework, especially with Magento 2 around the corner in just another friedman unit or two.

So this is the meat of that library, but it doesn't care how it gets its input. Simply implement an interface in your framework of choice, and kaboom, you're home.

## What does it actually do?

Glad you asked. In each case, an array of module names is supplied, and an array of urls is returned. Note that the array may be of length one.

It will take multiple modules and optionally compile them into one file. It will also run each module through any number of specified transformers, that take input and return the new content. This allows for minification. I didn't build minification in, because doing it right requires another runtime and I didn't want to start mandating that. After this, it will optionally cache the urls.

There is the option to split modules into default modules, that will be loaded on every page request, and page specific modules, that might not. This can help out with front-end caching, and might be something to look into.

The compiler supports module aliases, as well, in exactly the same way that require.js does.

Once the urls are returned, put them in a template and include them as javascript files. Add a script loader such as require.js, or its little cousin [almond][almond].

[AMDspec]: https://github.com/amdjs/amdjs-api/wiki/AMD
[require.js]: https://github.com/amdjs/amdjs-api/wiki/AMD
[r.js]: http://requirejs.org/docs/optimization.html
[almond]: https://github.com/jrburke/almond
