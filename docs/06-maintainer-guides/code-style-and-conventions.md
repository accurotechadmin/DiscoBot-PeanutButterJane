# Code Style and Conventions

**Audience:** Maintainers and command authors.
**Status:** Current guide
**Last reviewed:** 2026-06-03
**Related files:** `../../src/`, `../../tests/`, `../../composer.json`
**Related docs:** [Command interface contract](../04-extensibility/command-interface-contract.md), [Testing reference](../03-technical-reference/testing-reference.md)

## PHP style

- Use `declare(strict_types=1);`.
- Keep namespaces explicit (`App` or `App\Commands`).
- Prefer small final classes in this skeleton.
- Commands implement `CommandInterface` and return string replies.
- Do not wrap imports in try/catch blocks.
- Keep command examples framework-free.

## Documentation style

- Use short paragraphs, headings, bullets, and tables.
- Include metadata near the top of each page.
- Cite relevant source paths on technical pages.
- Label unimplemented ideas exactly as **Future consideration**.
- Do not imply databases, queues, Docker-first deployment, web controllers, monitoring stacks, or log aggregation exist. Slash, mention, and DM command paths are implemented opt-in behavior and should not be listed as absent infrastructure.
