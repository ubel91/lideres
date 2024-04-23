<?php

namespace Deployer;

require_once 'recipe/symfony4.php';
require_once 'recipe/yarn.php';

set('repository', 'git@github.com:ubel91/lideres.git');

set('application', 'lideres');

host('beta')
    ->hostname('lideres.cortex.com.ec')
    ->set('branch', 'main')
    ->user('deploy')
    ->set('deploy_path', '/var/www/html/Lideres');

host('production')
    ->hostname('corplideres.com')
    ->set('branch', 'main')
    ->user('deploy')
    ->set('deploy_path', '/var/www/html/Lideres_prod');

set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-suggest');

set('release_name', function () {
    return date('YmdHis');
});
set('keep_releases', 2);

set('shared_dirs', ['var/log', 'var/sessions','uploads']);
set('writable_dirs', ['var', 'var/cache','uploads','public/media/cache']);
set('writable_mode', 'chmod');
set('writable_use_sudo', true);
set('writable_chmod_recursive',true);
//set('copy_dirs', ['public/uploads']);


// Set to false to make npm recipe dont't copy node_modules from previous release
set('previous_release', false);

set('ssh_multiplexing',  true);
//set('use_relative_symlinks', false);

desc('Compile assets in production');
task('yarn:run:production', 'yarn run encore production');
//task('yarn:run:dev', 'yarn run encore dev');


desc('Database update');
task('database:update', function () {
    run('php {{bin/console}} doctrine:schema:update --force');
});

desc('Publish assets');
task('assets:install', 'php {{bin/console}} assets:install --symlink public');

desc('Dumping js routes');
task('dump:js-routes', 'php {{bin/console}} fos:js-routing:dump --target=public/bundles/fosjsrouting/js/fos_js_routing.js');

desc('chmod');
task('chmod:777', function () {
    run('sudo chmod -R 777 {{deploy_path}}/releases/{{release_name}}/public');
});

task('build', [
    'database:update',
    'assets:install',
    'dump:js-routes',
    'yarn:install',
    'yarn:run:production',
]);

after('deploy:vendors', 'build');
after('deploy:failed', 'deploy:unlock');
after('cleanup', 'chmod:777');