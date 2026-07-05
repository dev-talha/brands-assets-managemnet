CREATE TABLE IF NOT EXISTS downloads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    asset_file_id INTEGER,
    asset_group_id INTEGER,
    company_id INTEGER,
    user_id INTEGER,
    ip_address TEXT,
    user_agent TEXT,
    created_at TEXT NOT NULL,
    FOREIGN KEY (asset_file_id) REFERENCES brand_asset_files(id) ON DELETE SET NULL,
    FOREIGN KEY (asset_group_id) REFERENCES brand_asset_groups(id) ON DELETE SET NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
