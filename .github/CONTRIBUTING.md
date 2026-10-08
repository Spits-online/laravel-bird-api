# Contributing

Contributions are welcome. A few things make them easy to accept:

- **Open an issue or discussion first** for anything bigger than a bug fix, so we can agree on the approach before you spend time on it.
- **One pull request per change**, with a clear title and description.
- **Add tests.** A fix comes with a test that fails without it; a feature comes with tests for its success and failure paths.
- **Run `composer check`** before pushing (Pint, PHPStan and Pest, the same checks CI runs).
- **Document behaviour changes** in the README and add an entry under `[Unreleased]` in `CHANGELOG.md`.
- **Respect semver.** Breaking the public API needs a major version and an UPGRADE.md entry, so it needs a very good reason.

## Running the tests

```bash
composer install
composer test
```
