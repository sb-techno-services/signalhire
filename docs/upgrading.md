# Upgrade from the initial MVP

1. Stop the app and any workers.
2. Back up the SQLite database using your normal SQLite backup process. Include pending journal data if your backup tooling requires it.
3. Extract the new project. Copy the backed-up database to the new project's `storage/signalhire.sqlite`, or set `SIGNALHIRE_DB` to the absolute path of your existing database.
4. Run `php bin/setup.php` from the extracted project.
5. Start the server as shown in the README. Sign in and check an existing vacancy and application.

Migration files are applied once and recorded in `schema_migrations`. Existing CV bytes, user accounts, vacancies and applications are retained. An existing application receives one historical baseline entry reflecting its status at upgrade. Earlier individual status transitions cannot be reconstructed from the first release.

The release test suite creates a database using the original schema and upgrades it twice to check retention and repeatability.
