CREATE TABLE IF NOT EXISTS cdn_access_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    asset_file_id INTEGER NOT NULL,
    ip_address TEXT,
    user_agent TEXT,
    referer TEXT,
    accessed_at TEXT NOT NULL,
    FOREIGN KEY (asset_file_id) REFERENCES brand_asset_files(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_cdn_logs_asset ON cdn_access_logs(asset_file_id);
CREATE INDEX IF NOT EXISTS idx_cdn_logs_time ON cdn_access_logs(accessed_at);
