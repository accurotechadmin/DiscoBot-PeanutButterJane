# Documentation Maintenance

**Audience:** Maintainers editing Markdown docs.
**Status:** Maintained current-state documentation
**Last reviewed:** 2026-06-04
**Related files:** `../../README.md`, `../../docs/`, `../../bin/bot.php`, `../../config/`, `../../src/`, `../../tests/`
**Related docs:** [How to use these docs](../00-start-here/how-to-use-these-docs.md), [Documentation home](../README.md), [Coding LLM session primer](coding-llm-session-primer.md), [Documentation study report](documentation-study-report.md), [Release readiness checklist](release-readiness-checklist.md)

Documentation should describe current repository behavior, not an imagined larger platform.

## Before editing docs

- Read the affected source and tests first.
- Check page metadata for related files and docs.
- Search for the same behavior elsewhere to avoid contradictions.
- Use **Future consideration** exactly for ideas that are not implemented.

## Synchronization checklist

| If this changes | Update these docs |
| --- | --- |
| Environment variables or defaults | User configuration, operator environment, technical configuration, environment index. |
| Command registry or built-in replies | Built-in commands, command index, help metadata examples, tests references. |
| Parser behavior | Prefix command guide, command routing reference, data-flow index, ADR 0002 if decision changes. |
| Command interface/context | Extensibility guides, examples, command context reference, ADR 0003 if decision changes. |
| Logger behavior | Operator logging, logging reference, ADR 0004 if decision changes. |
| Runtime startup | GitHub-to-running-bot guide, quick start, running guide, runtime lifecycle, operator startup validation. |

## Sanity checks

```bash
find docs -type f -name '*.md' -print | sort
rg -n 'Scaffold|useful current-state stub|None yet|TODO|TBD|To expand|placeholder|FIXME' docs README.md *.txt || true
rg -n 'Laravel|Symfony|database|queue|Docker|file logging|systemd|supervisord|PM2|monitoring|controller|HTTP|webhook' docs README.md *.txt || true
```

The stale-language scan should return no accidental scaffold placeholders; intentional mentions, such as `storage/logs/.gitkeep` being a placeholder, must clearly describe current behavior. The infrastructure-term scan may return intentional absence, external option, or **Future consideration** statements.

**Future consideration:** if the docs start relying heavily on heading fragments, add a Markdown link checker that validates both target files and `#heading` anchors.
