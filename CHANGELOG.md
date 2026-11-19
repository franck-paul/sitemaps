# Changelog

## 10.1 — 2026-11-07

- Enforces some verification before registering module

## 10.0 — 2026-08-03

- Avoid using magic methods for user preferences and blog settings (2.39+)
- Forgot this one
- Use MetaRecord::strField(), MetaRecord::intField() and MetaRecord::boolField() whenever is possible Fav Icon
- Harmonize usage of App::blogWorkspace()::NS_... and App::userWorkspace()::WS_... (and use short version of constant name for types)

## 9.5 — 2026-03-08

- DA -&gt; DC

## 9.4 — 2025-09-22

- Use backend services rather than static calls

## 9.3 — 2025-09-12

- Replace static call to Ctx by context() — thanks JcDenis for advice

## 9.2 — 2025-09-09

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 9.1 — 2025-09-07

- DC 2.36 compliance

## 9.0 — 2025-09-06

- DC 2.36 compliance

## 8.1 — 2025-06-24

- Add information about some other search engines

## 8.0 — 2025-06-22

- Remove search engines pings and provide some search engine console URLs

## 7.4 — 2025-05-06

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 7.3 — 2025-05-05

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 7.2 — 2025-05-04

- Use direct attribute method rather than extra() when possible form Html/Form/Component

## 7.1 — 2025-03-25

- DC 2.34+

## 7.0 — 2025-03-24

- Use TemplateHelper for template blocks/values DC 2.34+

## 6.2 — 2025-02-26

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 6.1 — 2025-01-11

- Be conservative (will see in future if something more is needed)

## 6.0 — 2025-01-09

- 2.33 compliance

## 5.5 — 2024-08-29

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.4 — 2024-08-23

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.3 — 2023-10-27

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.2 — 2023-10-25

- Simplify template dir management

## 5.1 — 2023-10-25

- Use template()-&gt;appendPath(…) rather than -&gt;setPath(-&gt;getPath(), …)

## 5.0 — 2023-10-24

- Use instanced item constants rather than interface ones
- Use Decimal rather than Input for priority values
- 2.28 compliance

## 4.0 — 2023-08-26

- Dotclear 2.27 compliance, PHP 8.1+

## 3.1.2 — 2023-06-27

- Add local static $init variable

## 3.1.1 — 2023-06-22

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 3.1 — 2023-06-20

- Use dcCore::app()-&gt;adminurl-&gt;redirect() rather than Http::redirect()

## 3.0.3 — 2023-06-12

- Don't do things in frontend if plugin disabled

## 3.0.2 — 2023-05-21

- Variable init missing

## 3.0.1 — 2023-05-21

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 3.0 — 2023-05-21

- Switch to namespace
- Prepare 2.26

## 2.1.1 — 2023-02-09

- Fix redirect URL

## 2.1 — 2022-12-18

- 2.24 compliance

## 2.0 — 2022-11-30

- Dotclear 2.24 compliance

## 1.5.1 — 2022-08-14

- Usual oups :-)

## 1.5 — 2022-08-10

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 1.4.1 — 2021-12-29

- Update default branch in _define.php for alternative repository
- Add alternative repository

## 1.4 — 2021-04-22

- Call of obsolete getMeta() replaced
- Avoid using obosolete fn()
- Eventually Crowdin cope with HTML
- Crowdin do not cope with .html files, too bad!
- Update crowdin sources
- Update Crowdin configuration file
- Crowdin way of life!
- Switch to HTTPS for URLs
- Autres correctifs et améliorations.

## 1.3.3 — 2017-05-18

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 1.3.2 — 2017-05-18

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 1.3.1 — 2017-05-17

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 1.3 — 2017-05-12

- Use getURLFor() rather than getBase() in order to cope with 3rd party plugins (via behavior)
- Using dcPage::cssLoad, dcPage::jsLoad and dcPage::getPF is a better way to load plugin resources

## 1.2 — 2015-02-17

- Remove current blog obsolete search engine if present

## 1.1-RC6 — 2015-02-17

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.
