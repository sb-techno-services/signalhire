# Job alerts

Candidates create private alerts using keyword, location and employment type. Empty fields match any value. An alert considers only publication events after its creation. Matches are recorded once per vacancy per alert, and the Matches list combines overlapping alerts without duplicate vacancy cards.

Matches are updated when the candidate visits **Alerts**. A local operator can also run `php bin/run-alerts.php` periodically against the same database. Set `SIGNALHIRE_DB` for the command if the app uses a custom path. Matches appear in the app. Closing a vacancy removes it from the active matches list, and removing an alert clears its recorded matches.

To try it, create an alert for Java in London and publish a new matching vacancy from the recruiter account. Open the candidate’s Alerts page to see the match.
