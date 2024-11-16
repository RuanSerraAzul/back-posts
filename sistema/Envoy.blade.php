@servers(['web' => $server])

@setup
    $server = sprintf(
        '%s@%s',
        getenv('DEPLOY_USER') ?: '',
        getenv('DEPLOY_HOST') ?: ''
    );
    $repository = getenv('DEPLOY_REPOSITORY') ?: '';
    $releases_dir = getenv('DEPLOY_RELEASES_DIR') ?: '/back-posts/sistema/releases';
    $app_dir = getenv('DEPLOY_APP_DIR') ?: '/back-posts/sistema';
    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
@endsetup

@story('deploy')
    restore_branch
    pull_master
    enter_container
    run_migrations
    update_symlinks
@endstory

@task('restore_branch')
    echo 'Restoring branch state'
    cd {{ $app_dir }}
    git restore .
@endtask

@task('pull_master')
    echo 'Updating repository'
    cd {{ $app_dir }}/current
    git checkout master
    git pull origin master
@endtask

@task('enter_container')
    echo 'Entering in the container'
    docker exec php composer install --prefer-dist --no-scripts -q -o
@endtask

@task('run_migrations')
    echo "Running migrations ({{ $release }})"
    cd {{ $app_dir }}
    docker exec php php artisan migrate
@endtask

@task('update_symlinks')
    echo "Linking storage directory"
    rm -rf {{ $app_dir }}/current/storage
    ln -nfs {{ $app_dir }}/storage {{ $app_dir }}/current/storage

    echo 'Linking .env file'
    ln -nfs {{ $app_dir }}/.env {{ $app_dir }}/current/.env

    echo 'Updating current release'
    ln -nfs {{ $app_dir }}/current {{ $app_dir }}/current
@endtask
