CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT,
    updated_at TEXT NOT NULL
);

-- Default settings
INSERT OR IGNORE INTO settings (key, value, updated_at) VALUES ('app_name', 'Brand CDN Manager', datetime('now'));
INSERT OR IGNORE INTO settings (key, value, updated_at) VALUES ('cdn_base_url', 'http://localhost:8000', datetime('now'));
INSERT OR IGNORE INTO settings (key, value, updated_at) VALUES ('max_upload_mb', '50', datetime('now'));
INSERT OR IGNORE INTO settings (key, value, updated_at) VALUES ('public_brand_pages', '1', datetime('now'));
INSERT OR IGNORE INTO settings (key, value, updated_at) VALUES ('cdn_logging_enabled', '1', datetime('now'));
