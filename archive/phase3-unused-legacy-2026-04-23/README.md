# Phase 3 Archive (Unused Legacy Files)

Date: 2026-04-23

This folder contains files moved from the project during cleanup phase 3.

- manifest.csv: move log in format STATUS|ORIGINAL|ARCHIVE_PATH
- restore.ps1: script to restore all moved files to original locations

Run restore:
powershell -ExecutionPolicy Bypass -File archive/phase3-unused-legacy-2026-04-23/restore.ps1
