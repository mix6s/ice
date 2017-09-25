"use strict";

var gulp = require('gulp'),
	sass = require('gulp-sass'),
	postcss = require('gulp-postcss'),
	autoprefixer = require('autoprefixer'),
	cssnano = require('cssnano'),
	concat = require('gulp-concat'),
	server = require("browser-sync"),
	uglify = require('gulp-uglify'),
	sourcemaps = require('gulp-sourcemaps'),
	imagemin = require('gulp-imagemin'),
	pngquant = require('imagemin-pngquant'),
	del = require('del'),
	rename = require('gulp-rename'),
	spritesmith = require('gulp.spritesmith'),
	merge = require('merge-stream'),
	twig = require('gulp-twig'),
	runSequence = require('run-sequence').use(gulp);

gulp.task('styles', function () {
	var processors = [
		autoprefixer({
			browsers: ['last 2 versions']
		}),
		cssnano
	];
	return gulp.src('app/Resources/assets/scss/style.scss')
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(postcss(processors))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('web/assets/css'))
		.pipe(server.reload({stream: true}));
});

gulp.task('rename', function () {

	gulp.src("./web/bower_components/swiper/dist/css/swiper.css")
		.pipe(rename("web/bower_components/swiper/dist/css/swiper.scss"))
		.pipe(gulp.dest("./"));

	gulp.src("./web/bower_components/fancybox/dist/jquery.fancybox.css")
		.pipe(rename("web/bower_components/fancybox/dist/jquery.fancybox.scss"))
		.pipe(gulp.dest("./"));

	gulp.src("./web/bower_components/croppie/croppie.css")
		.pipe(rename("web/bower_components/croppie/croppie.scss"))
		.pipe(gulp.dest("./"));
});

gulp.task('images', function () {
	return gulp.src('app/Resources/assets/img/**/*')
		.pipe(imagemin({
			progressive: true,
			svgoPlugins: [{removeViewBox: false}],
			use: [pngquant()]
		}))
		.pipe(gulp.dest('web/assets/img'))
		.pipe(server.reload({stream: true}));
});

gulp.task('sprite', function () {
	var spriteData = gulp.src('app/Resources/assets/img/icons/*.png').pipe(spritesmith({
		imgName: '../img/sprite.png',
		cssName: 'sprite.scss',
		algorithm: 'top-down'
	}));
	var imgStream = spriteData.img
		.pipe(gulp.dest('web/assets/img'));

	var cssStream = spriteData.css
		.pipe(gulp.dest('app/Resources/assets/scss/'));

	return merge(imgStream, cssStream);
});

gulp.task('scripts', function () {
	return gulp.src([
		'bower_components/jquery/dist/jquery.js',
		'bower_components/modernizr1/modernizr.js',
		'bower_components/swiper/dist/js/swiper.js',
		'bower_components/fancybox/dist/jquery.fancybox.js',
		'bower_components/croppie/croppie.js',
		'app/Resources/assets/js/*.js'
	])
		.pipe(concat('scripts.js'))
		.pipe(gulp.dest('web/assets/js'))
		.pipe(rename({suffix: '.min'}))
		.pipe(uglify())
		.pipe(gulp.dest('web/assets/js'))
		.pipe(server.reload({stream: true}));
});

gulp.task('clean', function () {
	del('web/assets/*');
});

gulp.task('build', function(callback) {
	runSequence('clean','styles','scripts','images', 'sprite', callback)
});

gulp.task("watch", ['styles','scripts','images', 'sprite'], function() {
	gulp.watch("app/Resources/assets/**/*.{scss,sass}", ["styles"]);
	gulp.watch("app/Resources/assets/**/*.js", ["scripts"]);
	gulp.watch("app/Resources/assets/**/*.+(jpg,png,svg)", ["images"]);
});

gulp.task("serve", ['styles','scripts','images', 'sprite'], function() {
	server.init({
		server: "build",
		notify: false,
		open: true,
		ui: false
	});

	gulp.watch("app/Resources/assets/**/*.{scss,sass}", ["styles"]);
	gulp.watch("app/Resources/assets/**/*.js", ["scripts"]);
	gulp.watch("app/Resources/assets/**/*.+(jpg,png,svg)", ["images"]);
});

gulp.task('default', ['build']);