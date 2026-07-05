CREATE TABLE IF NOT EXISTS brand_asset_files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    asset_group_id INTEGER NOT NULL,
    original_filename TEXT NOT NULL,
    stored_filename TEXT NOT NULL,
    storage_path TEXT NOT NULL,
    public_token TEXT NOT NULL UNIQUE,
    extension TEXT NOT NULL,
    mime_type TEXT NOT NULL,
    file_size INTEGER NOT NULL,
    width INTEGER,
    height INTEGER,
    cdn_path TEXT NOT NULL UNIQUE,
    cache_version TEXT NOT NULL,
    is_primary INTEGER NOT NULL DEFAULT 0,
    is_public INTEGER NOT NULL DEFAULT 1,
    is_cdn_enabled INTEGER NOT NULL DEFAULT 1,
    uploaded_by INTEGER,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    deleted_at TEXT,
    FOREIGN KEY (asset_group_id) REFERENCES brand_asset_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_asset_files_group ON brand_asset_files(asset_group_id);
CREATE INDEX IF NOT EXISTS idx_asset_files_token ON brand_asset_files(public_token);
CREATE INDEX IF NOT EXISTS idx_asset_files_ext ON brand_asset_files(extension);
CREATE INDEX IF NOT EXISTS idx_asset_files_cdn ON brand_asset_files(cdn_path);
CREATE INDEX IF NOT EXISTS idx_asset_files_public ON brand_asset_files(is_public, is_cdn_enabled);
