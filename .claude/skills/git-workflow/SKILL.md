---
name: git-workflow
description: Use whenever making, committing, or planning any change in this repo — defines the required localhost/main branching model, when merges to main are allowed, and what to assume by default if the user doesn't specify a branch.
---

# Git Workflow — DeAlbidar

This project uses a strict two-branch model. This is not a suggestion — it's the required workflow for this repository.

## The two permanent branches

- **`localhost`** — where all development happens. Every feature, bug fix, refactor, test, and experiment starts and stays here until fully verified.
- **`main`** — production. Pushing to `main` triggers an automatic FTP deploy to the live server (see `.claude/skills/deployment/`). Nothing lands here that isn't finished and tested.

## Rules

1. **Default assumption: work happens on `localhost`.** If the user asks for a change and doesn't specify a branch, assume they mean `localhost`. Only touch `main` if the user explicitly says so.
2. **Never commit directly to `main`.** All changes flow: work on `localhost` → test locally → merge `localhost` → `main` only when confirmed working.
3. **Test before merge.** This project has no automated test suite (see `.claude/skills/testing/`), so "tested" means the user (or you, running the dev server) has manually verified the change works — not just that it doesn't throw a PHP error at a glance.
4. **`main` = production-ready code only.** Anything merged there is live within seconds via GitHub Actions. Treat every merge to `main` as equivalent to "deploy this right now."

## What this means for Claude Code sessions

- Before starting work, check `git branch --show-current`. If it's not `localhost` and the user hasn't asked for `main`-branch work, either switch to `localhost` or ask.
- Before creating a commit, confirm you're not on `main` unless the user explicitly asked for a `main` commit (e.g., "merge localhost into main now, I've tested it").
- When the user says something like "merge to main" or "deploy this," treat it as the trigger to fast-forward or merge `localhost` → `main` — and confirm with them first, since that action is effectively "push to production" (see the Executing Actions guidance in your system prompt: pushing to `main` is a shared, hard-to-reverse, production-affecting action).
- Don't suggest additional long-lived branches (feature branches, release branches, etc.) unless the user asks — this project intentionally keeps a two-branch model for simplicity.

## Common mistakes to avoid

- Committing a work-in-progress or experimental change straight to `main` "because it's a small fix" — small fixes still deploy instantly and un-reviewed.
- Assuming `main` is the default branch to work from just because it's the repository's conventional default branch name — for this project, `localhost` is the actual day-to-day branch.
- Force-pushing or rewriting history on either branch without explicit user confirmation — both are high-impact actions per the standard git safety rules, and `main` doubly so since it's also the deploy trigger.

## Example

```
User: "Fix the broken nav link on the About page"
→ Assume this happens on `localhost`. Make the fix, let the user verify it locally,
  and do NOT merge to main unless they say the fix is confirmed and ready to ship.
```
