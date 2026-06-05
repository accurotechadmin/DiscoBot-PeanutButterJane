# Delivery Operations Owner Feature Proposal

**Audience:** Business owners, managers, dispatchers, command authors, maintainers, and future implementers evaluating a delivery-operations bot build.
**Status:** Future planning proposal
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../config/bot.php`, `../../config/commands.php`, `../../src/CommandRouter.php`, `../../src/CommandContext.php`, `../../src/Commands/`, `../../tests/`
**Related docs:** [Delivery operations bot blueprint](delivery-operations-bot-blueprint.md), [Delivery operations data requirements index](delivery-operations-data-requirements-index.md), [Examples](README.md), [How to use these docs](../00-start-here/how-to-use-these-docs.md), [Master index](../07-reference/master-index.md)

This document condenses the delivery-operations blueprint and data requirements index into a business-owner approval checklist. It is meant to help an owner quickly see the possible feature packages, formulas, required data, operating notes, and dependencies before deciding what to approve, defer, or cross off.

Current behavior: this repository does not implement delivery-business storage, driver profiles, route imports, pace commands, route notes, polls, approval workflows, rescue planning, analytics, dashboards, or business data integrations. It remains a lightweight DiscordPHP command skeleton. Every feature, formula, data group, workflow, storage option, and integration below is **Future consideration** until implemented in source and tests.

## 1. Owner decision summary

Use this table as the first pass. Check items the business wants, cross off items that are not worth operational complexity, and mark uncertain items for follow-up.

| Decision area | Approve? | Suggested first answer | Owner note |
| --- | --- | --- | --- |
| Route progress and pace tracking | ☐ Yes ☐ No ☐ Later | **Future consideration:** approve first if dispatch needs same-day visibility. | Gives drivers and dispatchers one shared pace picture. |
| Dispatcher at-risk queue | ☐ Yes ☐ No ☐ Later | **Future consideration:** approve with pace tracking. | Turns raw stop counts into an action list. |
| Manager operations summary | ☐ Yes ☐ No ☐ Later | **Future consideration:** approve with MVP if owner wants daily visibility. | Aggregates driver progress into business health. |
| Route notes and approval queue | ☐ Yes ☐ No ☐ Later | **Future consideration:** approve after route state works. | Converts driver knowledge into reusable guidance. |
| Delay, issue, and poll collection | ☐ Yes ☐ No ☐ Later | **Future consideration:** approve after notes. | Explains why routes are late and improves future planning. |
| Rescue recommendations | ☐ Yes ☐ No ☐ Later | **Future consideration:** approve once pace and stale-update data are reliable. | Depends on projected finish and available helper capacity. |
| Guarded broadcasts | ☐ Yes ☐ No ☐ Later | **Future consideration:** approve with confirmation/audit rules. | Helps dispatch communicate with groups without accidental mass messages. |
| End-of-day reporting | ☐ Yes ☐ No ☐ Later | **Future consideration:** approve when history is retained. | Summarizes volume, risks, issues, rescues, and knowledge captured. |
| Historical analytics | ☐ Yes ☐ No ☐ Later | **Future consideration:** defer until daily workflows are stable. | Requires retention, privacy rules, and fair interpretation. |
| External platform integration | ☐ Yes ☐ No ☐ Later | **Future consideration:** defer until manual import proves the workflow. | Reduces manual entry but increases dependency and support risk. |

## 2. Proposed operating ecosystem

**Future consideration:** The strongest version of the application is an ecosystem, not a single command. Route imports or manual route setup create the day's assignments. Driver stop updates feed pace formulas. Pace outputs feed dispatcher risk queues, manager summaries, and rescue planning. Delay and issue reports explain why a route is behind. Route notes and polls convert same-day experience into reviewed knowledge for future drivers. Broadcasts and confirmations close the loop by helping dispatch communicate actions back to the right audience.

Suggested ecosystem layers:

1. **Future consideration:** **Foundation:** driver identity, roles, permissions, route assignments, business-day settings, storage, and validation.
2. **Future consideration:** **Live operations:** stop updates, pace, projected finish, risk levels, stale updates, dispatcher cards, at-risk queues, and manager summaries.
3. **Future consideration:** **Knowledge capture:** route notes, note approvals, note confidence, confirmations, route polls, delay categories, and issue categories.
4. **Future consideration:** **Action support:** rescue recommendations, targeted broadcasts, exception queues, suggested dispatcher actions, and audit trails.
5. **Future consideration:** **Management intelligence:** end-of-day reports, driver trends, route difficulty, problem locations, route knowledge health, and retained history.

## 3. Feature package checklist

### 3.1 Foundation and access package

| Component | Approve? | Data needed | Notes |
| --- | --- | --- | --- |
| Driver identity mapping | ☐ Yes ☐ No ☐ Later | **Future consideration:** Discord user ID, driver display name, optional external driver ID, active/inactive status. | Required before driver-specific pace, permissions, or history can be trusted. |
| Role and permission mapping | ☐ Yes ☐ No ☐ Later | **Future consideration:** Discord roles for driver, dispatcher, manager, admin; app-level capability rules. | Protects performance data, route notes, broadcast actions, and configuration. |
| Daily route assignment | ☐ Yes ☐ No ☐ Later | **Future consideration:** route ID, date, assigned driver, total stops, start time, route status. | The hub that ties all same-day calculations together. |
| Business-day settings | ☐ Yes ☐ No ☐ Later | **Future consideration:** timezone, target finish time, stale-update minutes, risk thresholds. | Keeps route math consistent across drivers and reports. |
| Persistent storage | ☐ Yes ☐ No ☐ Later | **Future consideration:** JSON, SQLite, relational database, or external platform source. | Needed for anything beyond transient command replies. SQLite is the strongest lightweight first option if route history and approval queues are in scope. |

### 3.2 Driver self-service package

| Component | Approve? | Data needed | Notes |
| --- | --- | --- | --- |
| `/update-stops` | ☐ Yes ☐ No ☐ Later | **Future consideration:** driver identity, active route, completed stops, timestamp, updater source. | The simplest driver input that powers pace, risk, and summaries. |
| `/route-status` | ☐ Yes ☐ No ☐ Later | **Future consideration:** route assignment, latest progress event, total stops. | Lets drivers confirm what the bot thinks their current route state is. |
| `/pace` | ☐ Yes ☐ No ☐ Later | **Future consideration:** completed stops, total stops, route start time, target finish time, timezone, current timestamp. | Gives a driver an intuitive personal view of current pace and projected finish. Prefer private or ephemeral replies. |
| `/need-pace` | ☐ Yes ☐ No ☐ Later | **Future consideration:** remaining stops and hours until target finish. | Answers the practical question, “How fast do I need to go from here?” |
| `/done` | ☐ Yes ☐ No ☐ Later | **Future consideration:** route status, completion timestamp, optional final stop count. | Can trigger end-of-route polls and close the day's route state. |

### 3.3 Dispatcher control package

| Component | Approve? | Data needed | Notes |
| --- | --- | --- | --- |
| `/driver @driver` stat card | ☐ Yes ☐ No ☐ Later | **Future consideration:** driver mapping, route state, pace metrics, issues, route notes, freshness. | Gives dispatch one private view before messaging or helping a driver. |
| `/update-driver @driver` | ☐ Yes ☐ No ☐ Later | **Future consideration:** dispatcher permission, selected driver, completed stops, audit event. | Lets dispatch correct or enter progress when drivers cannot. |
| `/at-risk` | ☐ Yes ☐ No ☐ Later | **Future consideration:** projected finish, target finish, risk thresholds, freshness. | Turns projections into a prioritized intervention queue. |
| `/stale-updates` | ☐ Yes ☐ No ☐ Later | **Future consideration:** last update timestamp and stale threshold. | Separates truly late drivers from drivers whose data is simply old. |
| `/rescue-candidates` and `/rescue-plan` | ☐ Yes ☐ No ☐ Later | **Future consideration:** projected finish, remaining stops, candidate capacity, route proximity or policy, audit trail. | Useful only after pace and update freshness are reliable. |
| Dispatcher exception queue | ☐ Yes ☐ No ☐ Later | **Future consideration:** delay reports, issue reports, late projections, disputed notes, failed broadcasts. | Converts scattered events into a queue dispatch can work through. |

### 3.4 Route knowledge package

| Component | Approve? | Data needed | Notes |
| --- | --- | --- | --- |
| `/submit-route-note` | ☐ Yes ☐ No ☐ Later | **Future consideration:** submitter, text, route/location target, category, timestamp, sensitivity. | Captures fresh driver knowledge before it is forgotten. |
| `/review-notes` approval queue | ☐ Yes ☐ No ☐ Later | **Future consideration:** pending notes, reviewer role, approval actions, audit event. | Prevents raw notes from becoming official guidance without review. |
| `/approve-note` / `/reject-note` | ☐ Yes ☐ No ☐ Later | **Future consideration:** note ID, reviewer, decision, reason, edited text if applicable. | Maintains safety, quality, and accountability. |
| `/route-notes` | ☐ Yes ☐ No ☐ Later | **Future consideration:** approved notes, route/location matching, visibility rules. | Surfaces useful guidance to the next driver on the route. |
| Note confirmations and disputes | ☐ Yes ☐ No ☐ Later | **Future consideration:** confirmation count, dispute count, last confirmed date, confirmer role. | Keeps route knowledge fresh and retires stale or incorrect advice. |

### 3.5 Issues, delays, and polls package

| Component | Approve? | Data needed | Notes |
| --- | --- | --- | --- |
| `/delay reason:...` | ☐ Yes ☐ No ☐ Later | **Future consideration:** driver, route, delay category, timestamp, optional note. | Explains low pace without changing the math. |
| `/issue type:...` | ☐ Yes ☐ No ☐ Later | **Future consideration:** issue category, severity, route/location, status, owner. | Supports exception queues and manager follow-up. |
| `/route-poll` | ☐ Yes ☐ No ☐ Later | **Future consideration:** poll question, driver response, route target, timestamp. | Converts subjective route experience into structured signals. |
| Problem location summary | ☐ Yes ☐ No ☐ Later | **Future consideration:** repeated issue/delay/note targets across days. | Helps the owner identify recurring blockers such as access, parking, lockers, or closed businesses. |

### 3.6 Communication package

| Component | Approve? | Data needed | Notes |
| --- | --- | --- | --- |
| Targeted broadcasts | ☐ Yes ☐ No ☐ Later | **Future consideration:** audience rule, message, sender, confirmation status, delivery result. | Lets dispatch contact projected-late drivers, stale-update drivers, or all drivers with guardrails. |
| Broadcast preview and confirmation | ☐ Yes ☐ No ☐ Later | **Future consideration:** sender permission, preview text, intended recipients, confirmation action. | Reduces accidental messages and supports audit review. |
| Two-way check-ins | ☐ Yes ☐ No ☐ Later | **Future consideration:** prompt, response, driver, route, timestamp. | Gives dispatch a structured way to ask, “Do you need help?” or “What is slowing you down?” |

### 3.7 Manager intelligence package

| Component | Approve? | Data needed | Notes |
| --- | --- | --- | --- |
| `/ops-summary` | ☐ Yes ☐ No ☐ Later | **Future consideration:** active drivers, total/completed/remaining stops, risk levels, issues, stale updates. | Gives owner/manager a same-day operating picture. |
| `/exceptions` | ☐ Yes ☐ No ☐ Later | **Future consideration:** issue queue, critical risks, unresolved approvals, failed broadcasts. | Focuses management attention on items needing decisions. |
| `/eod-report` | ☐ Yes ☐ No ☐ Later | **Future consideration:** retained route events, issues, delays, rescues, notes, poll answers. | Summarizes the day and creates a handoff for tomorrow. |
| Driver history summary | ☐ Yes ☐ No ☐ Later | **Future consideration:** retained driver performance events and visibility policy. | Sensitive performance data; approve only with privacy and fairness rules. |
| Route difficulty and problem trends | ☐ Yes ☐ No ☐ Later | **Future consideration:** historical pace, delay, issue, note, and poll data by route/location. | Helps planning, training, and route assignment decisions. |
| Route knowledge health | ☐ Yes ☐ No ☐ Later | **Future consideration:** pending, stale, confirmed, disputed, retired, and high-value notes. | Shows whether the business knowledge base is improving or decaying. |

## 4. Formula reference

These formulas are proposed planning formulas. They need tests, edge-case rules, and data-quality warnings before production use.

| Output | Formula | Required data | Owner note |
| --- | --- | --- | --- |
| Stops remaining | **Future consideration:** `stops_remaining = max(total_planned_stops - completed_stops, 0)` | total planned stops, completed stops. | Base value for pace, risk, rescue, and summaries. |
| Current stops/hour | **Future consideration:** `current_stops_per_hour = completed_stops / active_route_hours_elapsed` | completed stops, route start time, current timestamp. | Explain with delay/issue context so it is not treated as a standalone judgment. |
| Rolling stops/hour | **Future consideration:** `rolling_stops_per_hour = stops_completed_in_window / window_hours` | progress history, window length, current timestamp, interpolation rule. | Better than daily average for detecting a driver who recently slowed down or recovered. |
| Required pace | **Future consideration:** `required_stops_per_hour = stops_remaining / hours_until_target_finish` | stops remaining, target finish time, current timestamp, timezone. | The clearest driver-facing answer for whether the target is still reachable. |
| Projected finish | **Future consideration:** `projected_finish = now + (stops_remaining / pace_basis_stops_per_hour)` | stops remaining, current timestamp, chosen pace basis. | Choose whether the pace basis is average, rolling 30-minute, rolling 60-minute, or blended. |
| Minutes behind or ahead | **Future consideration:** `minutes_behind = projected_finish_time - target_finish_time` | projected finish, target finish. | Positive means behind; negative means ahead if the implementation adopts that convention. |
| Data freshness | **Future consideration:** `minutes_since_update = now - last_progress_update_time` | latest update timestamp, current timestamp, stale threshold. | Prevents dispatch from overreacting to projections based on old data. |
| Rescue need | **Future consideration:** combine remaining stops, projected finish, required pace, risk thresholds, and candidate capacity. | route state, target, candidate data, business policy. | Should remain a recommendation, not an automatic decision. |
| Note confidence | **Future consideration:** combine approval status, confirmations, disputes, age, category, and reviewer role. | route-note metadata and review history. | Helps decide which notes to show first and which need reconfirmation. |
| Route difficulty | **Future consideration:** combine historical pace, delays, issue frequency, route-note volume, and poll difficulty. | retained history by route/location. | Useful later; avoid over-interpreting until enough history exists. |

## 5. Data inventory checklist

| Data group | Approve collecting? | Minimum fields | Sensitivity / handling note |
| --- | --- | --- | --- |
| Driver identity | ☐ Yes ☐ No ☐ Later | **Future consideration:** Discord ID, display name, driver ID, status. | Internal operations; required for nearly every feature. |
| Roles and permissions | ☐ Yes ☐ No ☐ Later | **Future consideration:** role names/IDs, command permissions, approval capabilities. | Internal operations; prevents accidental exposure. |
| Daily route assignment | ☐ Yes ☐ No ☐ Later | **Future consideration:** date, route ID, driver, planned stops, start time, status. | Internal operations; core operational state. |
| Progress events | ☐ Yes ☐ No ☐ Later | **Future consideration:** completed stops, timestamp, route, updater, source. | Restricted performance; powers pace, risk, summaries, rescue. |
| Business-day settings | ☐ Yes ☐ No ☐ Later | **Future consideration:** timezone, target time, thresholds, stale rules. | Internal operations; should be validated. |
| Delay and issue events | ☐ Yes ☐ No ☐ Later | **Future consideration:** category, severity, route/location, note, status. | Internal to sensitive operations depending on content. |
| Route notes | ☐ Yes ☐ No ☐ Later | **Future consideration:** text, category, target, submitter, status, sensitivity, timestamps. | May contain sensitive operations data; approval rules are important. |
| Poll responses | ☐ Yes ☐ No ☐ Later | **Future consideration:** question, answer, route target, respondent, timestamp. | Internal operations; useful for trend analysis. |
| Rescue plans | ☐ Yes ☐ No ☐ Later | **Future consideration:** from driver, to driver, stop count, status, dispatcher, timestamp. | Restricted performance and operational planning. |
| Broadcast events | ☐ Yes ☐ No ☐ Later | **Future consideration:** sender, audience, message, confirmation, delivery results. | Internal operations; requires audit and preview. |
| Audit events | ☐ Yes ☐ No ☐ Later | **Future consideration:** actor, action, target, before/after, timestamp. | Protects sensitive administrative and approval actions. |
| Historical records | ☐ Yes ☐ No ☐ Later | **Future consideration:** retained route, driver, issue, note, poll, and rescue history. | Requires privacy, retention, and access policy. |

## 6. Sensitivity and visibility checklist

| Output or action | Suggested visibility | Owner approval note |
| --- | --- | --- |
| Driver's own pace | **Future consideration:** driver, dispatcher, manager, admin. | Prefer private or ephemeral replies. |
| Another driver's live stats | **Future consideration:** dispatcher, manager, admin. | Treat as restricted performance data. |
| Historical driver stats | **Future consideration:** manager/admin, maybe dispatcher. | Approve only with retention and fairness policy. |
| Route notes | **Future consideration:** drivers, dispatchers, managers, admins when notes are approved and safe. | Sensitive categories may need tighter visibility. |
| Route note approvals/rejections | **Future consideration:** dispatcher, manager, admin. | Keep audit history. |
| Safety-sensitive or access-related notes | **Future consideration:** policy-dependent; usually dispatcher/manager/admin and relevant driver only. | Avoid exposing customer-specific or unsafe details broadly. |
| Manager operations summary | **Future consideration:** manager/admin or configured manager channel. | Aggregated view, but still operationally sensitive. |
| Broadcast preview | **Future consideration:** sender and approver only. | Require confirmation before delivery. |
| Configuration changes | **Future consideration:** manager/admin. | Target time, thresholds, storage, and roles affect all outputs. |

## 7. Suggested approval phases

| Phase | Approve? | Includes | Why this order works |
| --- | --- | --- | --- |
| Phase 1: route state MVP | ☐ Yes ☐ No ☐ Later | **Future consideration:** storage decision, delivery config, driver mapping, route import/manual setup, `/update-stops`, `/pace`, `/need-pace`, `/driver`, `/ops-summary`, `/at-risk`. | Establishes the data and formulas that support every later feature. |
| Phase 2: route knowledge | ☐ Yes ☐ No ☐ Later | **Future consideration:** `/submit-route-note`, `/route-notes`, `/review-notes`, `/approve-note`, `/reject-note`, note categories, note targets, confirmations. | Converts live driver experience into approved reusable guidance. |
| Phase 3: dispatcher co-pilot | ☐ Yes ☐ No ☐ Later | **Future consideration:** stale updates, delay/issues, rescue candidates, rescue plans, exception queues, guarded broadcasts. | Uses pace and note data to recommend action. |
| Phase 4: manager intelligence | ☐ Yes ☐ No ☐ Later | **Future consideration:** `/eod-report`, problem locations, route difficulty, driver history, route knowledge health, trend questions. | Requires retained history and stable daily workflows. |
| Phase 5: integrations | ☐ Yes ☐ No ☐ Later | **Future consideration:** external platform exports, APIs, richer reporting stores, advanced imports. | Best after the owner has validated the workflow manually. |

## 8. Options to avoid at first

These ideas may be valuable later, but they should not be part of the first approval unless the business explicitly wants the added complexity.

- **Future consideration:** Full web dashboard.
- **Future consideration:** GPS tracking.
- **Future consideration:** Payroll or timekeeping workflows.
- **Future consideration:** HR discipline automation.
- **Future consideration:** OCR or screenshot parsing.
- **Future consideration:** AI-only decision making without human review.
- **Future consideration:** Multi-instance infrastructure.
- **Future consideration:** External monitoring stack.
- **Future consideration:** Hosted SaaS deployment story.

## 9. Open owner decisions

Before implementation, answer these questions:

1. **Future consideration:** What is the smallest route-state feature set that would help dispatch tomorrow?
2. **Future consideration:** Should the first storage layer be JSON for a tiny pilot or SQLite for a serious first implementation?
3. **Future consideration:** Will routes be imported by CSV, manually created, or synced from another system?
4. **Future consideration:** What target finish time, timezone, and risk thresholds should the business use?
5. **Future consideration:** Which Discord roles map to driver, dispatcher, manager, and admin capabilities?
6. **Future consideration:** Which command replies must be private or ephemeral by default?
7. **Future consideration:** Which route-note categories are allowed, and who approves them?
8. **Future consideration:** Are access codes or customer-specific details allowed in notes, or should notes only say where to get that information?
9. **Future consideration:** How long should route progress, driver history, route notes, issues, polls, broadcasts, and audit events be retained?
10. **Future consideration:** Which actions require manager confirmation, such as broadcasts, route resets, note policy changes, or history exports?

## 10. Owner sign-off worksheet

| Package | Decision | Required before build | Notes |
| --- | --- | --- | --- |
| Foundation and access | ☐ Approve ☐ Defer ☐ Reject | **Future consideration:** roles, storage choice, target time, route setup method. |  |
| Driver self-service | ☐ Approve ☐ Defer ☐ Reject | **Future consideration:** route assignments and progress events. |  |
| Dispatcher control | ☐ Approve ☐ Defer ☐ Reject | **Future consideration:** dispatcher permissions and reliable route state. |  |
| Route knowledge | ☐ Approve ☐ Defer ☐ Reject | **Future consideration:** note policy, categories, approval roles, sensitivity rules. |  |
| Issues, delays, and polls | ☐ Approve ☐ Defer ☐ Reject | **Future consideration:** category list, visibility rules, retention policy. |  |
| Communication | ☐ Approve ☐ Defer ☐ Reject | **Future consideration:** broadcast audience rules, confirmation requirements, audit log. |  |
| Manager intelligence | ☐ Approve ☐ Defer ☐ Reject | **Future consideration:** retained history and report audience. |  |
| Integrations | ☐ Approve ☐ Defer ☐ Reject | **Future consideration:** stable manual workflow and trusted data source. |  |
