<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:ubel91/lideres.git');

set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-suggest');

add('shared_dirs', ['var/log', 'var/sessions','uploads']);
add('writable_dirs', ['var', 'var/cache','uploads','public/media/cache']);


// Hosts

host('lideres.cortex.com.ec')
    ->set('remote_user', 'deploy')
    ->set('deploy_path', '/var/www/html/Lideres_demo');
host('corplideres.com')
    ->set('remote_user', 'deploy')
    ->set('deploy_path', '/var/www/html/Lideres_demo');

// Hooks
desc('Database update');
task('database:update', function () {
    cd('{{release_path}}');
    run('php {{bin/console}} doctrine:schema:update --force');
});


desc('Publish assets');
task('assets:install', function () {
    cd('{{release_path}}');
    run('php {{bin/console}} assets:install --symlink public');
});


desc('Compile assets in production');
task('build-js', function () {
    cd('{{release_path}}');
    run('yarn install');
    run('yarn run encore production');
});


desc('Dumping js routes');
task('dump:js-routes', function () {
    cd('{{release_path}}');
    run('php {{bin/console}} fos:js-routing:dump --target=public/bundles/fosjsrouting/js/fos_js_routing.js');
});

desc('chmod');
task('chmod:777', function () {
    run('sudo chmod -R 777 {{deploy_path}}/releases/{{release_name}}/public');
});

task('build', [
    'database:update',
    'assets:install',
    'build-js',
    'dump:js-routes',
]);

after('deploy:vendors', 'build');
after('deploy:failed', 'deploy:unlock');
after('deploy:failed', 'deploy:unlock');
