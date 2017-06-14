var gulp = require('gulp');
var gulpsync = require('gulp-sync')(gulp);
/* https://github.com/zont/gulp-usemin */
var usemin = require('gulp-usemin');
var uglify = require('gulp-uglify');
var htmlmin = require('gulp-htmlmin');
//var minifyCss = require('gulp-minify-css');
var cssnano = require('gulp-cssnano');
var rev = require('gulp-rev');
var gulpCopy = require('gulp-copy');
var imagemin = require('gulp-imagemin');
//var pngquant = require('imagemin-pngquant');
var ngAnnotate = require('gulp-ng-annotate');
var jsonminify = require('gulp-jsonminify');
var del = require('del');
var Q = require('q');

// https://www.npmjs.com/package/gulp-replace
var replace = require('gulp-replace');

// https://www.npmjs.com/package/gulp-concat
var concat = require('gulp-concat');

// https://www.npmjs.com/package/gulp-rename
var rename = require('gulp-rename');
var merge = require('merge-stream');

var connect = require('gulp-connect');

// https://github.com/shinnn/gulp-gh-pages
var ghPages = require('gulp-gh-pages');


var source = __dirname + '/src/';
//var source = __dirname + '/';
//var output = __dirname + '/../dist/';
var output = __dirname + '/dist/';
var paths = [];


// Dockblock
// https://github.com/zont/gulp-usemin
// Sourcemaps to debug in chrome : https://github.com/floridoo/gulp-sourcemaps mais dabord assayer la génération avec uglify ...
gulp.task('usemin', function() {
    return gulp.src([source + 'index.html'])
    .pipe(usemin({
        //css: [ minifyCss(), rev() ], // 'concat' 
        /* http://cssnano.co/options/ */
        css: [ cssnano({discardComments: {removeAll: true}}), rev() ],
        //css: [ sourcemaps.init(), cssnano(), rev(), sourcemaps.write('.') ],
        /* https://github.com/kangax/html-minifier */
        html: [ htmlmin({
            collapseWhitespace: true,
            removeComments: true
        }) ],
        libraries: [ uglify() , rev() ], // ngAnnotate(),  rev(), NO uglify() NO 
        angular: [ uglify(), rev() ], // uglify(), rev()
    }))
    .pipe( gulp.dest( output ) );
});

/**
 * this task changes path to assets previously minified by usemin
 */
// gulp.task('usemin-path', function(){
//     gulp.src([output + 'index.html'])
//         .pipe(replace('/js/libraries', '/build/js/libraries'))
//         .pipe(replace('/js/angular', '/build/js/angular'))
//         .pipe(replace('/css/killduplicate', '/build/css/killduplicate'))
//         .pipe(gulp.dest(output));
// });


// Fonts
gulp.task('fonts', function() {
    return gulp.src([
        source + '/bower_components/font-awesome/fonts/fontawesome-webfont.*', 
        source + '/bower_components/bootstrap/fonts/glyphicons-halflings-regular.*', 
        source + '/fonts/*.*'
    ])
    .pipe(gulp.dest(output + 'fonts/'));
});
// gulp.task('fonts-internal', function() {
//     return gulp.src([
//         source + '/css/font/*.*'
//     ])
//     .pipe(gulp.dest(output + 'css/font/'));
// });


// JSON compress
// gulp.task('json-site', function() {
//     return gulp.src([source + 'json/**/*.json'])
//         .pipe(jsonminify())
//         .pipe(gulp.dest(output + 'json/'));
// });
// gulp.task('json-i18n', function() {
//     return gulp.src([source + 'js/i18n/**/*.json'])
//         .pipe(jsonminify())
//         .pipe(gulp.dest(output + 'js/i18n/'));
// });
// gulp.task('jsons', gulpsync.sync(['json-site', 'json-i18n']));



// MINIFY HTML TEMPLATES
gulp.task('template-general', function() {
    return gulp.src([source + 'js/App/templates/**/*.html'])
        .pipe(htmlmin({collapseWhitespace: true, removeComments: true})) 
        .pipe(gulp.dest(output + 'js/App/templates/'));
});
gulp.task('template-app', function() {
    return gulp.src([source + 'js/App/Upr/templates/**/*.html'])
        .pipe(htmlmin({collapseWhitespace: true, removeComments: true}))
        .pipe(gulp.dest(output + 'js/App/Upr/templates/'));
});
gulp.task('templates', gulpsync.sync(['template-general', 'template-app']));



// COPY FILES
// https://github.com/klaascuvelier/gulp-copy
gulp.task('copy-files', function() {
  return gulp.src([
        /* FILES */
        source + '.htaccess',
        source + 'bower.json',
        source + 'favicon.ico',
        source + 'humans.txt',
        source + 'robots.txt',
        // source + 'sitemap-index.xml',
        //source + 'CNAME',
        source + '404.html',
    ])
    .pipe(gulp.dest(output));
});

gulp.task('copy-env-prod', function() {
  return gulp.src([source + 'env-prod.js'])
    .pipe(rename('env.js'))
    .pipe(gulp.dest(output));
});


gulp.task('copy-folders', function() {
    var dir1 = gulp.src(source + 'data/**/*').pipe(gulp.dest(output + 'data/'));
    var dir2 = gulp.src(source + 'img/**/*.svg').pipe(gulp.dest(output + 'img/'));
    var dir3 = gulp.src(source + 'sitemaps/**/*').pipe(gulp.dest(output + 'sitemaps/'));
    //return dir2;
    return merge(dir1, dir2, dir3);
});


// IMAGES OPTIMIZATION
gulp.task('img', function () {
    return gulp.src([source + 'img/**/*.{png,jpg,jpeg,gif,ico}'])
        .pipe(imagemin({
            // progressive: true,
            // svgoPlugins: [{removeViewBox: false}],
            // use: [pngquant()]
        }))
        .pipe(gulp.dest(output + 'img'));
});



// removes all compiled dev files
gulp.task('clean', function() {
    return del(output + '**/*', {dot: true, force: true});
});


// GLOBAL PRODUCTION SCRIPT 'clean', 
gulp.task('build', gulpsync.sync([
    'templates', 
//    'jsons', 
    'fonts', 
//    'fonts-internal', 
    'usemin', 
//    'usemin-path', 
    'copy-files', 
    'copy-env-prod',
//    'copy-folders', 
    'img'
]));

// Deploy to github pages
gulp.task('deploy', function() {
  return gulp.src('./dist/**/*')
    .pipe(ghPages());
});

// copier les svg
// /var/www/politiques.test/angular-french-politic-map/src/img
// find . -name '*.svg' | cpio -updm /var/www/politiques.test/angular-french-politic-map/dist/img

// server tasks
// https://github.com/avevlad/gulp-connect
// try
// https://github.com/schickling/gulp-webserver
gulp.task('connect', function() {
  connect.server({
    root: 'dist',
    port: 8000,
    livereload: true,
    //directoryListing: true,
  });
});

gulp.task('html', function () {
  gulp.src('./dist/*.html')
    .pipe(connect.reload());
});

gulp.task('watch', function () {
  gulp.watch(['./dist/*.html'], ['html']);
});

gulp.task('default', ['connect', 'watch']);