define('foo/ding', ['foo/dang', 'foo/dong'], function (dang, dong) {
    return ['ding', dang, dong];
});


define('foo/dang', function () {
    return 'dang';
});


define('foo/dong', function () {
    return 'dong';
});


define('mine/yours', function () {
    return 'what\'s mine is yours, and what\'s yours is yours too';
});


require.config({ "deps": ["foo\/ding","mine\/yours","foo\/baz"] });
