# SignalHire · Expanded release

SignalHire is a standalone UK recruitment app with a responsive interface, fictional demo data and separate candidate and recruiter workspaces.

## Run locally

Use PHP 8.0 or later with PDO SQLite and Fileinfo enabled, and SQLite 3.24 or later. On Ubuntu or Debian, install `php-cli php-sqlite3`. No Composer packages or JavaScript build are required.

From the extracted `signalhire` directory, run:

```sh
php bin/setup.php --demo
php -d upload_max_filesize=3M -d post_max_size=4M -S 127.0.0.1:8080 -t public
```

Open [http://127.0.0.1:8080](http://127.0.0.1:8080). Run `php bin/setup.php` without `--demo` for an empty database. Setup is safe to rerun sequentially. Demo accounts and vacancies are inserted only into an empty account database.

| Role | Email | Password |
| --- | --- | --- |
| Candidate | `candidate@example.test` | `SignalDemo2026!` |
| Recruiter | `recruiter@example.test` | `SignalDemo2026!` |

The recruiter demo has a fictional company and three live adverts. Save a short fictional CV as `sample-cv.txt` or use a sample PDF. Use separate browser sessions for the two accounts, for example one normal window and one private window.

1. In the recruiter workspace, select **New vacancy**, save a draft, then choose **Publish saved vacancy**.
2. In the candidate session, search for that vacancy and open its details. Sign in or register, upload your CV, then choose **Submit application**.
3. In the recruiter workspace, open **Applicants** for that vacancy. Download the submitted CV and change the application status to **Interview**.
4. In the candidate session, open **Applications** to see the new status. Use **Close vacancy** in the recruiter’s edit screen to remove the advert from search and block new submissions.

## Added workflows

| Feature | Where to use it |
| --- | --- |
| Saved jobs and alerts | Save a vacancy from its details page. Open **Saved jobs** to manage the list or **Alerts** to subscribe to future matching publications. |
| Candidate directory | Under **My profile**, open **Recruiter discovery settings** and opt in. Recruiters can then search shared professional profiles in **Candidate directory**. |
| Application messaging | Open **Messages and status history** from an application. Candidate and recruiter can reply in the same thread. |
| Demo billing | Open **Billing** to top up fictional credits and see publication debits. |
| Integrations | Read the [integration guide](docs/integrations.md) for the JSON feed and signed publication webhooks. |
| Company profiles | Add a website and benefits to show on the company’s vacancy pages. |

Existing installations must rerun `php bin/setup.php` to apply migrations. Follow the [upgrade guide](docs/upgrading.md) if you have existing data.

## Run the automated tests

Python 3.9+ with its standard library is needed for the HTTP test runner. Run from the project directory:

```sh
python3 -m unittest discover -s tests -v
```

The tests start their own PHP server on a local port and create a disposable seeded SQLite database. They cover the original hiring flows plus saved jobs, alerts, discovery consent, message permissions, status history, billing, feed visibility, signed webhook retries and upgrades.

## Project layout

| Path | Purpose |
| --- | --- |
| `public/` | Web document root, front controller and responsive styles |
| `app/` | Request handlers, feature services, templates, authorisation and named SQL queries |
| `database/` | Baseline schema, versioned migrations, constraints and indexes |
| `bin/setup.php` | Database initialisation and optional demo seed |
| `tests/` | HTTP and migration test suites |
| `docs/` | Workflow, integration, upgrade and verification guides |
| `REFERENCE-REVIEW.md` | JobServe research and map of all 13 supplied PHP files |

## Data and scope

The default database is `storage/signalhire.sqlite`. To override it, set `SIGNALHIRE_DB` to an absolute path outside `public/` for both setup and the app. The SQLite database contains both user data and private CV bytes. Protect it and its backups, and serve only the `public/` directory.

Each recruiter account owns one company. One current CV is available per candidate. A submitted application retains the exact CV version it was created with, even after replacement. Downloads require the recruiter role and ownership of the company that posted the vacancy. Accepted files are PDF and UTF-8 plain text up to 2 MiB. File checks use the extension, server-detected MIME type and basic content checks. Applications use a database uniqueness constraint and a single conditional insert to enforce vacancy state at submission time. Closed adverts remain readable by direct link and existing applications remain accessible to their authorised users.

Application statuses are **Submitted**, **In review**, **Interview**, **Offered** and **Unsuccessful**. Recruiters choose the current status and candidates see the latest value. Discovery is opt-in and searches profiles only. Billing uses fictional credits. Alerts and messaging are in-app. The built-in PHP server and demo credentials are for local development. See [data access](docs/data-access.md) for the permission model.
