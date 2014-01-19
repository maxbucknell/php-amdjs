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


define('mine/yours', function () {
    return 'what\'s mine is yours, and what\'s yours is yours too';
});


require.config({ "deps": ["foo\/bar","mine\/yours"] });
