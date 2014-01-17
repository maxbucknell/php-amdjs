define('foo/ding', ['foo/dang', 'foo/dong'], function (dang, dong) {
    return ['ding', dang, dong];
});
