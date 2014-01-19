define('foo/bar', ['foo/baz', 'foo/bop'], function (baz, bop) {
    return baz(bop);
});
