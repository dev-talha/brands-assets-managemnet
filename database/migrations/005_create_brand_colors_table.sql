CREATE TABLE IF NOT EXISTS brand_colors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    company_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    hex_code TEXT NOT NULL,
    rgb_value TEXT,
    hsl_value TEXT,
    cmyk_value TEXT,
    color_type TEXT NOT NULL DEFAULT 'primary',
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_brand_colors_company ON brand_colors(company_id);
