{{-- Per-Page Selector Component --}}
<div class="d-flex align-items-center">
    <label class="me-2 mb-0">Show:</label>
    <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
        <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
        <option value="999999" {{ request('per_page') == 999999 ? 'selected' : '' }}>All</option>
    </select>
    <span class="ms-2 text-muted">entries</span>
</div>

<script>
    function changePerPage(value) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', value);
        window.location.href = url.toString();
    }
</script>