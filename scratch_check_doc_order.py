import zipfile
import xml.etree.ElementTree as ET

doc_path = r"C:\Users\zum95\Lemon\Dokumentasi\DokuemntasiSekolahPAUD.docx"

with zipfile.ZipFile(doc_path, 'r') as z:
    rels_xml = z.read('word/_rels/document.xml.rels')
    doc_xml = z.read('word/document.xml')

# Parse rels
rels_root = ET.fromstring(rels_xml)
rel_map = {}
for rel in rels_root:
    rel_id = rel.attrib.get('Id')
    target = rel.attrib.get('Target')
    rel_map[rel_id] = target

print("Rel map sample:", list(rel_map.items())[:5])

# Parse doc_xml to find image order
root = ET.fromstring(doc_xml)

namespaces = {
    'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
    'a': 'http://schemas.openxmlformats.org/drawingml/2006/main',
    'r': 'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
}

blips = root.findall('.//a:blip', namespaces)
print(f"Total blip images found in document.xml: {len(blips)}")

image_order = []
for idx, b in enumerate(blips):
    embed_id = b.attrib.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}embed')
    target_img = rel_map.get(embed_id, 'UNKNOWN')
    image_order.append((idx + 1, embed_id, target_img))
    print(f"Drawing #{idx+1}: {embed_id} -> {target_img}")
