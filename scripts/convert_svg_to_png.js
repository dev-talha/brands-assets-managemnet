/**
 * SVG → PNG Converter + Brand Logo Asset Seeder
 * 
 * Converts all storage/uploads/{slug}/logo.svg → logo.png
 * Then registers PNG as brand_asset_files (logo) for each company.
 * 
 * Run: node scripts/convert_svg_to_png.js
 */

const sharp = require('sharp');
const path  = require('path');
const fs    = require('fs');

const uploadsDir = path.join(__dirname, '..', 'storage', 'uploads');

async function main() {
    // Find all logo.svg files
    const entries = fs.readdirSync(uploadsDir, { withFileTypes: true });
    const slugDirs = entries.filter(e => e.isDirectory() && e.name !== 'companies');

    let converted = 0, skipped = 0, failed = 0;

    for (const dir of slugDirs) {
        const svgPath = path.join(uploadsDir, dir.name, 'logo.svg');
        const pngPath = path.join(uploadsDir, dir.name, 'logo.png');

        if (!fs.existsSync(svgPath)) {
            skipped++;
            continue;
        }

        if (fs.existsSync(pngPath)) {
            console.log(`⏭  Skip (PNG exists): ${dir.name}`);
            skipped++;
            continue;
        }

        try {
            await sharp(svgPath, { density: 300 })
                .resize(512, 512, {
                    fit: 'contain',
                    background: { r: 255, g: 255, b: 255, alpha: 0 }
                })
                .png({ compressionLevel: 9 })
                .toFile(pngPath);

            console.log(`✅ Converted: ${dir.name}/logo.svg → logo.png`);
            converted++;
        } catch (err) {
            console.error(`❌ Failed: ${dir.name} — ${err.message}`);
            failed++;
        }
    }

    console.log(`\n📊 Done: ${converted} converted, ${skipped} skipped, ${failed} failed`);
}

main().catch(console.error);
