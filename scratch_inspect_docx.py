import os
import zipfile
import docx

doc_path = r"C:\Users\zum95\Lemon\Dokumentasi\DokuemntasiSekolahPAUD.docx"
extract_dir = r"C:\Users\zum95\Lemon\Dokumentasi\extracted_images"
os.makedirs(extract_dir, exist_ok=True)

# 1. Unzip media
with zipfile.ZipFile(doc_path, 'r') as z:
    for f in z.namelist():
        if f.startswith('word/media/'):
            filename = os.path.basename(f)
            if filename:
                out_path = os.path.join(extract_dir, filename)
                with open(out_path, 'wb') as out_f:
                    out_f.write(z.read(f))
                print(f"Extracted: {filename} ({os.path.getsize(out_path)} bytes)")

# 2. Check document paragraphs & runs
doc = docx.Document(doc_path)
print(f"\nTotal paragraphs: {len(doc.paragraphs)}")
for idx, p in enumerate(doc.paragraphs):
    text = p.text.strip()
    # Check if paragraph has images
    has_img = False
    xml = p._p.xml
    if '<a:blip' in xml or '<w:drawing' in xml or 'graphic' in xml:
        has_img = True
    print(f"[{idx}] (has_img={has_img}): '{text}'")

print(f"\nTotal tables: {len(doc.tables)}")
