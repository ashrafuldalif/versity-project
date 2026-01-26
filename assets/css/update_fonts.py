#!/usr/bin/env python3
import os

files_to_update = [
    'style.css',
    'execuSec.css'
]

replacements = [
    ("'Playfair Display', Georgia, serif", "var(--font-family-display)"),
]

for filename in files_to_update:
    filepath = os.path.join(os.getcwd(), filename)
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        for old, new in replacements:
            content = content.replace(old, new)
        
        if content != original_content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f'✓ Updated {filename}')
        else:
            print(f'- No changes needed in {filename}')
    else:
        print(f'✗ File not found: {filename}')

print('\nUpdate completed!')
