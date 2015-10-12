'use strict';

var gulp = require('gulp');
var sourcemaps = require('gulp-sourcemaps');
var gutil = require('gulp-util');
var babel = require("gulp-babel");
var concat = require("gulp-concat");
var watch = require('gulp-watch');
var uglify = require('gulp-uglify');
var nodemon = require('gulp-nodemon');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');

gulp.task('buildApp', function () {
    // var app = browserify();
    // app.add('./view/js/game.js', {
    //   debug: true
    // });
    // app.bundle()
    //   .on('error', function (err) {
    //     gutil.log(gutil.colors.bgRed("Browserify error (App)"), gutil.colors.bgBlue(err.message));
    //     // notifier.notify({title: "Browserify error (App)", message: err.message });
    //     this.emit("end");
    //   })
    //   .pipe(source('app.js'))
    //   .pipe(buffer())
    //   .pipe(sourcemaps.init({loadMaps: true}))
    //   .pipe(uglify())
    //   .pipe(sourcemaps.write('./'))
    //   .pipe(gulp.dest('./build'));

    return gulp.src("./view/js/**/*.js")
      .pipe(sourcemaps.init())
      .pipe(buffer())
      .pipe(uglify())
      .pipe(babel())
      .pipe(concat("all.js"))
      .pipe(sourcemaps.write("."))
      .pipe(gulp.dest("./view/dist/"));
});

gulp.task('watch', function() {
  gulp.watch(['./view/js/**/*.js'], ['buildApp']);
});

gulp.task('startServer', function () {
    nodemon({
      script: 'server/server.js',
      ext: 'js',
      ignore: ['node_modules/**', 'build/**', 'src/**', 'gulpfile.js']
    })
    .on('start', ['watch'])
    .on('change', ['watch'])
    .on('restart', function () {
      console.log('restarted!'); // TODO: fix and restart nodemon
    });
});

gulp.task('default', ['watch', 'startServer']);
