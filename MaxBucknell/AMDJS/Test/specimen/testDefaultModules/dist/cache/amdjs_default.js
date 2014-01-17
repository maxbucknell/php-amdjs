define('foo/bar', ['foo/baz', 'foo/bop'], function (baz, bop) {
    return baz(bop);
});


define('foo/baz', function () {
    return function (bop) {
        return 'bing';
    }
});


define('foo/bop', function () {
    return {
        'ta': 'dah'
    };
});
