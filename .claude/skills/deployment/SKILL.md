---
name: deployment
description: Use whenever discussing releases, pushing to main, CI/CD, or "going live" — explains that this repo auto-deploys to production via FTP on every push to main, with no build step, staging, or rollback safety net.
---

# Deployment — DeAlbidar

## How it works

`.github/workflows/deploy.yml` defines the entire deploy pipeline:

```yaml
on:
  push:
    branches:
      - main
```

On every push to `main`:
1. GitHub Actions checks out the repo (`actions/checkout@v2`).
2. `SamKirkland/FTP-Deploy-Action@4.3.3` syncs the **entire working tree** (minus `.git*` files and a couple of excluded paths) to the FTP root of the live server, using `FTP_SERVER` / `FTP_USERNAME` / `FTP_PASSWORD` GitHub Actions secrets.

There is:
- **No build step** — no compile, bundle, minify, or transform of any file. What's in the repo is byte-for-byte what gets served (aside from PHP execution on the server).
- **No staging environment** — `main` *is* production.
- **No automated tests gating the deploy** — the workflow does nothing but sync files.
- **No rollback mechanism** beyond reverting the commit on `main` and letting the workflow redeploy.

## What this means

Merging to `main` is operationally identical to running a production deploy manually. There is no review gate, no CI check, and no dry run baked into this repo. The only safety net is the discipline described in `.claude/skills/git-workflow/`: everything is fully tested on `localhost` first.

## When you're asked to deploy / release / ship

1. Confirm the change has actually been tested (locally, or on `localhost` by the user) — don't take "looks right" as sufficient given there's no test suite to fall back on.
2. Confirm with the user before merging `localhost` → `main` or pushing directly to `main` — this is a production-affecting, externally-visible action per this environment's safety guidance, not something to do silently even if previously approved for a different change.
3. After merging, the deploy is automatic — there's no separate "now deploy" step to run. Pushing to `main` **is** deploying.
4. If something goes wrong post-deploy, the fix is: revert or fix-forward on `localhost`, test, merge to `main` again. There's no built-in rollback button.

## Common mistakes to avoid

- Treating a merge to `main` as a low-stakes git operation — it is not; it is a production push.
- Assuming there's a review/approval step in GitHub Actions that will catch a bad change — there isn't one configured here.
- Forgetting that `jobs.php`, the cron scripts, and any file path referenced by them all point at the **live** `https://dealbidar.com` domain — testing the Facebook-poster subsystem locally still requires care since some of its trigger scripts hit production URLs directly, not `localhost`.
- Committing local/test credentials or debug output to files that then deploy verbatim to production (e.g., leaving `display_errors`-driven debug output or temporary `var_dump()`s in a file that gets merged to `main`).

## Secrets involved

`FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD` live in the GitHub repository's Actions secrets — they are not and should not be present anywhere in this codebase. If you ever need to reference or change them, that's a GitHub repo settings change, not a code change, and should go through the user.
