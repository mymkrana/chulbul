<?php
// Legacy compatibility entry point. Apache blocks direct access to this root
// file; keeping a tiny wrapper also prevents a second copy of the AI workflow.
require __DIR__ . '/admin/import-save.php';
