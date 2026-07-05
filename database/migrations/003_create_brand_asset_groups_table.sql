CREATE TABLE IF NOT EXISTS brand_asset_groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    company_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    slug TEXT NOT NULL,
    asset_type TEXT NOT NULL,
    theme TEXT NOT NULL DEFAULT 'default',
    description TEXT,
    tags TEXT,
    is_public INTEGER NOT NULL DEFAULT 1,
    status TEXT NOT NULL DEFAULT 'approved',
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    deleted_at TEXT,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE(company_id, theme, slug)
);

CREATE INDEX IF NOT EXISTS idx_asset_groups_company ON brand_asset_groups(company_id);
CREATE INDEX IF NOT EXISTS idx_asset_groups_slug ON brand_asset_groups(slug);
CREATE INDEX IF NOT EXISTS idx_asset_groups_type ON brand_asset_groups(asset_type);
CREATE INDEX IF NOT EXISTS idx_asset_groups_theme ON brand_asset_groups(theme);
CREATE INDEX IF NOT EXISTS idx_asset_groups_status ON brand_asset_groups(status);
