# City Tech OpenLab

This is the codebase for the City Tech OpenLab https://openlab.citytech.cuny.edu.

## Developer setup

After cloning the repository, you'll need to configure the following environment-specific files:

1. Copy env-sample.php to env.php sample (untracked) and replace the database credentials with your own.
1. If you're using SharDB (ie, you have 257 databases), change the `DO_SHARDB` constant to `true`. In this case, `DB_NAME` should be the name of the "global" database.
1. If you plan to use persistest caching in your local environment, change `OPENLAB_CACHE_ENGINE` to `'memcached'` and define the `$memcached_servers` array.
