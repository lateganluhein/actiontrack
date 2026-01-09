@extends('layouts.app')

@section('title', 'People')

@section('content')
<div class="page-header">
    <h1 class="page-title">People</h1>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="openBroadcastModal()">Broadcast Email</button>
        <a href="{{ route('people.create') }}" class="btn btn-primary">+ Add Person</a>
    </div>
</div>

<!-- Search -->
<div class="filters">
    <form method="GET" action="{{ route('people.index') }}" class="filter-form">
        <div class="filter-group">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by name, email, or company..."
                   class="filter-input">
        </div>
        <button type="submit" class="btn btn-secondary">Search</button>
        @if(request('search'))
            <a href="{{ route('people.index') }}" class="btn btn-link">Clear</a>
        @endif
    </form>
</div>

<!-- People Table -->
@if($people->isNotEmpty())
<div class="activity-table-container">
    <table class="activity-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                </th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($people as $person)
            <tr>
                <td>
                    <input type="checkbox"
                           class="person-checkbox"
                           value="{{ $person->id }}"
                           data-name="{{ $person->full_name }}"
                           data-email="{{ $person->email_primary }}"
                           {{ !$person->email_primary ? 'disabled' : '' }}>
                </td>
                <td>
                    <a href="{{ route('people.show', $person) }}" class="person-name">
                        {{ $person->full_name }}
                    </a>
                </td>
                <td>
                    @if($person->email_primary)
                        <a href="mailto:{{ $person->email_primary }}">{{ $person->email_primary }}</a>
                    @else
                        <span class="text-muted">No email</span>
                    @endif
                </td>
                <td>
                    @if($person->phone_primary)
                        <a href="tel:{{ $person->phone_primary }}">{{ $person->phone_primary }}</a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $person->company ?? '-' }}</td>
                <td class="actions-cell">
                    <a href="{{ route('people.show', $person) }}" class="btn btn-sm btn-view">View</a>
                    <a href="{{ route('people.edit', $person) }}" class="btn btn-sm btn-edit">Edit</a>
                    <form method="POST" action="{{ route('people.destroy', $person) }}"
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete {{ $person->full_name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-container">
    {{ $people->withQueryString()->links() }}
</div>
@else
<div class="empty-state">
    <span class="empty-icon">👥</span>
    <h3>No people found</h3>
    @if(request('search'))
        <p>Try a different search term.</p>
        <a href="{{ route('people.index') }}" class="btn btn-secondary">Clear Search</a>
    @else
        <p>Add your contacts to start tracking activities.</p>
        <a href="{{ route('people.create') }}" class="btn btn-primary">Add Person</a>
    @endif
</div>
@endif

<!-- Broadcast Modal -->
<div id="broadcastModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeBroadcastModal()"></div>
    <div class="modal-content">
        <button class="modal-close" onclick="closeBroadcastModal()">&times;</button>

        <h2>Broadcast Email</h2>
        <p class="modal-subtitle">Send an email to selected people</p>

        <div class="selected-count">
            <span id="selectedCount">0</span> people selected
        </div>

        <div id="selectedPeopleList" class="selected-people-list"></div>

        <form id="broadcastForm" class="broadcast-form">
            <div class="form-group">
                <label for="broadcastSubject" class="form-label required">Subject</label>
                <input type="text"
                       id="broadcastSubject"
                       class="form-input"
                       placeholder="Email subject..."
                       required>
            </div>
            <div class="form-group">
                <label for="broadcastMessage" class="form-label required">Message</label>
                <textarea id="broadcastMessage"
                          class="form-textarea"
                          rows="6"
                          placeholder="Type your message..."
                          required></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="sendBroadcastBtn">Send Broadcast</button>
                <button type="button" class="btn btn-secondary" onclick="closeBroadcastModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.person-checkbox:not(:disabled)');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.person-checkbox:checked');
    document.getElementById('selectedCount').textContent = checked.length;
}

function openBroadcastModal() {
    const checked = document.querySelectorAll('.person-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select at least one person to send a broadcast.');
        return;
    }

    const list = document.getElementById('selectedPeopleList');
    list.innerHTML = Array.from(checked).map(cb =>
        `<span class="selected-person-tag">${cb.dataset.name}</span>`
    ).join('');

    document.getElementById('broadcastModal').style.display = 'flex';
}

function closeBroadcastModal() {
    document.getElementById('broadcastModal').style.display = 'none';
    document.getElementById('broadcastForm').reset();
}

document.querySelectorAll('.person-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

document.getElementById('broadcastForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const checked = document.querySelectorAll('.person-checkbox:checked');
    const peopleIds = Array.from(checked).map(cb => cb.value);

    const btn = document.getElementById('sendBroadcastBtn');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    fetch('{{ route("people.broadcast") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            people: peopleIds,
            subject: document.getElementById('broadcastSubject').value,
            message: document.getElementById('broadcastMessage').value
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            closeBroadcastModal();
            // Uncheck all
            document.querySelectorAll('.person-checkbox:checked').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateSelectedCount();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send broadcast');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Send Broadcast';
    });
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBroadcastModal();
    }
});
</script>
@endpush
@endsection
