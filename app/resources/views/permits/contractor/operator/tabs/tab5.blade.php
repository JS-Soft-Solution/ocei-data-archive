{{-- Tab 5: Certificate Details & Attachments --}}
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="certificate_number" class="form-label">Certificate Number</label>
        <input type="text" class="form-control @error('certificate_number') is-invalid @enderror"
            id="certificate_number" name="certificate_number"
            value="{{ old('certificate_number', $application->certificate_number) }}">
        @error('certificate_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="issue_date" class="form-label">Issue Date</label>
        <input type="date" class="form-control @error('issue_date') is-invalid @enderror" id="issue_date"
            name="issue_date" value="{{ old('issue_date', $application->issue_date?->format('Y-m-d')) }}">
        @error('issue_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="expiry_date" class="form-label">Expiry Date</label>
        <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date"
            name="expiry_date" value="{{ old('expiry_date', $application->expiry_date?->format('Y-m-d')) }}">
        @error('expiry_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="renewal_period" class="form-label">Renewal Period (Years)</label>
        <input type="number" class="form-control @error('renewal_period') is-invalid @enderror" id="renewal_period"
            name="renewal_period" value="{{ old('renewal_period', $application->renewal_period) }}">
        @error('renewal_period')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="last_renewal_date" class="form-label">Last Renewal Date</label>
        <input type="date" class="form-control @error('last_renewal_date') is-invalid @enderror" id="last_renewal_date"
            name="last_renewal_date"
            value="{{ old('last_renewal_date', $application->last_renewal_date?->format('Y-m-d')) }}">
        @error('last_renewal_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="result" class="form-label">Result</label>
        <input type="text" class="form-control @error('result') is-invalid @enderror" id="result" name="result"
            maxlength="15" value="{{ old('result', $application->result) }}">
        @error('result')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr class="my-4">

{{-- Attachments Section --}}
<h5 class="mb-3">Attachments <span class="text-danger">* At least one required</span></h5>

<div class="mb-3">
    <label for="attachment_upload" class="form-label">Upload New Attachment</label>
    <div class="input-group">
        <input type="file" class="form-control" id="attachment_upload" accept=".pdf,.jpg,.jpeg,.png">
        <select id="attachment_type" class="form-control" style="max-width: 200px;">
            <option value="">Select Type</option>
            <option value="nid_copy">NID Copy</option>
            <option value="old_certificate">Old Certificate</option>
            <option value="photo">Photograph</option>
            <option value="education_doc">Education Document</option>
            <option value="experience_doc">Experience Document</option>
            <option value="other">Other</option>
        </select>
        <button type="button" class="btn btn-primary" onclick="uploadAttachment()">
            <i class="fas fa-upload"></i> Upload
        </button>
    </div>
    <small class="text-muted">Allowed: PDF, JPG, PNG (Max 10MB)</small>
</div>

<div class="table-responsive">
    <table class="table table-bordered" id="attachmentsTable">
        <thead class="table-light">
            <tr>
                <th>Type</th>
                <th>File Name</th>
                <th>Size</th>
                <th>Uploaded By</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($application->attachments as $attachment)
                <tr id="attachment-{{ $attachment->id }}">
                    <td>
                        <span class="badge bg-secondary">{{ $attachment->attachment_type ?? 'General' }}</span>
                    </td>
                    <td>{{ $attachment->original_name }}</td>
                    <td>{{ $attachment->file_size_human }}</td>
                    <td>{{ $attachment->uploadedBy?->name }}</td>
                    <td>{{ $attachment->uploaded_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('attachments.download', $attachment) }}" class="btn btn-sm btn-info"
                            target="_blank">
                            <i class="fas fa-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger"
                            onclick="deleteAttachment({{ $attachment->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr id="noAttachments">
                    <td colspan="6" class="text-center text-muted">No attachments uploaded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        function uploadAttachment() {
            const fileInput = document.getElementById('attachment_upload');
            const attachmentType = document.getElementById('attachment_type').value;
            const file = fileInput.files[0];

            if (!file) {
                alert('Please select a file');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('attachable_type', 'App\\Models\\ExContractorRenewApplication');
            formData.append('attachable_id', '{{ $application->id }}');
            formData.append('attachment_type', attachmentType);

            fetch('{{ route('attachments.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Upload failed: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Upload error: ' + error);
                });
        }

        function deleteAttachment(id) {
            if (!confirm('Delete this attachment?')) return;

            fetch(`/attachments/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('attachment-' + id).remove();
                        const tbody = document.querySelector('#attachmentsTable tbody');
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = '<tr id="noAttachments"><td colspan="6" class="text-center text-muted">No attachments uploaded yet.</td></tr>';
                        }
                    } else {
                        alert('Delete failed: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Delete error: ' + error);
                });
        }
    </script>
@endpush