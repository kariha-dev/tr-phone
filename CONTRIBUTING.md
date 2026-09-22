# Contributing

Thank you for considering a contribution to TR Phone.

## Development

```bash
composer install
composer check
```

## Pull Requests

- Keep changes focused.
- Add tests for behavior changes.
- Update documentation when public API behavior changes.
- Do not include `vendor/`, generated coverage reports, secrets, or customer data.

## Numbering Data

Prefix and area-code changes should be based on official or authoritative sources, preferably BTK public numbering-plan pages. Include the source and verification date in the pull request.

## Privacy

Phone numbers can be personal data. Do not post real customer numbers in issues, pull requests, tests, or documentation. Use fictional examples or mask sensitive digits, for example `0555 *** ** 78`.

## Backwards Compatibility

TR Phone follows Semantic Versioning. Public API changes should be introduced carefully. Deprecations should be documented in the changelog before removal in a future major version.
