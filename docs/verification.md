# Verification guide

From the project root:

```sh
find app bin public -name '*.php' -exec php -l {} \;
python3 -m unittest discover -s tests -v
```

The automated tests start isolated PHP servers on loopback and create temporary SQLite databases. The webhook test uses a loopback receiver that deliberately returns a failure before accepting the retry. No live external account is needed.

For a manual check, use separate browser sessions for candidate and recruiter. Publish a vacancy, find it, save it, apply with a fictional CV, and exchange messages. Change the status and confirm the candidate’s history updates. At a narrow viewport, confirm the search form and detail sections stack and that every action remains reachable by keyboard.
