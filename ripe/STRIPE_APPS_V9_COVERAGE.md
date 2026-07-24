# Stripe Apps SDK v9 coverage

Ripe translates the public Stripe Apps SDK v9 visual language onto Filament 5.
It does not ship Stripe code, images, fonts, logos, or React components.
Filament keeps ownership of markup, accessibility, state, and behaviour; Ripe
owns the visual translation through Filament's public `fi-*` CSS hooks.

Baseline reviewed on **23 July 2026**:

- [Stripe Apps UI components](https://docs.stripe.com/stripe-apps/components)
- [Stripe Apps styling and tokens](https://docs.stripe.com/stripe-apps/style)
- [Stripe Apps design patterns](https://docs.stripe.com/stripe-apps/patterns)
- [Button component, SDK v9](https://docs.stripe.com/stripe-apps/components/button?app-sdk-version=9)

## Foundations

The live SDK v9 examples were rendered in a real browser and measured before
the Ripe tokens were defined. The core light-mode measurements are:

| Primitive          | SDK v9 value                      | Ripe token or rule       |
| ------------------ | --------------------------------- | ------------------------ |
| Primary action     | `#533afd`                         | `--ripe-primary`         |
| Destructive action | `#e61947`                         | `--ripe-danger`          |
| Interactive text   | `#5469d4`                         | `--ripe-interactive`     |
| Heading text       | `#1a2c44`                         | `--ripe-ink`             |
| Body text          | `#273951`                         | `--ripe-body`            |
| Secondary text     | `#50617a`                         | `--ripe-muted`           |
| Keyline            | `#d4dee9`                         | `--ripe-line`            |
| Subtle divider     | `#ecf1f6`                         | `--ripe-hair`            |
| Control radius     | `6px`                             | `--ripe-radius`          |
| Medium control     | `28px`, `14/20`, semibold actions | button and field rules   |
| Data row           | `44px`, `14/20`                   | table row and cell rules |
| Table heading      | `36px`, `12/20`, bold             | table header rules       |
| Spacing scale      | `2, 4, 8, 16, 24, 32, 48px`       | `--ripe-space-*`         |

Ripe also provides an accessible dark-mode interpretation. Stripe Apps SDK v9
does not expose a public dark palette with equivalent measured values, so dark
mode preserves the same hierarchy and component states instead of claiming an
exact Stripe palette.

## Component translation

### Views and layout

| Stripe Apps component | Filament 5 surface                | Ripe coverage                                                   |
| --------------------- | --------------------------------- | --------------------------------------------------------------- |
| ContextView           | Panel shell, sidebar, topbar      | Shell surfaces, keylines, active navigation, actions            |
| FullPageView          | Resource and custom pages         | Page canvas, headers, breadcrumbs, actions, content modules     |
| OnboardingView        | Wizard, callout, empty state      | Task progress, pending/error feedback, primary action hierarchy |
| SettingsView          | Form and section layouts          | Field groups, descriptions, controls, save actions              |
| SignInView            | Simple/auth layout                | Auth card, heading, fields, footer links, actions               |
| Box                   | Grid, section, schema containers  | Surfaces, spacing, borders, radii, elevation                    |
| DetailPage            | View record page and infolist     | Breadcrumbs, page actions, sections, property values            |
| Divider               | Section, table, dropdown dividers | Neutral keylines and subtle dividers                            |
| OverviewPage          | List page and dashboard           | Header actions, cards, tables, empty state, pagination          |

### Navigation and actions

| Stripe Apps component | Filament 5 surface             | Ripe coverage                                                                                                         |
| --------------------- | ------------------------------ | --------------------------------------------------------------------------------------------------------------------- |
| Button                | Button and action              | Primary, secondary, destructive, outlined, disabled, pending, focus, five Filament sizes mapped to three visual tiers |
| ButtonGroup           | Action group                   | Joined keylines, shared radius, compact overflow trigger                                                              |
| Link                  | Link action and rich text link | Primary/secondary emphasis, hover, keyboard focus                                                                     |
| Menu                  | Dropdown                       | Trigger, panel, groups, selected, disabled, destructive items                                                         |
| Tabs                  | Tabs and page sub-navigation   | SDK-style underline tabs, selected bar, disabled and vertical variants                                                |

### Content and feedback

| Stripe Apps component | Filament 5 surface                 | Ripe coverage                                                             |
| --------------------- | ---------------------------------- | ------------------------------------------------------------------------- |
| Accordion             | Collapsible section                | Header hover, disclosure control, divided content                         |
| Badge                 | Badge                              | Neutral, info, positive, negative, warning palettes and compact sizes     |
| Banner                | Callout                            | Neutral and semantic keylines, icons, actions, dismiss control            |
| Chip                  | Tags input and multi-select badges | Pill shape, label/action separation, removable state                      |
| DataTable             | Filament table                     | Header, sorting, filters, selection, row actions, pagination, empty state |
| FocusView             | Modal and slide-over               | Overlay, window, header, footer actions, close and focus hierarchy        |
| Icon                  | Filament icon                      | Semantic colour inheritance, control and badge placement                  |
| Img                   | Avatar, image entry, image column  | Keyline, shape, object presentation                                       |
| Inline                | Text, link, badge, affix           | Typography, status colour, wrapping and truncation inheritance            |
| List                  | Repeatable entry, dropdown list    | Rows, media, title, secondary text, value/action region                   |
| PropertyList          | Infolist and key-value entry       | Label/value hierarchy and compact orientation                             |
| Spinner               | Loading indicator                  | Interactive colour, overlay, reduced-motion handling                      |
| Table                 | Filament table and table repeater  | Head, body, footer, cells, dividers, density                              |
| TaskList              | Wizard and onboarding sections     | Upcoming, active, completed, and inaccessible-step visual language        |
| Toast                 | Filament notification              | Neutral and semantic backgrounds, title/body/actions, elevation           |
| Tooltip               | Tippy tooltip used by Filament     | Compact dark surface, 12/16 type, radius and elevation                    |

### Forms

| Stripe Apps component | Filament 5 surface            | Ripe coverage                                                          |
| --------------------- | ----------------------------- | ---------------------------------------------------------------------- |
| Checkbox              | Checkbox and checkbox list    | 14px control, checked, indeterminate, invalid, disabled, focus         |
| CurrencyField         | Numeric text input with affix | Field sizing, prefix/suffix keyline, numeric content states            |
| DateField             | Date/time picker              | Field, trigger, calendar, selected/today/disabled states               |
| DateRangePicker       | Paired date/time pickers      | Shared field and calendar styling; Filament has no native range state  |
| FormFieldGroup        | Fieldset, section, grid       | Label, description, grouping surface, disabled inheritance             |
| Radio                 | Radio and radio group         | 14px control, checked, invalid, disabled, focus                        |
| SearchField           | Search and global search      | Prefix icon, compact field, result panel, empty state                  |
| Select                | Native and custom select      | Trigger, menu, options, search, selected, multi-value chips            |
| Switch                | Toggle                        | 34x18 track, 14px thumb, on/off, disabled, focus                       |
| TextArea              | Textarea                      | Medium field, invalid, disabled, read-only, resize behaviour           |
| TextField             | Text input                    | Small/medium/large visual tiers, invalid, disabled, read-only, affixes |

### Charts

| Stripe Apps component | Filament 5 surface   | Ripe coverage                                                           |
| --------------------- | -------------------- | ----------------------------------------------------------------------- |
| BarChart              | Chart widget         | Module surface, grid/text tokens, interactive series colour             |
| LineChart             | Chart widget         | Module surface, grid/text tokens, line and fill colour                  |
| Sparkline             | Stats overview chart | Interactive line and translucent fill                                   |
| MeterChart            | Custom chart widget  | Module framing and semantic palette; Filament has no native meter chart |

Ripe also styles Filament controls that have no direct Stripe Apps component,
such as rich editors, file uploads, repeaters, and builders, so mixed forms keep
one coherent visual language.

## UX pattern translation

Visual fidelity is only one part of the system. These Stripe Apps patterns map
to native Filament flows and remain application-level decisions:

| Stripe Apps pattern    | Filament implementation rule                                                                                            |
| ---------------------- | ----------------------------------------------------------------------------------------------------------------------- |
| Onboarding and sign in | Use the simple/auth layout, keep setup brief, show only required fields, and make the next action primary.              |
| Settings sign in       | Put the connection state and sign-in action in one settings section with explicit success or error feedback.            |
| Demo content           | Mark sample data clearly and keep its removal or replacement action visible.                                            |
| Additional context     | Move long explanations to a dedicated page, modal, or section instead of crowding a transaction action.                 |
| External redirect      | Explain why the new tab opens, use a secondary action, and provide a clear route back to the panel.                     |
| Sign out               | Keep the action in the user menu, separate it from routine navigation, and confirm only when unsaved work is at risk.   |
| Back navigation        | Use breadcrumbs or a quiet link action; do not style Back as the primary action.                                        |
| Action hierarchy       | Show one primary action per page or focused flow. Use secondary for routine actions and destructive only for data loss. |
| Communicating state    | Pair semantic colour with text and an icon; never rely on colour alone.                                                 |
| Empty state            | State what is absent, why it matters, and offer one useful next action when one exists.                                 |
| Loading and waiting    | Preserve layout, show a spinner or loading section, disable duplicate submission, and retain a useful status label.     |
| Progress stepping      | Use the wizard header and footer actions, keep the current step obvious, and make the final action primary.             |
| Lists                  | Use Filament tables for sortable/actionable data and repeatable or infolist entries for compact read-only collections.  |

Button and action copy should use sentence case, describe the action directly,
avoid decorative punctuation, and prefer a verb plus its object. Common short
actions such as **Save**, **Cancel**, and **Delete** can stay concise.

## Interaction contract

Every interactive Ripe surface covers hover, keyboard focus, active/pressed,
disabled, invalid, loading/pending, selected, and destructive states where the
Filament component exposes that state. Motion is short and functional; the
theme suppresses animation when `prefers-reduced-motion: reduce` is active.

## Verification evidence

On **23 July 2026**, Ripe was installed as a clean Composer path dependency in
a Laravel 12 consumer running Filament 5.7.1. A Playwright browser pass covered
login and a component showcase at 1440px desktop and 390px mobile widths in
light and dark mode. The pass exercised the panel shell, sidebar, topbar,
buttons, badges, tabs, dropdown, modal, text/select/checkbox/radio/toggle
controls, table, callout, and empty state. The final pass produced no browser
console or page errors.

The repository verification scripts also pass clean package installation and
Filament boot checks. The browser fixture is temporary test evidence rather
than package runtime code.

## Maintenance rule

The source CSS is split by responsibility under `resources/css/`. When Stripe
Apps or Filament changes, update this matrix, the relevant module, and the
committed `resources/dist/theme.css` together. A Filament major upgrade needs a
new real-panel verification before the compatibility claim changes.

Stripe is a trademark of Stripe, Inc. Ripe is an independent theme and is not
affiliated with, endorsed by, or distributed by Stripe.
