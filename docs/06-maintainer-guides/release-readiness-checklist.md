# Release Readiness Checklist

**Audience:** Maintainers preparing a handoff, tag, or publication.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../README.md`, `../../composer.json`, `../../.env.example`, `../../docs/`, `../../src/`, `../../tests/`
**Related docs:** [Documentation maintenance](documentation-maintenance.md), [Test suite tour](test-suite-tour.md), [From GitHub to a running bot](../01-user-guides/from-github-to-running-bot.md), [Dependency management](../02-operator-guides/dependency-management.md)

Use this checklist before declaring the repository ready for users or future maintainers.

## Before release

- Run `composer check` when dependencies are available.
- Confirm `README.md` points readers to `docs/README.md`.
- Confirm `.env.example` contains only documented variables and no secrets.
- Confirm command docs match `../../config/commands.php`.
- Confirm parser docs match `../../tests/CommandRouterTest.php`.
- Confirm logger docs describe console plus optional daily JSON logging without implying log aggregation or monitoring.
- Confirm unimplemented infrastructure is described as absent or **Future consideration**.
- Run a local Markdown link check.

## After changing key areas

| Changed area | Required follow-up |
| --- | --- |
| Config/defaults | Update `.env.example`, config docs, operator docs, and environment index. |
| Commands | Update user command docs, examples if relevant, command index, and tests. |
| Router/parser | Update parser docs, technical reference, data-flow index, and tests. |
| Logger | Update operator logging docs, technical logging reference, and ADR 0004 if the decision changes. |
| Command interface/context | Update extensibility pages, examples, tests, and ADR 0003 if the decision changes. |
| Deployment/runtime | Update the GitHub-to-running-bot guide and keep operational docs generic unless repository-owned files are added. |

## Final documentation scan

- No scaffold/stub language remains.
- Every Markdown page under `docs/` has audience, status, last reviewed, related files, and related docs.
- Relative Markdown links resolve.
- Source paths in technical docs still exist.
