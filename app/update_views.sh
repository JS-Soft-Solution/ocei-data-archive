#!/bin/bash

# Script to add per-page selector component to all list view files

cd "/Users/applestudio/Desktop/Sefat/untitled folder/OCEI/app"

echo "Adding per-page selector to view files..."

# Define the per-page selector block to insert
PER_PAGE_BLOCK='{{-- Per-Page Selector --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            @include('"'"'components.per-page-selector'"'"')
            <div>
                <span class="text-muted">Total: <strong>{{ $applications->total() }}</strong> applications</span>
            </div>
        </div>'

# Function to check if file already has per-page selector
has_per_page_selector() {
    grep -q "components.per-page-selector" "$1"
}

# Function to add per-page selector before table
add_per_page_selector() {
    local file="$1"
    
    if has_per_page_selector "$file"; then
        echo "  ✓ Already has selector: $file"
        return
    fi
    
    # Find line with <div class="table-responsive"> and insert before it
    perl -i -pe 'if (/\s*<div class="table-responsive">/) { print "        {{-- Per-Page Selector --}}\n        <div class=\"d-flex justify-content-between align-items-center mb-3\">\n            \@include('"'"'components.per-page-selector'"'"')\n            <div>\n                <span class=\"text-muted\">Total: <strong>{{ \$applications->total() }}</strong> applications</span>\n            </div>\n        </div>\n\n"; }' "$file"
    
    echo "  ✅ Added selector to: $file"
}

# Process all list views
echo ""
echo "Processing Electrician views..."
for file in resources/views/permits/electrician/*/{index,pending,rejected,approved}.blade.php; do
    [ -f "$file" ] && add_per_page_selector "$file"
done

echo ""
echo "Processing Supervisor views..."
for file in resources/views/permits/supervisor/*/{index,pending,rejected,approved}.blade.php; do
    [ -f "$file" ] && add_per_page_selector "$file"
done

echo ""
echo "Processing Contractor views..."
for file in resources/views/permits/contractor/*/{index,pending,rejected,approved}.blade.php; do
    [ -f "$file" ] && add_per_page_selector "$file"
done

echo ""
echo "✅ Done! Per-page selector added to all view files."
echo ""
echo "Verify with: grep -r 'per-page-selector' resources/views/permits/ | wc -l"
