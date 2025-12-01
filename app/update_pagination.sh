#!/bin/bash

# Batch replacement script for adding per_page parameter to all remaining controllers

cd "/Users/applestudio/Desktop/Sefat/untitled folder/OCEI/app"

echo "Updating remaining controllers with per_page parameter..."

# Function to update a single file
update_controller() {
    local file="$1"
    echo "Processing: $file"
    
    # Replace ->paginate(20) or ->paginate(25) with per_page parameter
    perl -i -pe 's/\$applications = \$query->latest\(([^)]*)\)->paginate\(2[05]\);/\$perPage = \$request->get("per_page", 25);\n        \$applications = \$query->latest($1)->paginate(\$perPage)->appends(\$request->except("page"));/g' "$file"
}

# Update all Supervisor controllers
for file in app/Http/Controllers/Permits/Supervisor/*Controller.php; do
    update_controller "$file"
done

# Update all Contractor controllers  
for file in app/Http/Controllers/Permits/Contractor/*Controller.php; do
    update_controller "$file"
done

echo "Done! All controllers updated."
echo "Next: Run this to verify changes:"
echo "grep 'per_page' app/Http/Controllers/Permits/*/.*Controller.php | wc -l"
