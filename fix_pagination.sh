#!/bin/bash

# Fix malformed pagination code created by the batch update script

cd "/Users/applestudio/Desktop/Sefat/untitled folder/OCEI/app"

echo "Fixing malformed pagination code..."

# Find and fix all files with the inline assignment pattern
find app/Http/Controllers/Permits -name "*Controller.php" -type f -exec perl -i -pe '
    # Fix inline assignment pattern: paginate($perPage = request()->get("per_page", 25))
    # Should be two separate lines
    s/\$applications = (\$query->latest\([^\)]*\))->paginate\(\$perPage = request\(\)->get\("per_page", 25\)\)->append\(request\(\)->except\("page"\)\);/\$perPage = \$request->get('\''per_page'\'', 25);\n        \$applications = $1->paginate(\$perPage)->appends(\$request->except('\''page'\''));/g
' {} \;

echo "Done! All malformed pagination code fixed."
echo ""
echo "Verify with:"
echo "grep -c 'paginate(\$perPage = request' app/Http/Controllers/Permits/*/*.php"
echo "Should return 0 for all files"
