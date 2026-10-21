# Architecture

The custom theme renders the homepage and WordPress pages without a page builder or JavaScript framework. Responsive CSS, system fonts, navigation, reduced-motion support and illustrated sample interfaces are theme responsibilities.

The companion plugin provisions editable pages without overwriting an existing slug. It saves the previous homepage options before initial activation. Settings require `manage_options`; WordPress Settings API handles administrative nonces. The inquiry handler uses a nonce, scalar checks, length limits, sanitization, an allowlist for show count, a honeypot and a per-IP transient limit. It sends only to an explicitly configured email and stores no inquiry body in the database. Rate-limit identifiers use salted hashes.

WordPress mail acceptance is not proof of inbox delivery. Authenticated delivery and a received-message test remain necessary. No analytics IDs, SaaS integrations, compliance certifications or customer results are invented.

The website describes a product in development. Its dashboard and dossier figures are illustrative. This repository does not implement the SaaS research application.
