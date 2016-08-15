var gulp = require('gulp');
//var gutil = require('gulp-util');
//var bower = require('bower');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var minifyCss = require('gulp-minify-css');
var rename = require('gulp-rename');
//var sh = require('shelljs');
//var browserify = require("browserify");
//var source = require('vinyl-source-stream');
var rigger = require('gulp-rigger');

var paths = {
  sass: ['./scss/**.scss']
};

gulp.task('default', ['sass', 'watch', 'rigger']);

gulp.task('rigger', function () {
     gulp.src('./pages/index.html')
        .pipe(rigger())
        .pipe(gulp.dest('./'));
});

gulp.task('sass', function(done) {
  gulp.src(['./scss/stylesheet.scss'])
    .pipe(sass())
    .on('error', sass.logError)
    .pipe(gulp.dest('./css/'))
    .pipe(minifyCss({
      keepSpecialComments: 0
    }))
    .pipe(rename({ extname: '.min.css' }))
    .pipe(gulp.dest('./css/'))
    .on('end', done);
});

gulp.task('watch', function() {
  gulp.watch(paths.sass, ['sass']);
  gulp.watch('pages/**.html', ['rigger']);
});

