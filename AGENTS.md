# AGENTS.md

## Repository layout

The repository contains a WordPress plugin that provides CLDR data.

- data/ — CLDR data, downloaded by get-cldr-files.sh and processed by prune-cldr-files.php

## Updating the CLDR version

- Determine the current version based on the data folder name.
- Always fetch the latest CLDR version from <https://github.com/unicode-org/cldr-json/tags>. Don't use the gh tool.
- Create a branch named feature/update-cldr-to-version-NEW_CLDR_VERSION_HERE.
- Create a pull request for the changes.
- Increment the plugin version.

## Running tests

- `phpunit`

## Creating Pull Requests

- Create PRs as draft.
- When updating the CLDR data version, always remove the previous data version.
