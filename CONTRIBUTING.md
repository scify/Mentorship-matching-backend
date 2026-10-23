# Contributing

Discuss a change with the maintainers first, in an issue or by email. Please follow the code of conduct below in
all your interactions with the project.

## Pull request process

1. Set up the project as the [README](README.md#getting-started) describes, and create a branch from an
   up-to-date `master`, for example `fix/short-description`.
2. Follow the layers of the app: controllers call managers, managers hold the business rules, and only storage
   classes query the database.
3. Add or update tests for every behaviour you change (`tests/`; see [Tests](README.md#tests)).
4. Before every commit, run the checks; each must pass:

   ```bash
   ddev composer lint:check
   ddev composer test
   ```

   After a change to routes or views, also run the browser tests (`cd e2e && npm test`).
5. Write commit messages as [Conventional Commits](https://www.conventionalcommits.org/), for example
   `fix: keep the rating comment after a validation error`.
6. Describe the change under `## [Unreleased]` in [CHANGELOG.md](CHANGELOG.md). If it needs an action on the server
   (a new `.env` key, a one-off command, a data check), also add it to the "Unreleased" upgrade notes in
   [DEPLOYMENT.md](DEPLOYMENT.md#upgrade-notes).
7. Push the branch and open a pull request against `master`. Without write access to this repository, fork it and
   open the pull request from your fork. The check `ci / CI` must pass; a maintainer reviews
   and merges the pull request.

## Code of Conduct

### Our Pledge

In the interest of fostering an open and welcoming environment, we as
contributors and maintainers pledge to making participation in our project and
our community a harassment-free experience for everyone, regardless of age, body
size, disability, ethnicity, gender identity and expression, level of experience,
nationality, personal appearance, race, religion, or sexual identity and
orientation.

### Our Standards

Examples of behavior that contributes to creating a positive environment
include:

* Using welcoming and inclusive language
* Being respectful of differing viewpoints and experiences
* Gracefully accepting constructive criticism
* Focusing on what is best for the community
* Showing empathy towards other community members

Examples of unacceptable behavior by participants include:

* The use of sexualized language or imagery and unwelcome sexual attention or
  advances
* Trolling, insulting/derogatory comments, and personal or political attacks
* Public or private harassment
* Publishing others' private information, such as a physical or electronic
  address, without explicit permission
* Other conduct which could reasonably be considered inappropriate in a
  professional setting

### Our Responsibilities

Project maintainers are responsible for clarifying the standards of acceptable
behavior and are expected to take appropriate and fair corrective action in
response to any instances of unacceptable behavior.

Project maintainers have the right and responsibility to remove, edit, or
reject comments, commits, code, wiki edits, issues, and other contributions
that are not aligned to this Code of Conduct, or to ban temporarily or
permanently any contributor for other behaviors that they deem inappropriate,
threatening, offensive, or harmful.

### Scope

This Code of Conduct applies both within project spaces and in public spaces
when an individual is representing the project or its community. Examples of
representing a project or community include using an official project e-mail
address, posting via an official social media account, or acting as an appointed
representative at an online or offline event. Representation of a project may be
further defined and clarified by project maintainers.

### Enforcement

Instances of abusive, harassing, or otherwise unacceptable behavior may be
reported by contacting the project team at [info@scify.org](mailto:info@scify.org). All
complaints will be reviewed and investigated and will result in a response that
is deemed necessary and appropriate to the circumstances. The project team is
obligated to maintain confidentiality with regard to the reporter of an incident.
Further details of specific enforcement policies may be posted separately.

Project maintainers who do not follow or enforce the Code of Conduct in good
faith may face temporary or permanent repercussions as determined by other
members of the project's leadership.

### Attribution

This Code of Conduct is adapted from the [Contributor Covenant][homepage], version 1.4,
available at [http://contributor-covenant.org/version/1/4][version]

[homepage]: http://contributor-covenant.org

[version]: http://contributor-covenant.org/version/1/4/
