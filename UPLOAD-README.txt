CHULBUL DESIGN — READY-TO-UPLOAD META AUTO-POST DEPLOY
======================================================

This package contains:
- admin/config.php
- .env (private live configuration including the permanent Meta token)

DEPLOY STEPS
------------
1. Take a backup of the current live admin/config.php and .env files.
2. Upload this ZIP inside the website root/public_html directory.
3. Extract it there and allow both included files to overwrite.
4. Do not extract it inside the admin folder; extract from public_html/root.
5. Open the website, blog and admin login to confirm they load.

RESULT
------
- Newly published blog posts auto-share to Facebook and Instagram.
- Instagram uses the actual blog thumbnail, including WEBP thumbnails.
- LinkedIn is not included; it requires a separate LinkedIn API setup.

SECURITY
--------
This ZIP contains private database/API credentials in .env.
Do not share it. Delete the ZIP from Desktop and hosting File Manager after
successful deployment, while keeping your normal secure backups.
