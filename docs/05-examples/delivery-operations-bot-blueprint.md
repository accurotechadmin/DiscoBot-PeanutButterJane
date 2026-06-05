# Delivery Operations Bot Blueprint

**Audience:** Business owners, managers, dispatchers, command authors, and maintainers exploring a delivery-business command suite.
**Status:** Future planning example
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../bin/bot.php`, `../../bin/sync-slash-commands.php`, `../../config/bot.php`, `../../config/commands.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/CommandContext.php`, `../../src/Commands/`, `../../tests/`
**Related docs:** [Application at a glance](../00-start-here/application-at-a-glance.md), [How to use these docs](../00-start-here/how-to-use-these-docs.md), [Adding a command](../04-extensibility/adding-a-command.md), [Command registration and aliases](../04-extensibility/command-registration-and-aliases.md), [Interaction paths reference](../03-technical-reference/interaction-paths-reference.md), [Configuration reference](../03-technical-reference/configuration-reference.md), [Examples](README.md), [Component inventory](../07-reference/component-inventory.md)

This document is a product and implementation blueprint for turning the current DiscordPHP Bot Skeleton into a delivery-operations assistant for a package delivery business. It expands the idea of driver self-service, dispatcher visibility, manager intelligence, shared route knowledge, route notes, polls, broadcast workflows, and actionable delivery insights.

Current behavior: the repository is still a lightweight, framework-free PHP CLI DiscordPHP bot skeleton. It currently includes the built-in `ping`, `time`, `settings`, `echo`, and `help` commands, configurable prefix/slash/mention/DM interaction paths, optional daily JSON logs, startup validation, command routing, lifecycle handling, and a basic in-memory rate limiter. It does not currently include delivery-business commands, route storage, driver profiles, dispatcher dashboards, manager analytics, approval workflows, polls, external integrations, databases, queues, web controllers, dashboards, or hosted deployment automation.

Every product feature, command, workflow, storage concept, integration idea, dashboard, alert, approval flow, route-note system, and automation described below is **Future consideration** unless a later source change implements it.

## 1. Product vision

**Future consideration:** Build a Discord-native delivery operations command center that lets drivers answer their own route questions, lets dispatchers see driver context without hunting through chat, lets managers inspect business status at a glance, and lets the whole operation turn daily route knowledge into reusable guidance.

The bot should become a practical assistant for questions like:

- **Future consideration:** Driver: "Am I on pace to finish by 8:30 PM?"
- **Future consideration:** Driver: "What pace do I need for the rest of the day?"
- **Future consideration:** Driver: "Has anyone left notes for this route, apartment complex, locker, business stop, or problem address?"
- **Future consideration:** Driver: "Can I submit a tip so tomorrow's driver does not waste time here?"
- **Future consideration:** Dispatcher: "What is this driver's current route card while I am messaging them?"
- **Future consideration:** Dispatcher: "Who needs rescue, and who can safely give rescue stops?"
- **Future consideration:** Dispatcher: "Which drivers have not updated recently?"
- **Future consideration:** Manager: "How is the whole business doing right now?"
- **Future consideration:** Manager: "What are today's biggest risks, repeated issues, and opportunities?"
- **Future consideration:** Manager: "Which route notes should be approved, retired, or escalated into standing operating guidance?"

The ideal bot is not just a calculator. It is a route memory system, dispatcher co-pilot, team communication hub, exception detector, and lightweight operational knowledge base.

## 2. Design principles

| Principle | Recommendation |
| --- | --- |
| Keep the first version small | **Future consideration:** Start with route state, pace math, permissions, and a few high-value commands before adding complex automation. |
| Prefer explicit interactions | **Future consideration:** Use slash commands, buttons, select menus, confirmations, and context commands instead of passively parsing every message whenever possible. |
| Protect sensitive data | **Future consideration:** Default performance-related replies to ephemeral slash responses or manager/dispatcher-only channels. |
| Make insights actionable | **Future consideration:** Prefer "needs 18.7 stops/hour and a 20-stop rescue" over raw stats alone. |
| Preserve human control | **Future consideration:** Require preview and confirmation for mass messaging, rescue assignments, and manager-sensitive actions. |
| Keep driver workflows fast | **Future consideration:** Drivers should be able to update progress or retrieve guidance with minimal typing. |
| Turn daily learning into reusable knowledge | **Future consideration:** Route notes, stop tips, apartment access guidance, and polls should become approved route knowledge instead of disappearing in chat. |
| Separate current behavior from roadmap | Current behavior must stay traceable to source; unimplemented ideas remain **Future consideration** until built. |

## 3. User roles and operating model

### 3.1 Driver

**Future consideration:** Drivers need fast, mobile-friendly self-service while on route.

Primary needs:

- **Future consideration:** See current stops completed, stops remaining, route pace, projected finish, and required pace.
- **Future consideration:** Submit route progress updates.
- **Future consideration:** Submit route notes, tips, warnings, access information, and delay reasons.
- **Future consideration:** Answer quick route polls after finishing a route or after encountering a known issue.
- **Future consideration:** Get approved notes for a route, route segment, apartment complex, locker, business, or known problem address.
- **Future consideration:** Ask for help or signal that rescue may be needed.

### 3.2 Dispatcher

**Future consideration:** Dispatchers need real-time operating context and fast exception handling.

Primary needs:

- **Future consideration:** View driver stat cards while messaging drivers.
- **Future consideration:** See at-risk drivers, stale updates, rescue candidates, blockers, and open issues.
- **Future consideration:** Approve, edit, tag, or reject driver-submitted route notes.
- **Future consideration:** Attach approved notes to a route, route segment, address, building, apartment complex, locker, business stop, or delivery area.
- **Future consideration:** Send targeted messages to one driver, groups of drivers, or dynamic audiences such as "projected late" or "no update in 30 minutes."
- **Future consideration:** Track rescue plans, dispatcher notes, incident notes, and follow-up tasks.

### 3.3 Manager / owner

**Future consideration:** Managers need business-level insight, accountability, and safe communication tools.

Primary needs:

- **Future consideration:** Query current operating status across all active routes.
- **Future consideration:** See top risks, repeated blockers, dispatcher workload, rescue activity, and route-note trends.
- **Future consideration:** Review operational knowledge submitted by drivers and approved by dispatchers.
- **Future consideration:** Broadcast messages with guardrails and audit logging.
- **Future consideration:** Review route history, driver history, recurring problem locations, and end-of-day summaries.

### 3.4 Admin / maintainer

**Future consideration:** Admins maintain mappings, config, route imports, permissions, and data hygiene.

Primary needs:

- **Future consideration:** Link Discord users to driver profiles.
- **Future consideration:** Import daily route manifests.
- **Future consideration:** Configure target finish times, thresholds, and role permissions.
- **Future consideration:** Archive daily state and retain or purge older data according to business policy.

## 4. Recommended MVP scope

The best first build should prove the business workflow without adding a full platform.

### 4.1 MVP phase 1: route state and pace answers

**Future consideration:** Build the smallest useful delivery bot around daily route state.

Include:

- **Future consideration:** Driver identity mapping.
- **Future consideration:** Daily route import or manual daily route creation.
- **Future consideration:** Configurable target finish time, defaulting to a business-selected value such as 8:30 PM.
- **Future consideration:** Driver progress update command.
- **Future consideration:** Driver pace command.
- **Future consideration:** Driver required-pace command.
- **Future consideration:** Dispatcher driver-stat command.
- **Future consideration:** Dispatcher at-risk command.
- **Future consideration:** Manager operations summary command.
- **Future consideration:** Basic role-based access checks.
- **Future consideration:** Tests for math, command outputs, permissions, and config validation.

### 4.2 MVP phase 2: route notes and dispatcher approvals

**Future consideration:** Add the route knowledge loop after route state exists.

Include:

- **Future consideration:** Drivers submit route notes and tips from Discord.
- **Future consideration:** Dispatchers/managers review submitted notes in an approval queue.
- **Future consideration:** Approved notes attach to routes, addresses, complexes, lockers, businesses, zones, or route segments.
- **Future consideration:** Drivers can query notes before or during a route.
- **Future consideration:** The bot can surface notes automatically when a driver is assigned the same route or portion of a route.
- **Future consideration:** Drivers can answer post-route polls that improve route guidance.
- **Future consideration:** Notes carry freshness, confidence, author, approver, and last-confirmed metadata.

### 4.3 MVP phase 3: dispatcher co-pilot

**Future consideration:** Add dispatcher decision support once the route data and note loop are working.

Include:

- **Future consideration:** Rescue recommendations.
- **Future consideration:** Stale update alerts.
- **Future consideration:** Dynamic audience messaging.
- **Future consideration:** Dispatcher context cards.
- **Future consideration:** Structured delay and issue tracking.
- **Future consideration:** Preview-and-confirm broadcasts.

### 4.4 MVP phase 4: manager intelligence

**Future consideration:** Add richer manager analytics after enough daily data exists.

Include:

- **Future consideration:** End-of-day reports.
- **Future consideration:** Repeated problem-location summaries.
- **Future consideration:** Driver trend views.
- **Future consideration:** Route difficulty trends.
- **Future consideration:** Dispatcher workload and rescue effectiveness summaries.
- **Future consideration:** Knowledge-base health metrics, such as stale route notes awaiting reconfirmation.

## 5. Command suite catalog

The command names below are proposed names only. The current repository does not implement them.

### 5.1 Driver commands

| Command | Priority | Description |
| --- | --- | --- |
| `/pace` | **Future consideration:** MVP | Show the driver's own current route card, pace, projected finish, and target status. |
| `/need-pace` | **Future consideration:** MVP | Show required stops/hour to finish at the configured target time. |
| `/finish-time` | **Future consideration:** MVP | Estimate finish time using current or rolling pace. |
| `/update-stops completed:67` | **Future consideration:** MVP | Record the driver's current completed stop count. |
| `/route-status` | **Future consideration:** MVP | Show total stops, completed stops, remaining stops, last update, and target finish. |
| `/route-notes` | **Future consideration:** Phase 2 | Show approved notes for the driver's current route or route segment. |
| `/submit-route-note` | **Future consideration:** Phase 2 | Submit a note, warning, tip, access code hint, locker instruction, or route-segment observation for review. |
| `/confirm-note note:123 helpful:true` | **Future consideration:** Phase 2 | Confirm whether an existing note is still accurate or useful. |
| `/route-poll` | **Future consideration:** Phase 2 | Answer route-quality, difficulty, access, or timing questions. |
| `/delay reason:apartments` | **Future consideration:** Phase 2 | Report a structured delay reason. |
| `/issue type:access-code` | **Future consideration:** Phase 2 | Report package, access, customer, business, vehicle, or safety issues. |
| `/break start` / `/break end` | **Future consideration:** Later | Track break state if the business wants it inside Discord. |
| `/need-help` | **Future consideration:** Phase 3 | Signal rescue or dispatcher assistance may be needed. |
| `/done` | **Future consideration:** Later | Mark a route completed and trigger optional end-of-route poll questions. |

### 5.2 Dispatcher commands

| Command | Priority | Description |
| --- | --- | --- |
| `/driver @driver` | **Future consideration:** MVP | Show a private dispatcher card for one driver. |
| `/update-driver @driver completed:67` | **Future consideration:** MVP | Let dispatch update a driver's stop count. |
| `/at-risk` | **Future consideration:** MVP | List drivers projected to miss target finish time. |
| `/stale-updates` | **Future consideration:** Phase 3 | List drivers with no update in a configured window. |
| `/rescue-candidates` | **Future consideration:** Phase 3 | Show drivers likely able to take rescue stops. |
| `/rescue-plan from:@driver to:@driver stops:20` | **Future consideration:** Phase 3 | Record or preview a route rescue plan. |
| `/route-notes route:CX-132` | **Future consideration:** Phase 2 | Show approved notes for a route or segment. |
| `/review-notes` | **Future consideration:** Phase 2 | Open the route-note approval queue. |
| `/approve-note note:123` | **Future consideration:** Phase 2 | Approve a driver-submitted note. |
| `/reject-note note:123 reason:duplicate` | **Future consideration:** Phase 2 | Reject a note while preserving audit context. |
| `/attach-note note:123 route:CX-132 segment:A` | **Future consideration:** Phase 2 | Attach note knowledge to an operational route target. |
| `/note @driver text:...` | **Future consideration:** Phase 3 | Add dispatcher-only operational note. |
| `/broadcast audience:projected-late` | **Future consideration:** Phase 3 | Send guarded targeted communications. |
| `/driver-context @driver` | **Future consideration:** Phase 3 | Summarize current context and suggested dispatcher action. |

### 5.3 Manager commands

| Command | Priority | Description |
| --- | --- | --- |
| `/ops-summary` | **Future consideration:** MVP | Show active drivers, total stops, completed stops, remaining stops, on-time projection, and critical risks. |
| `/exceptions` | **Future consideration:** MVP | Show business-critical issues requiring attention. |
| `/eod-report` | **Future consideration:** Phase 4 | Generate end-of-day operational summary. |
| `/route-knowledge-health` | **Future consideration:** Phase 4 | Show pending notes, stale notes, highly confirmed notes, and frequently disputed notes. |
| `/problem-locations` | **Future consideration:** Phase 4 | List addresses or complexes with repeated delay or failure signals. |
| `/driver-history @driver` | **Future consideration:** Phase 4 | Show historical driver trends, if retained and permissioned. |
| `/route-history route:CX-132` | **Future consideration:** Phase 4 | Show route performance and knowledge history. |
| `/broadcast-all` | **Future consideration:** Phase 3 | Send a manager-approved broadcast with preview, confirmation, and audit logging. |
| `/config target-time:20:30` | **Future consideration:** MVP | Change target finish time or business-day settings. |

### 5.4 Admin commands

| Command | Priority | Description |
| --- | --- | --- |
| `/link-driver user:@driver external-id:123` | **Future consideration:** MVP | Map a Discord user to a driver profile. |
| `/unlink-driver user:@driver` | **Future consideration:** MVP | Remove a driver mapping. |
| `/import-routes` | **Future consideration:** MVP | Import daily routes from a CSV or similar file. |
| `/create-route` | **Future consideration:** MVP | Manually create a route assignment. |
| `/reset-day` | **Future consideration:** MVP | Archive or reset daily operational state. |
| `/set-permission` | **Future consideration:** Later | Manage app-level command access if Discord roles are not sufficient. |
| `/storage-status` | **Future consideration:** Later | Show whether route storage is readable and writable. |

## 6. Driver pace and route math

### 6.1 Core route metrics

**Future consideration:** The bot should calculate these metrics from route state:

- **Future consideration:** Total planned stops.
- **Future consideration:** Completed stops.
- **Future consideration:** Remaining stops.
- **Future consideration:** Route start time.
- **Future consideration:** Last update time.
- **Future consideration:** Elapsed active route time.
- **Future consideration:** Current average stops/hour.
- **Future consideration:** Rolling 30-minute stops/hour.
- **Future consideration:** Rolling 60-minute stops/hour.
- **Future consideration:** Target finish time.
- **Future consideration:** Hours remaining until target.
- **Future consideration:** Required pace to hit target.
- **Future consideration:** Projected finish time.
- **Future consideration:** Minutes ahead or behind target.
- **Future consideration:** Data freshness score.
- **Future consideration:** Risk level.

### 6.2 Example driver pace output

**Future consideration:** A driver-facing `/pace` reply could look like this:

```text
Route pace for Jordan
Stops: 67 / 142
Remaining: 75
Current pace: 15.9 stops/hour
Rolling 30-minute pace: 14.2 stops/hour
Target finish: 8:30 PM
Projected finish: 9:08 PM
Needed pace from now: 18.7 stops/hour
Status: Behind by about 38 minutes
Last update: 4 minutes ago
Suggested action: Message dispatch if your next 30 minutes stays below 18 stops/hour.
```

### 6.3 Example dispatcher stat card

**Future consideration:** A dispatcher-facing `/driver @Jordan` reply could look like this:

```text
Driver card: Jordan M.
Route: CX-132
Stops: 74 / 151
Remaining: 77
Current pace: 16.4 stops/hour
Needed pace by 8:30 PM: 19.1 stops/hour
Projected finish: 9:02 PM
Risk: HIGH
Last update: 4 minutes ago
Open issues: 2 access issues, 1 business closed
Route notes: 3 approved, 1 pending, 1 stale
Suggested dispatcher action: Ask whether apartments or access codes are slowing progress.
```

### 6.4 Example manager operations summary

**Future consideration:** A manager-facing `/ops-summary` reply could look like this:

```text
Daily operations summary
Active drivers: 86
Total stops: 12,840
Completed: 8,910
Remaining: 3,930
On-time projection: 78%
At-risk routes: 14
Critical routes: 5
No-update drivers: 7
Open route issues: 23
Pending route notes: 18
Projected latest finish: 9:17 PM
Top blocker today: Apartment access delays
```

## 7. Route notes, tips, polls, and shared route knowledge

This is the most important expansion beyond simple pace tracking.

### 7.1 Route note vision

**Future consideration:** Drivers should be able to submit useful route notes and tips while the information is fresh. Dispatchers and managers should review those submissions, approve the helpful ones, and attach them to the right route entity so future drivers can benefit.

Examples:

- **Future consideration:** "Building 4 has no visible numbers; use the west entrance near the leasing office."
- **Future consideration:** "Amazon locker is inside the lobby; concierge leaves at 6 PM."
- **Future consideration:** "Business closes at 4:30 PM on Fridays."
- **Future consideration:** "Gate code in old notes is wrong; ask dispatch for updated code."
- **Future consideration:** "Route segment after stop 80 is apartment-heavy and pace drops sharply."
- **Future consideration:** "Best parking for this complex is the fire lane near entrance C, but do not block the gate."

### 7.2 Route knowledge objects

**Future consideration:** Route notes should attach to one or more targets:

| Target | Example use |
| --- | --- |
| Route ID | **Future consideration:** Guidance specific to route `CX-132`. |
| Route segment | **Future consideration:** Notes for stops 80-115 or a named section. |
| Address | **Future consideration:** Instructions for one recurring stop. |
| Apartment complex | **Future consideration:** Building access, parking, lockers, leasing office hours. |
| Business | **Future consideration:** Receiving hours, dock location, after-hours rules. |
| Neighborhood / zone | **Future consideration:** Traffic, parking, safety, rural access, road closures. |
| Customer / location tag | **Future consideration:** Non-sensitive operational hints where policy allows. |
| Problem category | **Future consideration:** Access code, dog, locker, construction, business closed, no safe place. |

### 7.3 Route note lifecycle

**Future consideration:** Notes should move through a clear lifecycle:

1. **Future consideration:** Submitted by driver, dispatcher, or manager.
2. **Future consideration:** Stored as pending review.
3. **Future consideration:** Reviewed by dispatcher or manager.
4. **Future consideration:** Edited for clarity and safety if needed.
5. **Future consideration:** Approved, rejected, merged, or marked duplicate.
6. **Future consideration:** Attached to route/location targets.
7. **Future consideration:** Surfaced to future drivers when relevant.
8. **Future consideration:** Confirmed, disputed, or updated by later drivers.
9. **Future consideration:** Retired when stale, inaccurate, unsafe, or no longer useful.

### 7.4 Approval workflow

**Future consideration:** Dispatchers and managers should have a review queue.

Example approval card:

```text
Pending route note #184
Submitted by: Jordan M.
Route: CX-132
Target: Oak Ridge Apartments / Segment B
Text: "Use west entrance near leasing office; building numbers are hidden from main road."
Suggested tags: apartment, access, navigation, time-saver
Confidence: New submission
Actions: [Approve] [Edit] [Attach] [Reject] [Duplicate] [Ask driver]
```

Approval choices:

- **Future consideration:** Approve as written.
- **Future consideration:** Approve with edits.
- **Future consideration:** Attach to a different target.
- **Future consideration:** Merge with an existing note.
- **Future consideration:** Reject with a reason.
- **Future consideration:** Ask the submitting driver for clarification.
- **Future consideration:** Mark as safety-sensitive or manager-only.

### 7.5 Note quality metadata

**Future consideration:** Each approved note should carry metadata that helps future users trust it:

- **Future consideration:** Note ID.
- **Future consideration:** Author.
- **Future consideration:** Approver.
- **Future consideration:** Created date.
- **Future consideration:** Approved date.
- **Future consideration:** Last confirmed date.
- **Future consideration:** Number of helpful votes.
- **Future consideration:** Number of disputes.
- **Future consideration:** Target route/address/segment tags.
- **Future consideration:** Category tags.
- **Future consideration:** Freshness status.
- **Future consideration:** Safety/privacy classification.
- **Future consideration:** Visibility level.

### 7.6 Note categories

**Future consideration:** Suggested route-note categories:

| Category | Purpose |
| --- | --- |
| Access | Gate codes, entry points, concierge, package room rules where policy allows. |
| Parking | Safe and legal parking tips. |
| Navigation | Hard-to-find buildings, hidden entrances, confusing numbering. |
| Timing | Business hours, school traffic, rush-hour issues, concierge availability. |
| Safety | Dogs, lighting, unsafe entry, road hazards, delivery policy cautions. |
| Apartments | Building order, lockers, leasing office, mailroom, elevator notes. |
| Business | Dock, receiving hours, suite access, after-hours process. |
| Rural | Long driveways, GPS errors, road conditions, cell service gaps. |
| Locker | Locker location, hours, package-room quirks. |
| Customer instruction | Operationally useful customer notes if allowed by business policy. |
| Route strategy | Sequence or pacing guidance for portions of the route. |
| Temporary condition | Construction, detour, event traffic, temporary closure. |

### 7.7 Surfacing notes to drivers

**Future consideration:** Notes can be surfaced in multiple ways:

- **Future consideration:** Driver runs `/route-notes` before starting.
- **Future consideration:** Driver runs `/route-notes segment:apartments` mid-route.
- **Future consideration:** Bot posts a morning route brief in DM after assignment.
- **Future consideration:** Bot highlights high-confidence notes only to avoid overload.
- **Future consideration:** Bot shows notes relevant to the driver's next known segment.
- **Future consideration:** Bot asks whether old notes are still accurate.

Example route brief:

```text
Route knowledge for CX-132
3 high-confidence notes:
1. Oak Ridge Apartments: Use west entrance near leasing office.
2. Business park: Several receiving docks close at 4:30 PM.
3. Segment B: Apartment-heavy; expected pace drop after stop 80.

1 note needs confirmation today:
- Locker location may have moved. Confirm after delivery? [Still accurate] [Changed] [Not sure]
```

### 7.8 Poll questions

**Future consideration:** Polls can collect structured knowledge from drivers without requiring long typed notes.

Example post-route poll questions:

- **Future consideration:** Was the route harder than expected?
- **Future consideration:** Which route segment slowed you down most?
- **Future consideration:** Were any existing notes inaccurate?
- **Future consideration:** Did apartments, businesses, traffic, access, or route order cause the biggest delay?
- **Future consideration:** Did you need rescue? If yes, why?
- **Future consideration:** Did any stop need a new note?
- **Future consideration:** Should this route be flagged for dispatcher review?

Example route-specific poll:

```text
CX-132 follow-up
What caused the biggest slowdown today?
[Apartments] [Traffic] [Access codes] [Business closures] [Package sorting] [Other]
```

### 7.9 Note confidence and freshness

**Future consideration:** Notes should become more or less trusted over time.

Possible statuses:

- **Future consideration:** Pending review.
- **Future consideration:** Approved.
- **Future consideration:** High confidence.
- **Future consideration:** Needs confirmation.
- **Future consideration:** Disputed.
- **Future consideration:** Stale.
- **Future consideration:** Retired.
- **Future consideration:** Safety-sensitive.
- **Future consideration:** Manager-only.

Suggested rules:

- **Future consideration:** A note becomes high confidence after multiple helpful confirmations.
- **Future consideration:** A note becomes stale after a configurable number of days without confirmation.
- **Future consideration:** A note becomes disputed if multiple drivers mark it inaccurate.
- **Future consideration:** Temporary notes automatically expire unless renewed.
- **Future consideration:** Access-sensitive notes may require manager approval.

### 7.10 Knowledge base health

**Future consideration:** Managers should be able to inspect the health of operational knowledge.

Possible `/route-knowledge-health` output:

```text
Route knowledge health
Approved notes: 642
Pending review: 18
Stale notes needing confirmation: 73
Disputed notes: 9
Most noted route: CX-132
Most common category: Apartment access
Top contributor this week: Sam R. with 11 useful notes
Recommended action: Review 9 disputed notes before tomorrow's dispatch.
```

## 8. Dispatcher co-pilot workflows

### 8.1 Driver context while messaging

**Future consideration:** When a dispatcher intentionally invokes context for a driver, the bot should show a compact stat card and suggested action.

Possible patterns:

- **Future consideration:** Slash command: `/driver @driver`.
- **Future consideration:** Message context command: "Show driver context."
- **Future consideration:** Mention-assisted command in a dispatcher channel.
- **Future consideration:** Button in an at-risk list to open the driver card.

The safer first implementation is an explicit command rather than reading every message.

### 8.2 Suggested dispatcher messages

**Future consideration:** The bot can suggest message templates without sending automatically.

Examples:

```text
Suggested check-in:
"Jordan, you are trending about 32 minutes late. Are apartments or access issues slowing you down? Do you need rescue?"
```

```text
Suggested encouragement:
"You are back on pace. Keep current rhythm and update again in 30 minutes."
```

### 8.3 Rescue recommendation logic

**Future consideration:** Rescue logic can identify drivers who need help and drivers who can help.

Inputs:

- **Future consideration:** Projected finish time.
- **Future consideration:** Stops remaining.
- **Future consideration:** Current and rolling pace.
- **Future consideration:** Distance or route proximity if available.
- **Future consideration:** Route difficulty.
- **Future consideration:** Driver rescue eligibility.
- **Future consideration:** Dispatcher region assignment.
- **Future consideration:** Confidence based on last update freshness.

Example output:

```text
Rescue recommendation
Driver needing help: Jordan M.
Projected finish: 9:02 PM
Recommended rescue: 22 stops
Best candidates:
1. Alex R. — projected 6:42 PM, can take 28 stops
2. Sam K. — projected 7:05 PM, can take 18 stops
3. Nina P. — projected 7:18 PM, can take 12 stops
Suggested action: Send Alex for 20-25 stops if location is practical.
```

### 8.4 Exception queues

**Future consideration:** Dispatchers should have exception queues rather than scanning raw chat.

Queues:

- **Future consideration:** Projected late.
- **Future consideration:** No update in threshold window.
- **Future consideration:** Need rescue.
- **Future consideration:** Open driver issues.
- **Future consideration:** Pending route notes.
- **Future consideration:** Disputed route notes.
- **Future consideration:** Safety-sensitive reports.
- **Future consideration:** High-priority manager escalations.

## 9. Manager intelligence workflows

### 9.1 Operations summary

**Future consideration:** The manager should get high-level business status without asking multiple dispatchers.

Metrics:

- **Future consideration:** Active drivers.
- **Future consideration:** Total stops.
- **Future consideration:** Completed stops.
- **Future consideration:** Remaining stops.
- **Future consideration:** On-time projection.
- **Future consideration:** Routes at risk.
- **Future consideration:** Critical late routes.
- **Future consideration:** No-update drivers.
- **Future consideration:** Open issues.
- **Future consideration:** Pending route notes.
- **Future consideration:** Rescue plans in progress.
- **Future consideration:** Top blocker of the day.

### 9.2 End-of-day report

**Future consideration:** At the end of the day, the bot can summarize operations.

Possible sections:

- **Future consideration:** Daily volume.
- **Future consideration:** On-time performance.
- **Future consideration:** Latest finish time.
- **Future consideration:** Average stops/hour.
- **Future consideration:** Route difficulty outliers.
- **Future consideration:** Drivers needing follow-up.
- **Future consideration:** Rescues assigned and completed.
- **Future consideration:** Top delay categories.
- **Future consideration:** New route notes submitted.
- **Future consideration:** Notes approved, rejected, disputed, or needing confirmation.
- **Future consideration:** Repeated problem addresses or complexes.

### 9.3 Business trend questions

**Future consideration:** Manager query examples:

- **Future consideration:** "Which routes are repeatedly late?"
- **Future consideration:** "Which apartment complexes create the most delay?"
- **Future consideration:** "Which route notes were most helpful this week?"
- **Future consideration:** "Which drivers frequently need rescue?"
- **Future consideration:** "Which drivers often finish early and can help?"
- **Future consideration:** "Which dispatchers handled the most exceptions today?"
- **Future consideration:** "What recurring issue should be solved operationally, not just chatted about?"

## 10. Communication and broadcast workflows

### 10.1 Broadcast types

**Future consideration:** Communication should be targeted, permissioned, and auditable.

Broadcast audiences:

- **Future consideration:** All drivers.
- **Future consideration:** All dispatchers.
- **Future consideration:** All managers.
- **Future consideration:** Drivers in one region.
- **Future consideration:** Drivers projected late.
- **Future consideration:** Drivers with no update in 30 minutes.
- **Future consideration:** Drivers under a certain pace threshold.
- **Future consideration:** Drivers assigned to apartment-heavy routes.
- **Future consideration:** Drivers with open issues.
- **Future consideration:** Drivers who need to confirm stale route notes.

### 10.2 Broadcast safety

**Future consideration:** Every mass communication should support:

- **Future consideration:** Preview before send.
- **Future consideration:** Recipient count.
- **Future consideration:** Audience explanation.
- **Future consideration:** Required confirmation.
- **Future consideration:** Permission check.
- **Future consideration:** Audit log entry.
- **Future consideration:** Rate-limit-aware sending.
- **Future consideration:** Failure summary.

Example preview:

```text
Broadcast preview
Audience: Projected late drivers
Recipients: 14
Message:
"If apartments or access issues are slowing you down, reply with /delay reason:apartments or message dispatch now."

Actions: [Send] [Edit] [Cancel]
```

### 10.3 Two-way check-ins

**Future consideration:** Instead of sending only one-way reminders, the bot can ask drivers to respond with structured buttons or commands.

Examples:

- **Future consideration:** "Are you delayed?" with `Yes`, `No`, `Need rescue`.
- **Future consideration:** "What is your blocker?" with `Traffic`, `Apartments`, `Access`, `Vehicle`, `Sorting`, `Other`.
- **Future consideration:** "Is this route note still accurate?" with `Yes`, `No`, `Not sure`.

## 11. Data and storage model

The current skeleton does not implement a database or business state storage. Any storage model below is **Future consideration**.

### 11.1 Minimal entities

| Entity | Purpose |
| --- | --- |
| Driver profile | **Future consideration:** Map Discord users to driver identities and status. |
| Dispatcher profile | **Future consideration:** Map dispatchers to roles, regions, and permissions. |
| Daily route | **Future consideration:** Store date, route ID, driver assignment, target finish, stop totals, and status. |
| Route progress event | **Future consideration:** Store each progress update and its source. |
| Route issue | **Future consideration:** Store access, vehicle, package, customer, business, safety, or delay issues. |
| Route note | **Future consideration:** Store submitted, approved, disputed, stale, or retired operational guidance. |
| Route note target | **Future consideration:** Attach notes to route IDs, segments, addresses, complexes, or categories. |
| Poll response | **Future consideration:** Store structured feedback from drivers. |
| Rescue plan | **Future consideration:** Store planned or completed rescue assignments. |
| Broadcast event | **Future consideration:** Store communication audience, sender, confirmation, and result. |
| Audit event | **Future consideration:** Store sensitive administrative and manager actions. |

### 11.2 Storage options

| Option | Fit |
| --- | --- |
| JSON files | **Future consideration:** Good for a very small MVP, simple local inspection, and daily state; weaker for concurrency and long-term reporting. |
| SQLite | **Future consideration:** Strong first persistent option for a framework-free CLI bot, daily route state, route notes, and moderate reporting. |
| MySQL/PostgreSQL | **Future consideration:** Better for multi-user dashboards, richer integrations, and larger reporting; higher operational complexity. |
| External platform API | **Future consideration:** Useful after stable core workflows exist and the delivery data source is reliable. |

Recommendation: **Future consideration:** choose SQLite for a serious first implementation if daily route history, approval queues, route notes, and manager reports are in scope.

### 11.3 Data retention and privacy

**Future consideration:** Before storing operational data, define retention rules.

Questions:

- **Future consideration:** How long should route progress be retained?
- **Future consideration:** How long should driver performance history be retained?
- **Future consideration:** Who can see driver history?
- **Future consideration:** Should route notes include customer-specific information, or only location/process guidance?
- **Future consideration:** Which notes require manager approval?
- **Future consideration:** Which data should be exportable?
- **Future consideration:** Which data should be purged after a season, contract period, or employment change?

## 12. Permissions and visibility

### 12.1 Access matrix

**Future consideration:** Use Discord roles and app-level checks to protect commands.

| Capability | Driver | Dispatcher | Manager | Admin |
| --- | --- | --- | --- | --- |
| View own pace | Yes | Yes | Yes | Yes |
| View another driver's current stats | No | Yes | Yes | Yes |
| View historical driver stats | No | Maybe | Yes | Yes |
| Update own stops | Yes | Yes | Yes | Yes |
| Update another driver's stops | No | Yes | Yes | Yes |
| Submit route notes | Yes | Yes | Yes | Yes |
| Approve route notes | No | Yes | Yes | Yes |
| Reject route notes | No | Yes | Yes | Yes |
| View safety-sensitive notes | Maybe | Yes | Yes | Yes |
| Broadcast to all drivers | No | Maybe | Yes | Yes |
| Change target finish time | No | No | Yes | Yes |
| Import routes | No | Maybe | Yes | Yes |
| Reset or archive day | No | No | Maybe | Yes |

### 12.2 Reply visibility

**Future consideration:** Suggested defaults:

- **Future consideration:** Driver `/pace`: ephemeral/private.
- **Future consideration:** Driver `/route-notes`: ephemeral/private unless a note is safe for public channel discussion.
- **Future consideration:** Dispatcher `/driver`: ephemeral/private.
- **Future consideration:** Dispatcher `/at-risk`: dispatcher channel or ephemeral.
- **Future consideration:** Manager `/ops-summary`: manager channel or ephemeral.
- **Future consideration:** Broadcast previews: ephemeral/private to sender.
- **Future consideration:** Broadcast deliveries: DM or configured channel depending on policy.

## 13. Configuration ideas

The current config file does not contain these delivery-specific settings. Each setting below is **Future consideration**.

| Setting | Purpose |
| --- | --- |
| `DELIVERY_TARGET_FINISH_TIME` | Default route finish target, such as `20:30`. |
| `DELIVERY_STATION_TIMEZONE` | Business timezone for route math. |
| `DELIVERY_STALE_UPDATE_MINUTES` | Minutes before a driver is considered stale. |
| `DELIVERY_RISK_YELLOW_MINUTES` | Minutes late before yellow risk. |
| `DELIVERY_RISK_ORANGE_MINUTES` | Minutes late before orange risk. |
| `DELIVERY_RISK_RED_MINUTES` | Minutes late before red risk. |
| `DELIVERY_ROUTE_NOTE_STALE_DAYS` | Days before approved notes require reconfirmation. |
| `DELIVERY_ROUTE_NOTE_APPROVAL_ROLE` | Discord role allowed to approve notes. |
| `DELIVERY_MANAGER_ROLE` | Discord role allowed to run manager commands. |
| `DELIVERY_DISPATCHER_ROLE` | Discord role allowed to run dispatcher commands. |
| `DELIVERY_DRIVER_ROLE` | Discord role used for driver commands. |
| `DELIVERY_STORAGE_PATH` | Local JSON or SQLite storage path if implemented. |
| `DELIVERY_BROADCAST_CONFIRMATION_REQUIRED` | Guardrail for mass communication. |

## 14. Alerting and risk logic

### 14.1 Risk levels

**Future consideration:** Risk scoring can categorize routes.

| Risk | Example meaning |
| --- | --- |
| Green | **Future consideration:** Projected on time with recent update. |
| Yellow | **Future consideration:** Slightly behind or update is aging. |
| Orange | **Future consideration:** Meaningfully late without intervention. |
| Red | **Future consideration:** Critical late projection or rescue likely needed. |
| Unknown | **Future consideration:** No route data or stale update makes projection unreliable. |

### 14.2 Alert types

**Future consideration:** Useful alert categories:

- **Future consideration:** Driver projected after target finish.
- **Future consideration:** Driver needs unrealistic pace to recover.
- **Future consideration:** Driver has no recent update.
- **Future consideration:** Driver reported vehicle issue.
- **Future consideration:** Driver reported repeated access issue.
- **Future consideration:** Route note disputed by multiple drivers.
- **Future consideration:** Important route note needs reconfirmation today.
- **Future consideration:** Business-critical broadcast failed for some recipients.

## 15. Integrations and imports

The repository currently has no delivery-platform integration. Each integration below is **Future consideration**.

### 15.1 Manual CSV import

**Future consideration:** Best first import format.

Possible columns:

- **Future consideration:** Date.
- **Future consideration:** Route ID.
- **Future consideration:** Driver name.
- **Future consideration:** Driver Discord user ID.
- **Future consideration:** Dispatcher.
- **Future consideration:** Total stops.
- **Future consideration:** Planned start time.
- **Future consideration:** Target finish time.
- **Future consideration:** Region.
- **Future consideration:** Route type.

### 15.2 External delivery platform exports

**Future consideration:** Later integrations could ingest stable exports, reports, APIs, or files from a delivery-provider system if legally and technically allowed.

### 15.3 Screenshot or OCR ingestion

**Future consideration:** Screenshot parsing could reduce manual updates, but it should be delayed until core workflows are valuable because OCR can be unreliable and may raise privacy concerns.

## 16. Implementation path in this skeleton

### 16.1 Fit with current command architecture

**Future consideration:** Delivery features should be added as small command classes under `src/Commands/`, registered in `config/commands.php`, validated in startup config, covered by offline tests, and documented in the command/user/reference docs.

Potential command classes:

- **Future consideration:** `PaceCommand`.
- **Future consideration:** `NeedPaceCommand`.
- **Future consideration:** `UpdateStopsCommand`.
- **Future consideration:** `DriverStatsCommand`.
- **Future consideration:** `AtRiskCommand`.
- **Future consideration:** `OpsSummaryCommand`.
- **Future consideration:** `SubmitRouteNoteCommand`.
- **Future consideration:** `ReviewRouteNotesCommand`.
- **Future consideration:** `ApproveRouteNoteCommand`.
- **Future consideration:** `RouteNotesCommand`.
- **Future consideration:** `BroadcastCommand`.

### 16.2 Suggested service classes

**Future consideration:** Keep business logic out of command classes by adding focused services.

Potential services:

- **Future consideration:** `DeliveryRouteRepository`.
- **Future consideration:** `DriverRepository`.
- **Future consideration:** `RouteProgressRepository`.
- **Future consideration:** `RouteNoteRepository`.
- **Future consideration:** `RouteNoteApprovalService`.
- **Future consideration:** `RouteMetricsCalculator`.
- **Future consideration:** `RiskClassifier`.
- **Future consideration:** `RescuePlanner`.
- **Future consideration:** `BroadcastAudienceResolver`.
- **Future consideration:** `DeliveryPermissionChecker`.
- **Future consideration:** `DeliveryClock` or timezone-aware date helper.

### 16.3 Testing strategy

**Future consideration:** Preserve the repository's offline testing style.

Test targets:

- **Future consideration:** Pace math with fixed clocks.
- **Future consideration:** Required pace calculation when target time is near, passed, or missing.
- **Future consideration:** Risk classification thresholds.
- **Future consideration:** Driver self-service command output.
- **Future consideration:** Dispatcher command permission checks.
- **Future consideration:** Manager command permission checks.
- **Future consideration:** Route note submission and approval lifecycle.
- **Future consideration:** Note freshness and confirmation rules.
- **Future consideration:** Broadcast preview and confirmation logic without live Discord API calls.
- **Future consideration:** CSV import validation.
- **Future consideration:** Storage read/write behavior using temporary directories or test databases.

## 17. Suggested build order

### Step 1: Decide storage

**Future consideration:** Choose JSON files for a quick prototype or SQLite for a stronger first real implementation.

### Step 2: Add delivery config and validation

**Future consideration:** Add target finish time, stale-update thresholds, risk thresholds, storage path, and role settings.

### Step 3: Add driver and route state

**Future consideration:** Implement route assignment, progress updates, and retrieval.

### Step 4: Add pace commands

**Future consideration:** Implement `/update-stops`, `/pace`, `/need-pace`, and `/driver`.

### Step 5: Add summary commands

**Future consideration:** Implement `/at-risk` and `/ops-summary`.

### Step 6: Add route notes

**Future consideration:** Implement `/submit-route-note`, `/review-notes`, `/approve-note`, `/reject-note`, and `/route-notes`.

### Step 7: Add polls and note freshness

**Future consideration:** Implement route-specific questions, note confirmations, disputes, and stale note queues.

### Step 8: Add rescue and exception workflows

**Future consideration:** Implement rescue recommendations, stale updates, and dispatcher exception queues.

### Step 9: Add guarded communication

**Future consideration:** Implement targeted broadcasts with preview, confirmation, rate-limit awareness, and audit logs.

### Step 10: Add historical intelligence

**Future consideration:** Implement end-of-day reports, problem-location summaries, and trend views.

## 18. Features to include, defer, or avoid first

### 18.1 Include first

- **Future consideration:** Driver pace lookup.
- **Future consideration:** Required pace to target finish.
- **Future consideration:** Driver progress updates.
- **Future consideration:** Dispatcher driver stat card.
- **Future consideration:** Manager operations summary.
- **Future consideration:** At-risk route list.
- **Future consideration:** Configurable target finish time.
- **Future consideration:** Role-based command access.
- **Future consideration:** Route import or manual route setup.
- **Future consideration:** Basic persistence.

### 18.2 Include soon after

- **Future consideration:** Driver route-note submission.
- **Future consideration:** Dispatcher/manager route-note approval queue.
- **Future consideration:** Approved route notes surfaced to future drivers.
- **Future consideration:** Route polls and note confirmations.
- **Future consideration:** Stale update alerts.
- **Future consideration:** Delay and issue reporting.
- **Future consideration:** Rescue recommendations.
- **Future consideration:** Broadcast preview and confirmation.

### 18.3 Include later

- **Future consideration:** Historical analytics.
- **Future consideration:** Driver trends.
- **Future consideration:** Problem location trends.
- **Future consideration:** Route difficulty scoring.
- **Future consideration:** External delivery platform integration.
- **Future consideration:** Advanced dispatcher workload summaries.
- **Future consideration:** Driver coaching insights.

### 18.4 Avoid initially

- **Future consideration:** Full web dashboard.
- **Future consideration:** GPS tracking.
- **Future consideration:** Payroll or timekeeping workflows.
- **Future consideration:** HR discipline automation.
- **Future consideration:** OCR or screenshot parsing.
- **Future consideration:** AI-only decision making without human review.
- **Future consideration:** Multi-instance infrastructure.
- **Future consideration:** External monitoring stack.
- **Future consideration:** Hosted SaaS deployment story.

## 19. Example day-in-the-life workflow

### Morning setup

1. **Future consideration:** Admin imports the daily route manifest.
2. **Future consideration:** Bot validates driver mappings and flags unmapped drivers.
3. **Future consideration:** Drivers receive route assignments and approved high-confidence route notes.
4. **Future consideration:** Drivers see any stale notes that need confirmation today.

### During route

1. **Future consideration:** Driver updates stops with `/update-stops`.
2. **Future consideration:** Driver checks `/pace` and `/need-pace`.
3. **Future consideration:** Driver submits `/submit-route-note` after discovering a useful route tip.
4. **Future consideration:** Dispatcher uses `/at-risk` to identify late routes.
5. **Future consideration:** Dispatcher opens `/driver @driver` while messaging a driver.
6. **Future consideration:** Dispatcher approves route notes that are safe and useful.
7. **Future consideration:** Manager checks `/ops-summary` and `/exceptions`.

### Late day

1. **Future consideration:** Bot highlights routes that need rescue to hit target finish.
2. **Future consideration:** Dispatcher sends a confirmed targeted broadcast to projected-late drivers.
3. **Future consideration:** Drivers answer issue or note confirmation polls.
4. **Future consideration:** Manager reviews unresolved exceptions.

### End of day

1. **Future consideration:** Drivers mark routes done.
2. **Future consideration:** Bot asks route-quality poll questions.
3. **Future consideration:** Dispatchers finish pending route-note approvals.
4. **Future consideration:** Manager runs `/eod-report`.
5. **Future consideration:** Stale, disputed, and high-value notes are queued for review before the next operating day.

## 20. Open decisions before implementation

Before building, answer these product decisions:

1. **Future consideration:** Should the first persistence layer be JSON or SQLite?
2. **Future consideration:** Are route assignments imported by CSV, manually created, or synced from another system?
3. **Future consideration:** What is the default target finish time and timezone?
4. **Future consideration:** What roles exist in Discord, and which commands can each role run?
5. **Future consideration:** Should driver pace replies be slash-only and ephemeral by default?
6. **Future consideration:** Should dispatchers approve all route notes, or only notes in sensitive categories?
7. **Future consideration:** What route-note categories are allowed?
8. **Future consideration:** Are access codes allowed in route notes, or should notes only say where to get the code?
9. **Future consideration:** How long should operational history be retained?
10. **Future consideration:** Who can view driver history and trend data?
11. **Future consideration:** Should broadcasts go to DMs, channels, or both?
12. **Future consideration:** Which actions require manager confirmation?
13. **Future consideration:** Which features must work entirely offline in tests?
14. **Future consideration:** What is the smallest feature set that would help dispatch tomorrow?

## 21. Candidate first implementation request

If this blueprint becomes the next development task, a focused first request could be:

> Implement the first delivery-operations MVP: add delivery config for target finish time and route storage, a lightweight route repository, driver route progress state, `/update-stops`, `/pace`, `/need-pace`, dispatcher `/driver`, manager `/ops-summary`, and `/at-risk`, with offline tests and synchronized documentation.

A second request could then add:

> Implement route knowledge workflows: `/submit-route-note`, `/route-notes`, dispatcher `/review-notes`, `/approve-note`, `/reject-note`, note categories, note targets, freshness metadata, and route-note tests and docs.

Both requests would be meaningful behavior changes. They would need source, tests, config, `.env.example`, command registry updates, user/operator docs, extensibility docs if command patterns change, command indexes, component inventory updates, and possibly an ADR if the storage choice changes the repository's architecture boundary.
