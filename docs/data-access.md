# Data access

| Resource | Who can access it |
| --- | --- |
| Published vacancies | Anyone |
| Closed vacancy details | Anyone with the direct link |
| Drafts and vacancy administration | The recruiter account that owns the company |
| Saved jobs, alert settings and candidate application list | The owning candidate |
| Discovery profiles | Signed-in recruiters, after the candidate opts in |
| Applicant list and submitted CV download | The recruiter who owns the vacancy’s company |
| Application messages and status history | The candidate and the recruiter who owns the vacancy’s company |
| Credit ledger | The recruiter who owns the company |

Discovery includes name, location, skills and summary. It does not expose structured email addresses, CV filenames or downloads. Candidates can turn it off immediately. The application CV is a snapshot stored as private SQLite bytes. Replacing the current CV preserves previously submitted versions.

Run the server with `public/` as the document root. Keep the database, session files and backups outside it. State-changing requests require a session CSRF token. Recruiter ownership and candidate identity are checked on the server.
