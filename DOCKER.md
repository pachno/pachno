## Docker setup (for development or production)

### Requirements

- docker
- docker-compose

### Preflight checks

Make sure the correct version is checked out (`master` or a specific tag). If the tag you're checking out lacks the `Dockerfile`, `docker-compose.yml`, `docker` elements, you can checkout `master`, set them aside and put them back in when you switch to the version you want.

If you want to harden the PHP settings for production, or have them more permissive for development, make sure you uncomment the line for the right `php.ini` file in the `Dockerfile`.

Most configuration options are sensible defaults, except for the database configuration. If you use docker-compose as-is, it shouldn't be too much of an issue as the mysql database isn't exposed to the world. But you may want to edit the relevant files anyways (`docker-compose.yml` and `docker/b2db.yml` should match).

### Quick start

- Open a terminal
- Navigate to the `pachno` directory
- Run `docker-compose up --build`
- Wait til the database settles down
- Run the web installer at `http://localhost:9000` (most fields should be pre-filled, you can hit continue until it asks for an admin user)

### Variants and caveats

If you changed the login credentials of the administrator, you might land on an error page. Just go back to `http://localhost:9000/`

Of course, all the port decisions, names of the services, etc, are purely arbitrary. Change them to your liking.

There is currently no way to run the final steps of the installation automagically: it's a chicken-and-egg problem. The database needs to be up and running (so, it can't be done in the `Dockerfile`), and you can't really insert the correct tables and values before the relevant frameworks are built (so, you can't have a custom database image pre-populated).

In a kubernetes environment, a `Job` might do the trick.