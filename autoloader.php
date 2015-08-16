<?php
/**
 * An example of a project-specific implementation.
 *
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
 * from /path/to/project/Foo/Bar/Baz/Qux.php:
 *
 *      new \Foo\Bar\Baz\Qux;
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
$base_dir = __DIR__ . DIRECTORY_SEPARATOR;

spl_autoload_register(function ($class) use ($base_dir) {
	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

	// if the file exists, require it
	if (file_exists($file)) {
		require $file;
	}
});
