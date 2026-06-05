# Delivery Operations Data Requirements Index

**Audience:** Business owners, managers, dispatchers, command authors, maintainers, and future implementers planning delivery-business commands.
**Status:** Future planning example
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../config/bot.php`, `../../config/commands.php`, `../../src/CommandRouter.php`, `../../src/CommandContext.php`, `../../src/Commands/`, `../../tests/`
**Related docs:** [Delivery operations bot blueprint](delivery-operations-bot-blueprint.md), [Examples](README.md), [How to use these docs](../00-start-here/how-to-use-these-docs.md), [Command registration and aliases](../04-extensibility/command-registration-and-aliases.md), [Configuration reference](../03-technical-reference/configuration-reference.md), [Master index](../07-reference/master-index.md)

This companion document catalogues the information that a future delivery-operations bot could synthesize, the source data needed to calculate or formulate that information, the sensitivity of that data, and the commands or workflows that would likely consume it.

Current behavior: the repository does not implement delivery-business storage, driver profiles, route imports, pace commands, route notes, polls, approval workflows, rescue planning, analytics, dashboards, or business data integrations. It remains a lightweight DiscordPHP command skeleton. Every delivery-business output, data element, calculation, storage idea, command, workflow, sensitivity policy, and integration described below is **Future consideration** until implemented in source and tests.

## 1. How to use this index

Use this document when deciding what to build next. Start with the information you want the bot to provide, then inspect the minimum data needed to provide it safely and accurately.

Recommended reading order:

1. Review the [Delivery operations bot blueprint](delivery-operations-bot-blueprint.md) for the product vision.
2. Use [Section 4](#4-synthesized-information-catalog) to choose the exact outputs you want.
3. Use [Section 5](#5-data-element-dictionary) to identify required data fields.
4. Use [Section 6](#6-sensitivity-and-access-model) to decide who can see or edit the data.
5. Use [Section 7](#7-calculation-notes-and-formulas) to verify the math and confidence assumptions.
6. Use [Section 8](#8-build-priority-by-data-cost) to choose a low-risk implementation phase.

## 2. Classification keys

### 2.1 Requirement level

| Label | Meaning |
| --- | --- |
| Required | **Future consideration:** The output cannot be calculated or formulated without this data. |
| Recommended | **Future consideration:** The output works without this data but is less accurate, less useful, or less safe. |
| Optional | **Future consideration:** The output can be enhanced by this data, but it should not block an MVP. |
| Derived | **Future consideration:** The field is calculated from other fields and should usually not be hand-entered. |

### 2.2 Sensitivity level

| Level | Meaning | Example |
| --- | --- | --- |
| Public operations | **Future consideration:** Safe for normal operating channels if the business allows it. | Route ID, general route note category. |
| Internal operations | **Future consideration:** Suitable for drivers, dispatchers, managers, or admins based on role. | Stops completed, target finish time, route notes. |
| Restricted performance | **Future consideration:** Driver performance or productivity data that should be limited to the driver, dispatchers, managers, or admins. | Stops/hour, behind/ahead minutes, driver history. |
| Sensitive operations | **Future consideration:** Data that could create safety, privacy, security, or customer-service risk if broadly shared. | Access instructions, safety notes, customer-specific route guidance. |
| Administrative secret | **Future consideration:** Data that should not appear in normal bot replies and may belong only in environment/config or a secure store. | Bot token, external API credentials. |

### 2.3 Freshness expectations

| Freshness | Meaning |
| --- | --- |
| Real-time-ish | **Future consideration:** Useful only if updated frequently during the route. |
| Same-day | **Future consideration:** Useful for today's dispatch but not necessarily minute-by-minute. |
| Historical | **Future consideration:** Useful across days, weeks, or seasons. |
| Static / config | **Future consideration:** Changes rarely and can be validated at startup or admin edit time. |

## 3. Minimal data model overview

The tables below summarize future data groups before the deeper dictionary.

| Data group | Why it exists | MVP need? | Sensitivity |
| --- | --- | --- | --- |
| Driver identity | **Future consideration:** Map Discord users to business driver records. | Yes | Internal operations. |
| Role and permission mapping | **Future consideration:** Decide who can view, edit, approve, broadcast, or configure. | Yes | Internal operations. |
| Daily route assignment | **Future consideration:** Know which driver owns which route today. | Yes | Internal operations. |
| Route progress events | **Future consideration:** Calculate pace, projected finish, stale updates, and rescue need. | Yes | Restricted performance. |
| Business-day settings | **Future consideration:** Define target finish time, timezone, risk thresholds, and stale-update thresholds. | Yes | Internal operations. |
| Route notes | **Future consideration:** Preserve tips and warnings from drivers for future drivers. | Phase 2 | Sensitive operations depending on category. |
| Route-note approvals | **Future consideration:** Prevent raw notes from becoming official guidance without review. | Phase 2 | Internal operations. |
| Poll responses | **Future consideration:** Convert post-route feedback into structured route intelligence. | Phase 2 | Internal or restricted performance. |
| Delay and issue reports | **Future consideration:** Explain route risk and feed dispatch decisions. | Phase 2 | Sensitive operations. |
| Rescue plans | **Future consideration:** Record and reason about route assistance. | Phase 3 | Restricted performance. |
| Broadcast events | **Future consideration:** Audit mass or targeted communications. | Phase 3 | Internal operations. |
| Historical aggregates | **Future consideration:** Support manager trends and business intelligence. | Phase 4 | Restricted performance. |

## 4. Synthesized information catalog

### 4.1 Current stops completed

**Future consideration:** Show a driver's latest known completed stop count.

| Field | Requirement | Notes |
| --- | --- | --- |
| Driver identity | Required | Discord user or selected driver must resolve to a driver profile. |
| Daily route assignment | Required | The bot must know the driver's active route for the date. |
| Latest progress event | Required | Contains completed stops and timestamp. |
| Total planned stops | Recommended | Needed to show `completed / total`; not required to show completed count alone. |
| Data source | Recommended | Driver update, dispatcher update, route import, or integration. |

Suggested commands:

- **Future consideration:** `/route-status`.
- **Future consideration:** `/driver @driver`.
- **Future consideration:** `/ops-summary`.

Sensitivity: **Future consideration:** restricted performance when tied to an individual driver.

### 4.2 Stops remaining

**Future consideration:** Calculate how many planned stops remain.

Formula:

```text
stops_remaining = max(total_planned_stops - completed_stops, 0)
```

| Field | Requirement | Notes |
| --- | --- | --- |
| Total planned stops | Required | Usually from route import or manual route setup. |
| Completed stops | Required | Latest progress event. |
| Cancelled or removed stops | Optional | Improves accuracy if routes are adjusted mid-day. |
| Added stops | Optional | Improves accuracy if route size changes. |

Suggested commands:

- **Future consideration:** `/pace`.
- **Future consideration:** `/need-pace`.
- **Future consideration:** `/driver @driver`.

Sensitivity: **Future consideration:** restricted performance.

### 4.3 Current average stops per hour

**Future consideration:** Calculate a driver's route-average pace.

Formula:

```text
current_stops_per_hour = completed_stops / active_route_hours_elapsed
```

Minimum hard data needed:

| Field | Requirement | Notes |
| --- | --- | --- |
| Completed stops | Required | Latest completed stop count. |
| Route start time | Required | Start of active delivery time. |
| Current timestamp | Required | Use a fixed clock in tests. |

Accuracy enhancers:

| Field | Requirement | Notes |
| --- | --- | --- |
| Break time | Optional | Exclude unpaid or non-delivery time if policy requires. |
| Delays / issues | Optional | Explain low pace without changing the basic calculation. |
| Route difficulty | Optional | Helps compare route context fairly. |

Suggested commands:

- **Future consideration:** `/pace`.
- **Future consideration:** `/driver @driver`.
- **Future consideration:** `/ops-summary` aggregates.

Sensitivity: **Future consideration:** restricted performance.

### 4.4 Rolling stops per hour

**Future consideration:** Calculate recent pace over a rolling window, such as 30 or 60 minutes.

Formula:

```text
rolling_stops_per_hour = stops_completed_in_window / window_hours
```

| Field | Requirement | Notes |
| --- | --- | --- |
| Progress event history | Required | Need at least two progress points around the window. |
| Window length | Required | Example: 30 or 60 minutes. |
| Current timestamp | Required | Defines the end of the window. |
| Interpolation rule | Recommended | Defines how to estimate stops at window boundary when no exact event exists. |

Suggested commands:

- **Future consideration:** `/pace`.
- **Future consideration:** `/driver @driver`.
- **Future consideration:** `/at-risk`.

Sensitivity: **Future consideration:** restricted performance.

### 4.5 Required pace to finish by target time

**Future consideration:** Calculate the stops/hour needed from now until the configured target finish time.

Formula:

```text
required_stops_per_hour = stops_remaining / hours_until_target_finish
```

Minimum hard data needed:

| Field | Requirement | Notes |
| --- | --- | --- |
| Total planned stops | Required | Needed for remaining stops. |
| Completed stops | Required | Latest progress event. |
| Target finish time | Required | Example: 20:30 in the station timezone. |
| Current timestamp | Required | Determines hours remaining. |
| Timezone | Required | Prevents incorrect target math. |

Edge cases:

- **Future consideration:** If `hours_until_target_finish <= 0` and stops remain, the driver has missed the target.
- **Future consideration:** If `stops_remaining = 0`, required pace is `0` and route is complete or effectively complete.
- **Future consideration:** If total stops are unknown, the bot should not pretend to know required pace.

Suggested commands:

- **Future consideration:** `/need-pace`.
- **Future consideration:** `/pace`.
- **Future consideration:** `/driver @driver`.

Sensitivity: **Future consideration:** restricted performance.

### 4.6 Projected finish time

**Future consideration:** Estimate when a driver will finish if current pace continues.

Formula:

```text
projected_finish = now + (stops_remaining / pace_basis_stops_per_hour)
```

| Field | Requirement | Notes |
| --- | --- | --- |
| Stops remaining | Required | Derived from total and completed stops. |
| Pace basis | Required | Current average pace, rolling pace, or selected blended pace. |
| Current timestamp | Required | Projection starts now. |
| Minimum pace guard | Recommended | Prevent division by zero or unrealistic projections. |
| Confidence label | Recommended | Shows whether projection is based on fresh or stale data. |

Suggested commands:

- **Future consideration:** `/finish-time`.
- **Future consideration:** `/pace`.
- **Future consideration:** `/driver @driver`.
- **Future consideration:** `/ops-summary`.

Sensitivity: **Future consideration:** restricted performance.

### 4.7 Ahead or behind target

**Future consideration:** Show whether the driver is ahead of, on, or behind the target finish time.

Formula:

```text
minutes_behind = projected_finish_time - target_finish_time
```

| Field | Requirement | Notes |
| --- | --- | --- |
| Projected finish time | Required | Derived. |
| Target finish time | Required | Config or route-specific override. |
| Risk threshold settings | Recommended | Defines green/yellow/orange/red labels. |

Suggested commands:

- **Future consideration:** `/pace`.
- **Future consideration:** `/at-risk`.
- **Future consideration:** `/ops-summary`.

Sensitivity: **Future consideration:** restricted performance.

### 4.8 Driver risk level

**Future consideration:** Convert projection and freshness into an easy risk label.

Possible inputs:

| Field | Requirement | Notes |
| --- | --- | --- |
| Minutes ahead/behind target | Required | Primary risk input. |
| Last update age | Required | Stale data may become `Unknown` or higher risk. |
| Risk thresholds | Required | Configurable yellow/orange/red thresholds. |
| Open issues | Recommended | Vehicle, access, safety, or package problems can raise risk. |
| Required pace | Recommended | Unrealistic required pace indicates rescue risk. |
| Route difficulty | Optional | Prevents overreacting to known difficult routes. |

Suggested commands:

- **Future consideration:** `/driver @driver`.
- **Future consideration:** `/at-risk`.
- **Future consideration:** `/exceptions`.

Sensitivity: **Future consideration:** restricted performance.

### 4.9 Stale update status

**Future consideration:** Identify drivers whose route state may be out of date.

Formula:

```text
last_update_age_minutes = now - latest_progress_event_timestamp
```

| Field | Requirement | Notes |
| --- | --- | --- |
| Latest progress timestamp | Required | From progress events. |
| Current timestamp | Required | Use fixed clock in tests. |
| Stale threshold minutes | Required | Configurable by business. |
| Driver active route status | Recommended | Avoid alerting for completed or inactive drivers. |

Suggested commands:

- **Future consideration:** `/stale-updates`.
- **Future consideration:** `/driver @driver`.
- **Future consideration:** `/exceptions`.

Sensitivity: **Future consideration:** internal operations or restricted performance.

### 4.10 Rescue needed estimate

**Future consideration:** Estimate whether a driver needs help and roughly how many stops should be rescued.

Possible formula:

```text
recoverable_stops_by_target = realistic_pace_from_now * hours_until_target
rescue_stops_needed = max(stops_remaining - recoverable_stops_by_target, 0)
```

| Field | Requirement | Notes |
| --- | --- | --- |
| Stops remaining | Required | From route state. |
| Hours until target | Required | From time settings. |
| Realistic pace assumption | Required | Business-defined, driver-specific, route-specific, or recent rolling pace. |
| Candidate helper availability | Recommended | Needed to suggest who can help. |
| Route proximity | Optional | Improves practical rescue suggestions. |
| Package transfer constraints | Optional | Needed if transfers have operational restrictions. |

Suggested commands:

- **Future consideration:** `/rescue-candidates`.
- **Future consideration:** `/rescue-plan`.
- **Future consideration:** `/at-risk`.

Sensitivity: **Future consideration:** restricted performance.

### 4.11 Rescue candidate capacity

**Future consideration:** Estimate which drivers can accept rescue stops without becoming late.

Possible formula:

```text
spare_time_hours = target_finish_time - projected_finish_time
candidate_rescue_capacity = max(spare_time_hours * realistic_rescue_pace, 0)
```

| Field | Requirement | Notes |
| --- | --- | --- |
| Candidate projected finish | Required | Derived from candidate route state. |
| Target finish time | Required | Same-day target or driver-specific target. |
| Realistic rescue pace | Required | May differ from normal route pace. |
| Driver eligibility | Recommended | Some drivers may be unavailable for rescue. |
| Location/proximity | Recommended | Prevents impractical recommendations. |
| Dispatcher region | Optional | Keeps recommendations within operating zones. |

Sensitivity: **Future consideration:** restricted performance.

### 4.12 Operations summary

**Future consideration:** Summarize the whole day for manager or dispatcher awareness.

Possible outputs:

- **Future consideration:** Active drivers.
- **Future consideration:** Total planned stops.
- **Future consideration:** Completed stops.
- **Future consideration:** Remaining stops.
- **Future consideration:** On-time projection percentage.
- **Future consideration:** At-risk routes.
- **Future consideration:** Critical routes.
- **Future consideration:** No-update drivers.
- **Future consideration:** Open issues.
- **Future consideration:** Pending route notes.

Required data groups:

| Data group | Requirement | Notes |
| --- | --- | --- |
| Active daily route assignments | Required | Base population. |
| Progress events | Required | Needed for stop totals and projections. |
| Target finish settings | Required | Needed for on-time projections. |
| Risk thresholds | Required | Needed for at-risk counts. |
| Issue reports | Recommended | Needed for exception summary. |
| Route note queue | Optional | Needed for knowledge-base summary. |

Suggested commands:

- **Future consideration:** `/ops-summary`.
- **Future consideration:** `/exceptions`.

Sensitivity: **Future consideration:** manager/dispatcher restricted.

### 4.13 Driver stat card

**Future consideration:** Provide a dispatcher or manager with one driver's operational context.

Possible outputs:

- **Future consideration:** Driver name.
- **Future consideration:** Route ID.
- **Future consideration:** Completed / total stops.
- **Future consideration:** Stops remaining.
- **Future consideration:** Current and rolling pace.
- **Future consideration:** Required pace.
- **Future consideration:** Projected finish.
- **Future consideration:** Risk level.
- **Future consideration:** Last update age.
- **Future consideration:** Open issues.
- **Future consideration:** Approved and pending route-note count.
- **Future consideration:** Suggested dispatcher action.

Required data groups:

| Data group | Requirement | Notes |
| --- | --- | --- |
| Driver identity | Required | Selects driver. |
| Daily route assignment | Required | Selects route. |
| Progress events | Required | Powers pace and projections. |
| Business-day settings | Required | Target and thresholds. |
| Issue reports | Recommended | Adds context. |
| Route notes | Optional | Adds route knowledge status. |
| Dispatcher notes | Optional | Adds internal context. |

Sensitivity: **Future consideration:** restricted performance.

### 4.14 Suggested dispatcher action

**Future consideration:** Turn route state into a short recommended next step.

Possible rules:

| Signal | Possible suggestion |
| --- | --- |
| Behind target and required pace is realistic | **Future consideration:** Ask driver for blocker and request another update soon. |
| Behind target and required pace is unrealistic | **Future consideration:** Evaluate rescue plan. |
| No recent update | **Future consideration:** Ask driver for current completed stops. |
| Multiple access issues | **Future consideration:** Ask whether access codes or apartments are the blocker. |
| Vehicle issue open | **Future consideration:** Escalate to dispatcher or manager. |
| Route note disputed | **Future consideration:** Ask driver to confirm or correct note. |

Required data:

- **Future consideration:** Driver risk level.
- **Future consideration:** Required pace.
- **Future consideration:** Last update age.
- **Future consideration:** Open issues and delay reasons.
- **Future consideration:** Business rules for action templates.

Sensitivity: **Future consideration:** dispatcher/manager restricted.

### 4.15 Delay reason summary

**Future consideration:** Summarize why drivers are losing time.

Required data:

| Field | Requirement | Notes |
| --- | --- | --- |
| Delay event type | Required | Apartment, access, traffic, vehicle, package sort, business closed, other. |
| Driver and route | Required | Associates delay with operation. |
| Timestamp | Required | Needed for same-day and historical summaries. |
| Free-text description | Optional | Useful but more sensitive and harder to analyze. |
| Resolution status | Optional | Helps manage open issues. |

Suggested commands:

- **Future consideration:** `/delay`.
- **Future consideration:** `/exceptions`.
- **Future consideration:** `/eod-report`.

Sensitivity: **Future consideration:** sensitive operations or restricted performance.

### 4.16 Route note recommendation

**Future consideration:** Surface approved tips for a driver's current route or segment.

Required data:

| Field | Requirement | Notes |
| --- | --- | --- |
| Driver's active route | Required | Identifies route or segment. |
| Route note targets | Required | Matches notes to route, segment, address, complex, or category. |
| Approval status | Required | Show approved notes by default. |
| Visibility level | Required | Prevents sensitive notes from going to the wrong audience. |
| Freshness status | Recommended | Helps decide whether to show or ask for confirmation. |
| Helpful/dispute counts | Optional | Improves ranking. |

Suggested commands:

- **Future consideration:** `/route-notes`.
- **Future consideration:** `/driver @driver`.
- **Future consideration:** Morning route brief workflow.

Sensitivity: **Future consideration:** internal or sensitive operations depending on note category.

### 4.17 Route note approval queue

**Future consideration:** List submitted notes that need dispatcher or manager review.

Required data:

| Field | Requirement | Notes |
| --- | --- | --- |
| Note text | Required | Submission body. |
| Submitted by | Required | Accountability and clarification. |
| Submitted timestamp | Required | Queue ordering. |
| Proposed target | Recommended | Route, segment, address, complex, or category. |
| Proposed category | Recommended | Helps reviewers triage. |
| Sensitivity flag | Recommended | Access or safety notes may need manager review. |
| Duplicate candidates | Optional | Helps merge repeated notes. |

Suggested commands:

- **Future consideration:** `/review-notes`.
- **Future consideration:** `/approve-note`.
- **Future consideration:** `/reject-note`.
- **Future consideration:** `/attach-note`.

Sensitivity: **Future consideration:** sensitive operations.

### 4.18 Note confidence score

**Future consideration:** Estimate whether a route note is reliable.

Possible inputs:

| Field | Requirement | Notes |
| --- | --- | --- |
| Approval status | Required | Pending notes should not be treated as official guidance. |
| Helpful confirmations | Recommended | Drivers mark useful and accurate notes. |
| Disputes | Recommended | Drivers report stale or incorrect notes. |
| Last confirmed timestamp | Recommended | Freshness matters for access, construction, and lockers. |
| Note age | Recommended | Old notes may be stale. |
| Approver role | Optional | Manager-approved safety note may carry more authority. |

Possible labels:

- **Future consideration:** New.
- **Future consideration:** Approved.
- **Future consideration:** High confidence.
- **Future consideration:** Needs confirmation.
- **Future consideration:** Disputed.
- **Future consideration:** Stale.
- **Future consideration:** Retired.

Sensitivity: **Future consideration:** internal operations or sensitive operations.

### 4.19 Route poll insight

**Future consideration:** Convert poll responses into route guidance and manager summaries.

Required data:

| Field | Requirement | Notes |
| --- | --- | --- |
| Poll question ID | Required | Defines what was asked. |
| Driver response | Required | Structured answer. |
| Route ID / segment | Required | Associates answer with route knowledge. |
| Timestamp | Required | Freshness and historical grouping. |
| Driver identity | Recommended | Needed for accountability and duplicate handling. |
| Free-text follow-up | Optional | Richer context but more sensitive and harder to analyze. |

Suggested outputs:

- **Future consideration:** Route difficulty trend.
- **Future consideration:** Common blocker summary.
- **Future consideration:** Note confirmation.
- **Future consideration:** Problem-location trend.

Sensitivity: **Future consideration:** internal operations, sometimes restricted performance.

### 4.20 Problem location summary

**Future consideration:** Identify repeated issues tied to addresses, complexes, businesses, lockers, or zones.

Required data:

| Field | Requirement | Notes |
| --- | --- | --- |
| Location key | Required | Address, complex, business, locker, zone, or normalized route target. |
| Issue category | Required | Access, safety, parking, business closed, dog, locker, navigation, other. |
| Occurrence timestamps | Required | Count frequency over time. |
| Route associations | Recommended | Shows routes affected. |
| Driver reports | Recommended | Provides evidence and examples. |
| Resolution notes | Optional | Tracks whether issue was fixed. |

Suggested commands:

- **Future consideration:** `/problem-locations`.
- **Future consideration:** `/route-history`.
- **Future consideration:** `/route-knowledge-health`.

Sensitivity: **Future consideration:** sensitive operations, especially if customer-specific.

### 4.21 Broadcast audience membership

**Future consideration:** Determine who should receive a targeted operational message.

Possible audience rules:

| Audience | Required data |
| --- | --- |
| All active drivers | **Future consideration:** Active daily driver assignments. |
| Projected late drivers | **Future consideration:** Driver projections and risk thresholds. |
| Stale update drivers | **Future consideration:** Last progress timestamp and stale threshold. |
| Drivers on a route segment | **Future consideration:** Route assignments and segment mapping. |
| Drivers needing note confirmation | **Future consideration:** Route assignments and stale route notes. |
| Dispatchers for a region | **Future consideration:** Dispatcher profile and region assignment. |

Required guardrail data:

- **Future consideration:** Sender identity.
- **Future consideration:** Sender role.
- **Future consideration:** Audience count.
- **Future consideration:** Message content.
- **Future consideration:** Confirmation timestamp.
- **Future consideration:** Delivery results.

Sensitivity: **Future consideration:** internal operations, with audit importance.

### 4.22 End-of-day report

**Future consideration:** Summarize operations after routes end.

Required data groups:

| Data group | Requirement | Notes |
| --- | --- | --- |
| Daily route assignments | Required | Report population. |
| Progress events | Required | Completion, pace, finish estimates. |
| Completion events | Recommended | Actual finish times if captured. |
| Delay and issue events | Recommended | Explains exceptions. |
| Rescue plans | Recommended | Shows interventions. |
| Route notes and polls | Recommended | Captures knowledge gained. |
| Historical comparison | Optional | Adds trend context. |

Suggested commands:

- **Future consideration:** `/eod-report`.

Sensitivity: **Future consideration:** manager/dispatcher restricted.

### 4.23 Driver history summary

**Future consideration:** Show historical trends for a driver.

Required data:

| Field | Requirement | Notes |
| --- | --- | --- |
| Driver identity | Required | Selected driver. |
| Historical route assignments | Required | Days and routes worked. |
| Historical progress or completion data | Required | Pace, finish, completion trend. |
| Retention policy | Required | Defines how much history can be shown. |
| Permission check | Required | Strongly restrict access. |
| Route difficulty context | Recommended | Avoid unfair comparisons. |
| Rescue received/given | Optional | Adds operational context. |

Sensitivity: **Future consideration:** restricted performance.

### 4.24 Route difficulty score

**Future consideration:** Estimate whether a route is harder than normal.

Possible inputs:

| Field | Requirement | Notes |
| --- | --- | --- |
| Historical route performance | Required | Prior finish times and pace. |
| Route size | Required | Stop count and package count if available. |
| Driver poll responses | Recommended | Captures lived difficulty. |
| Delay categories | Recommended | Explains difficulty. |
| Apartment/business/rural mix | Optional | Improves fairness. |
| Distance or travel time | Optional | Improves quality if available. |
| Weather or traffic | Optional | External integration, not an MVP need. |

Sensitivity: **Future consideration:** internal operations, sometimes restricted performance.

### 4.25 Route knowledge health

**Future consideration:** Summarize whether the route-note knowledge base is useful and maintained.

Required data:

| Field | Requirement | Notes |
| --- | --- | --- |
| Approved note count | Required | Base inventory. |
| Pending note count | Required | Review queue size. |
| Stale note count | Required | Maintenance need. |
| Disputed note count | Required | Trust issue. |
| Helpful confirmations | Recommended | Quality signal. |
| Route or category grouping | Recommended | Shows where knowledge work is needed. |

Suggested commands:

- **Future consideration:** `/route-knowledge-health`.
- **Future consideration:** `/review-notes`.

Sensitivity: **Future consideration:** manager/dispatcher internal operations.

## 5. Data element dictionary

### 5.1 Identity and access fields

| Data element | Type | Required for | Sensitivity | Freshness |
| --- | --- | --- | --- | --- |
| Discord user ID | String | **Future consideration:** Linking drivers, dispatchers, managers, command permissions. | Internal operations. | Static / config. |
| Driver external ID | String | **Future consideration:** CSV import, external data matching. | Internal operations. | Static / config. |
| Driver display name | String | **Future consideration:** Human-readable command output. | Internal operations. | Static / config. |
| Driver active status | Boolean | **Future consideration:** Active route lists and broadcasts. | Internal operations. | Same-day. |
| Business role | Enum | **Future consideration:** Driver/dispatcher/manager/admin permission checks. | Internal operations. | Static / config. |
| Discord role IDs | List | **Future consideration:** Role-based access. | Internal operations. | Static / config. |
| Dispatcher region | String | **Future consideration:** Regional queues and broadcasts. | Internal operations. | Static / config. |

### 5.2 Route assignment fields

| Data element | Type | Required for | Sensitivity | Freshness |
| --- | --- | --- | --- | --- |
| Service date | Date | **Future consideration:** Daily route state. | Internal operations. | Same-day. |
| Route ID | String | **Future consideration:** Route assignment, notes, history. | Internal operations. | Same-day / historical. |
| Driver ID | String | **Future consideration:** Route ownership. | Internal operations. | Same-day. |
| Dispatcher ID | String | **Future consideration:** Dispatcher queues and accountability. | Internal operations. | Same-day. |
| Planned total stops | Integer | **Future consideration:** Stops remaining, pace, summaries. | Restricted performance. | Same-day. |
| Planned package count | Integer | **Future consideration:** Route size and difficulty context. | Internal operations. | Same-day. |
| Planned start time | DateTime | **Future consideration:** Pace and elapsed time. | Internal operations. | Same-day. |
| Actual start time | DateTime | **Future consideration:** More accurate pace. | Internal operations. | Real-time-ish. |
| Target finish time | DateTime | **Future consideration:** Required pace, risk, projections. | Internal operations. | Static / same-day. |
| Route status | Enum | **Future consideration:** Active, complete, paused, reassigned, cancelled. | Internal operations. | Real-time-ish. |
| Route segment labels | List | **Future consideration:** Segment-specific notes and polls. | Internal operations. | Same-day / historical. |

### 5.3 Progress fields

| Data element | Type | Required for | Sensitivity | Freshness |
| --- | --- | --- | --- | --- |
| Progress event ID | String | **Future consideration:** Audit and event history. | Internal operations. | Historical. |
| Completed stops | Integer | **Future consideration:** Pace and remaining stops. | Restricted performance. | Real-time-ish. |
| Progress timestamp | DateTime | **Future consideration:** Pace, stale update, projections. | Restricted performance. | Real-time-ish. |
| Progress source | Enum | **Future consideration:** Driver, dispatcher, import, integration. | Internal operations. | Real-time-ish. |
| Remaining stops override | Integer | **Future consideration:** Adjusted routes when total changes. | Restricted performance. | Real-time-ish. |
| Break state | Enum | **Future consideration:** Active, break, lunch, return-to-station. | Restricted performance. | Real-time-ish. |
| Break duration | Duration | **Future consideration:** Active-time pace. | Restricted performance. | Same-day. |

### 5.4 Issue and delay fields

| Data element | Type | Required for | Sensitivity | Freshness |
| --- | --- | --- | --- | --- |
| Issue ID | String | **Future consideration:** Issue tracking. | Internal operations. | Historical. |
| Issue category | Enum | **Future consideration:** Delay summary and exceptions. | Sensitive operations. | Same-day / historical. |
| Issue description | Text | **Future consideration:** Dispatcher context. | Sensitive operations. | Same-day / historical. |
| Issue timestamp | DateTime | **Future consideration:** Ordering and freshness. | Sensitive operations. | Same-day / historical. |
| Issue status | Enum | **Future consideration:** Open, resolved, escalated. | Sensitive operations. | Real-time-ish. |
| Safety flag | Boolean | **Future consideration:** Escalation and restricted visibility. | Sensitive operations. | Same-day / historical. |
| Location key | String | **Future consideration:** Problem-location summaries. | Sensitive operations. | Historical. |

### 5.5 Route note fields

| Data element | Type | Required for | Sensitivity | Freshness |
| --- | --- | --- | --- | --- |
| Route note ID | String | **Future consideration:** Review, approval, updates. | Internal operations. | Historical. |
| Note text | Text | **Future consideration:** Route guidance. | Sensitive operations. | Historical. |
| Note category | Enum | **Future consideration:** Access, parking, navigation, safety, locker, business, apartments. | Sensitive operations. | Historical. |
| Submitted by | Driver ID | **Future consideration:** Accountability and clarification. | Internal operations. | Historical. |
| Submitted timestamp | DateTime | **Future consideration:** Queue ordering and freshness. | Internal operations. | Historical. |
| Approval status | Enum | **Future consideration:** Pending, approved, rejected, retired. | Internal operations. | Historical. |
| Approved by | User ID | **Future consideration:** Approval audit. | Internal operations. | Historical. |
| Approved timestamp | DateTime | **Future consideration:** Freshness and audit. | Internal operations. | Historical. |
| Visibility level | Enum | **Future consideration:** Driver, dispatcher, manager, admin, safety-sensitive. | Sensitive operations. | Static / historical. |
| Target type | Enum | **Future consideration:** Route, segment, address, complex, business, zone. | Sensitive operations. | Historical. |
| Target key | String | **Future consideration:** Match notes to routes. | Sensitive operations. | Historical. |
| Helpful count | Integer | **Future consideration:** Confidence score. | Internal operations. | Historical. |
| Dispute count | Integer | **Future consideration:** Confidence score and review queue. | Internal operations. | Historical. |
| Last confirmed timestamp | DateTime | **Future consideration:** Stale-note detection. | Internal operations. | Historical. |
| Expiration timestamp | DateTime | **Future consideration:** Temporary conditions. | Internal operations. | Historical. |

### 5.6 Poll fields

| Data element | Type | Required for | Sensitivity | Freshness |
| --- | --- | --- | --- | --- |
| Poll ID | String | **Future consideration:** Question identity. | Internal operations. | Historical. |
| Poll question | Text | **Future consideration:** Driver feedback collection. | Internal operations. | Static / config. |
| Poll answer choices | List | **Future consideration:** Structured responses. | Internal operations. | Static / config. |
| Poll response | Enum/Text | **Future consideration:** Route insight. | Internal operations or restricted performance. | Same-day / historical. |
| Response driver ID | String | **Future consideration:** Duplicate prevention and accountability. | Restricted performance. | Historical. |
| Response timestamp | DateTime | **Future consideration:** Freshness. | Internal operations. | Historical. |
| Associated route target | String | **Future consideration:** Route knowledge. | Internal operations. | Historical. |

### 5.7 Broadcast and audit fields

| Data element | Type | Required for | Sensitivity | Freshness |
| --- | --- | --- | --- | --- |
| Broadcast ID | String | **Future consideration:** Audit and delivery tracking. | Internal operations. | Historical. |
| Sender user ID | String | **Future consideration:** Permission and accountability. | Internal operations. | Historical. |
| Audience rule | String | **Future consideration:** Dynamic recipient selection. | Internal operations. | Same-day. |
| Recipient IDs | List | **Future consideration:** Delivery and audit. | Internal operations. | Same-day / historical. |
| Message content | Text | **Future consideration:** Broadcast body and audit. | Sensitive operations. | Historical. |
| Preview timestamp | DateTime | **Future consideration:** Confirmation guardrail. | Internal operations. | Historical. |
| Confirmation timestamp | DateTime | **Future consideration:** Approval guardrail. | Internal operations. | Historical. |
| Delivery status | Enum/List | **Future consideration:** Success, failure, skipped. | Internal operations. | Historical. |
| Audit event type | Enum | **Future consideration:** Approvals, imports, resets, config changes. | Internal operations. | Historical. |

## 6. Sensitivity and access model

### 6.1 Suggested visibility by output

| Output | Driver | Dispatcher | Manager | Admin |
| --- | --- | --- | --- | --- |
| Own current pace | **Future consideration:** Yes. | Yes. | Yes. | Yes. |
| Another driver's current pace | **Future consideration:** No. | Yes. | Yes. | Yes. |
| Required pace | **Future consideration:** Own only. | Any assigned driver. | Any driver. | Any driver. |
| At-risk list | **Future consideration:** No. | Yes. | Yes. | Yes. |
| Operations summary | **Future consideration:** No. | Maybe. | Yes. | Yes. |
| Pending route notes | **Future consideration:** Own submissions maybe. | Yes. | Yes. | Yes. |
| Approved route notes | **Future consideration:** Relevant route notes. | Yes. | Yes. | Yes. |
| Safety-sensitive notes | **Future consideration:** Only if needed for route. | Yes. | Yes. | Yes. |
| Driver history | **Future consideration:** Own summary maybe. | Maybe. | Yes. | Yes. |
| Broadcast audit | **Future consideration:** No. | Maybe. | Yes. | Yes. |
| External credentials | **Future consideration:** No. | No. | No. | Admin only outside normal replies. |

### 6.2 Data minimization guidance

- **Future consideration:** Prefer storing route-level operational guidance over personal or customer-specific details.
- **Future consideration:** Avoid storing access codes directly unless the business explicitly approves a secure handling policy.
- **Future consideration:** Keep free-text notes reviewable because they can contain sensitive details.
- **Future consideration:** Use categories and structured fields for analytics before adding large free-text analysis.
- **Future consideration:** Default individual performance data to ephemeral replies or restricted channels.
- **Future consideration:** Store audit entries for approvals, broadcasts, imports, route resets, and manager-visible changes.

## 7. Calculation notes and formulas

### 7.1 Basic pace formulas

**Future consideration:** These formulas require only a small amount of hard data.

| Information | Formula | Minimum data |
| --- | --- | --- |
| Stops remaining | `total_stops - completed_stops` | Total stops, completed stops. |
| Current stops/hour | `completed_stops / elapsed_hours` | Completed stops, start time, current time. |
| Required stops/hour | `stops_remaining / hours_until_target` | Total stops, completed stops, target finish, current time. |
| Projected finish | `now + stops_remaining / pace` | Stops remaining, pace, current time. |
| Minutes behind | `projected_finish - target_finish` | Projected finish, target finish. |
| Last update age | `now - latest_update_time` | Latest update time, current time. |

### 7.2 Confidence rules

**Future consideration:** Every projection should carry a confidence label.

Possible confidence rules:

- **Future consideration:** High confidence when the last update is recent and at least two progress events exist.
- **Future consideration:** Medium confidence when the last update is recent but progress history is thin.
- **Future consideration:** Low confidence when the last update is stale.
- **Future consideration:** Unknown when total stops, completed stops, target time, or start time is missing.

### 7.3 Display rules

**Future consideration:** The bot should be transparent when data is missing.

Examples:

```text
Required pace unavailable: total planned stops are missing for this route.
```

```text
Projection confidence is low because the last update was 48 minutes ago.
```

```text
Current pace excludes break time only if break tracking is enabled.
```

## 8. Build priority by data cost

### 8.1 Low data cost, high value

**Future consideration:** These are best for an MVP.

| Output | Data needed | Why it is cheap |
| --- | --- | --- |
| Stops remaining | Total stops, completed stops. | Two numbers. |
| Current stops/hour | Completed stops, start time, current time. | One stop count and clock data. |
| Required pace | Total stops, completed stops, target time, current time. | Same pace inputs plus config. |
| Projected finish | Stops remaining and pace. | Derived from pace inputs. |
| Stale update list | Last update timestamp and threshold. | One timestamp per active route. |

### 8.2 Medium data cost, high operational value

**Future consideration:** These likely belong in phase 2 or 3.

| Output | Extra data needed | Why it costs more |
| --- | --- | --- |
| Rescue recommendations | Candidate projections, availability, possibly proximity. | Needs multiple route comparisons. |
| Driver stat card | Issues, route notes, confidence labels. | Aggregates several data groups. |
| Route notes | Approval workflow, note targets, visibility. | Requires storage and moderation. |
| Delay reason summary | Structured delay events. | Requires consistent driver/dispatcher input. |
| Broadcast audience | Dynamic filters and confirmation records. | Requires permission and audit design. |

### 8.3 High data cost, later value

**Future consideration:** These should wait until the core data is reliable.

| Output | Extra data needed | Why to defer |
| --- | --- | --- |
| Driver history | Retained historical route data. | Sensitive and needs retention rules. |
| Route difficulty score | History, polls, issue categories, route composition. | Needs enough data to avoid misleading output. |
| Problem location trends | Normalized location keys and repeated reports. | Requires data cleanup and privacy decisions. |
| External integration insights | Stable source exports or APIs. | Adds dependency and operational complexity. |
| OCR-derived updates | Images and parsing confidence. | Unreliable until validated. |

## 9. Command-to-data quick lookup

| Command | Minimum data | Recommended additions | Sensitivity |
| --- | --- | --- | --- |
| `/update-stops` | **Future consideration:** Driver identity, active route, completed stops, timestamp. | Source audit and validation against total stops. | Restricted performance. |
| `/pace` | **Future consideration:** Active route, total stops, completed stops, start time, target finish, current time. | Rolling pace, confidence, break state. | Restricted performance. |
| `/need-pace` | **Future consideration:** Total stops, completed stops, target finish, current time. | Route-specific target override, confidence. | Restricted performance. |
| `/finish-time` | **Future consideration:** Stops remaining, pace basis, current time. | Rolling pace choice and confidence. | Restricted performance. |
| `/route-status` | **Future consideration:** Active route, total stops, completed stops, last update. | Open issues and approved notes count. | Restricted performance. |
| `/driver @driver` | **Future consideration:** Driver identity, route assignment, progress events, target settings. | Issues, notes, suggested action. | Restricted performance. |
| `/at-risk` | **Future consideration:** Route assignments, projections, risk thresholds. | Open issues, stale update flags. | Restricted performance. |
| `/stale-updates` | **Future consideration:** Last progress timestamps, stale threshold. | Route status and dispatcher assignment. | Internal operations. |
| `/rescue-candidates` | **Future consideration:** Projections for many drivers, target finish, capacity rule. | Proximity, eligibility, region. | Restricted performance. |
| `/ops-summary` | **Future consideration:** Active routes, progress events, target settings. | Issues, route-note queue, rescue plans. | Manager/dispatcher restricted. |
| `/submit-route-note` | **Future consideration:** Driver identity, note text, route target, timestamp. | Category, sensitivity flag, attachments. | Sensitive operations. |
| `/route-notes` | **Future consideration:** Route target, approved notes, visibility. | Confidence, freshness, helpful ranking. | Internal/sensitive operations. |
| `/review-notes` | **Future consideration:** Pending notes and reviewer permission. | Duplicate candidates and sensitivity flags. | Sensitive operations. |
| `/approve-note` | **Future consideration:** Note ID, approver identity, approval timestamp. | Edited text, visibility, target attachment. | Sensitive operations. |
| `/reject-note` | **Future consideration:** Note ID, reviewer identity, rejection reason. | Duplicate/merge target. | Sensitive operations. |
| `/broadcast` | **Future consideration:** Sender, audience rule, message, confirmation. | Delivery status, failure reasons. | Internal/sensitive operations. |
| `/eod-report` | **Future consideration:** Route assignments and progress events. | Issues, rescues, notes, polls, history. | Manager/dispatcher restricted. |
| `/problem-locations` | **Future consideration:** Normalized location keys and issue history. | Resolution notes and route associations. | Sensitive operations. |

## 10. Data quality and validation rules

**Future consideration:** Future implementation should validate incoming business data before using it in calculations.

Suggested validation rules:

- **Future consideration:** Completed stops must be an integer greater than or equal to zero.
- **Future consideration:** Completed stops should not exceed total planned stops unless an override or adjusted total exists.
- **Future consideration:** Total planned stops must be a positive integer for pace projections.
- **Future consideration:** Target finish time must be timezone-aware or interpreted using configured station timezone.
- **Future consideration:** Route start time must be before the current timestamp.
- **Future consideration:** Progress event timestamps should not move backward unless explicitly corrected by an authorized user.
- **Future consideration:** Driver must have one active route at a time unless multi-route support is deliberately designed.
- **Future consideration:** Route note text must be non-empty and should have a maximum length.
- **Future consideration:** Sensitive note categories should require elevated approval.
- **Future consideration:** Broadcast audience must resolve to at least one recipient before confirmation.

## 11. Open data decisions before implementation

1. **Future consideration:** Is the first storage layer JSON, SQLite, or another local store?
2. **Future consideration:** Does route start time come from import, driver command, dispatcher command, or first progress update?
3. **Future consideration:** Should pace exclude breaks, lunch, vehicle issues, or station return time?
4. **Future consideration:** Is target finish time global, route-specific, driver-specific, or configurable per day?
5. **Future consideration:** What is the stale update threshold for active drivers?
6. **Future consideration:** What required pace is considered unrealistic and rescue-worthy?
7. **Future consideration:** Which route-note categories may be visible to drivers without dispatcher context?
8. **Future consideration:** Are access instructions allowed in notes, or should notes only say how to obtain access guidance?
9. **Future consideration:** How long are progress events, driver performance data, route notes, poll responses, and broadcast audits retained?
10. **Future consideration:** Who can export, purge, or correct historical operational data?
11. **Future consideration:** Which outputs must be ephemeral/private by default?
12. **Future consideration:** Which calculations need confidence labels to avoid misleading dispatchers or drivers?

## 12. Suggested first implementation slice

**Future consideration:** The lowest-data, highest-value first slice is the pace suite.

Minimum fields:

- **Future consideration:** Discord user ID.
- **Future consideration:** Driver display name.
- **Future consideration:** Service date.
- **Future consideration:** Route ID.
- **Future consideration:** Driver-to-route assignment.
- **Future consideration:** Planned total stops.
- **Future consideration:** Route start time.
- **Future consideration:** Target finish time.
- **Future consideration:** Completed stops.
- **Future consideration:** Progress update timestamp.
- **Future consideration:** Station timezone.

First synthesized outputs:

- **Future consideration:** Stops remaining.
- **Future consideration:** Current stops/hour.
- **Future consideration:** Required stops/hour to target finish.
- **Future consideration:** Projected finish time.
- **Future consideration:** Minutes ahead or behind target.
- **Future consideration:** Last update age.
- **Future consideration:** Simple risk label.

First commands:

- **Future consideration:** `/update-stops`.
- **Future consideration:** `/pace`.
- **Future consideration:** `/need-pace`.
- **Future consideration:** `/driver @driver`.
- **Future consideration:** `/at-risk`.
- **Future consideration:** `/ops-summary`.

This first slice avoids route notes, polls, problem-location analysis, driver history, rescue planning, external integrations, and sensitive access guidance until the core route-state data is reliable.
