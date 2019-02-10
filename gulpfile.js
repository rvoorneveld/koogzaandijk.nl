var gulp = require('gulp');
var sass = require('gulp-sass');
var minifyJavascript = require('gulp-minify');
var minifyCss = require('gulp-minify-css');
var concat = require('gulp-concat');

gulp.task('sass', function() {
    return gulp.src('source/scss/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('source/css/'))
});

gulp.task('css', function() {
    return gulp.src('source/css/*.css')
        .pipe(concat('app.min.css'))
        // .pipe(minifyCss())
        .pipe(gulp.dest('public/assets/default/css'));
});

gulp.task('javascript', function() {
    gulp.src('source/script/async/*.js', '!source/script/async/*.mobile.js')
        .pipe(concat('app.async.js'))
        .pipe(minifyJavascript())
        .pipe(gulp.dest('public/assets/default/script'));

    gulp.src('source/script/async/*.js', '!source/script/async/*.app.js')
        .pipe(concat('app.mobile.async.js'))
        .pipe(minifyJavascript())
        .pipe(gulp.dest('public/assets/default/script'));

    return gulp.src('source/script/sync/*.js')
        .pipe(concat('app.sync.js'))
        .pipe(minifyJavascript())
        .pipe(gulp.dest('public/assets/default/script'));
});

gulp.task('default', gulp.series('sass', 'css', 'javascript'));