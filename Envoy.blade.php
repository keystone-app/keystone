@servers(['web' => $server])

@setup
    $repository = 'git@github.com:keystone-app/keystone.git';
    $releases_dir = $path . '/releases';
    $app_dir = $path . '/current'; // The symlink path
    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
@endsetup

@story('deploy')
    clone_repository
    run_composer
    run_npm
    run_migrations
    optimize_laravel
    update_symlinks
    clean_old_releases
@endstory

@task('clone_repository')
    echo 'Cloning repository ({{ $tag }})...'
    [ -d {{ $releases_dir }} ] || mkdir -p {{ $releases_dir }}
    # Clone and immediately checkout the specific tag
    git clone --depth 1 --branch {{ $tag }} {{ $repository }} {{ $new_release_dir }}
@endtask

@task('run_composer')
    echo "Installing Composer dependencies..."
    cd {{ $new_release_dir }}
    composer install --prefer-dist --no-scripts --no-dev -q -o
@endtask

@task('run_npm')
    echo "Building assets..."
    cd {{ $new_release_dir }}
    npm install --silent
    npm run build --silent
@endtask

@task('run_migrations')
    echo "Running migrations..."
    cd {{ $new_release_dir }}
    # Run migrations on the new folder BEFORE it goes live
    php artisan migrate --force
@endtask

@task('optimize_laravel')
    echo "Optimizing Laravel..."
    cd {{ $new_release_dir }}
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
@endtask

@task('update_symlinks')
    echo "Linking shared assets..."
    # Link the shared .env
    ln -nfs {{ $path }}/.env {{ $new_release_dir }}/.env
    
    # Remove the release storage and link to the persistent shared storage
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $path }}/storage {{ $new_release_dir }}/storage
    
    echo 'Flipping the symlink to v{{ $release }}...'
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}
@endtask

@task('clean_old_releases')
    echo "Cleaning up old releases..."
    cd {{ $releases_dir }}
    # Deletes all but the 5 most recent releases
    ls -dt */ | tail -n +6 | xargs -d "\n" rm -rf
@endtask

@task('rollback')
    echo "Rolling back to previous release..."
    cd {{ $releases_dir }}
    # Finds the second-to-last directory and points 'current' to it
    PREV=$(ls -1t {{ $releases_dir }} | head -n 2 | tail -n 1)
    ln -nfs {{ $releases_dir }}/$PREV {{ $app_dir }}
    echo "Rolled back to $PREV"
@endtask