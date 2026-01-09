<!-- Activity Detail Modal -->
<div id="activityModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeActivityModal()"></div>
    <div class="modal-content">
        <button class="modal-close" onclick="closeActivityModal()">&times;</button>

        <div id="modalLoading" class="modal-loading">
            Loading...
        </div>

        <div id="modalBody" style="display: none;">
            <div class="modal-header">
                <h2 id="modalActivityName"></h2>
                <div class="modal-badges">
                    <span id="modalStatusBadge" class="status-badge"></span>
                    <span id="modalUrgencyBadge"></span>
                </div>
            </div>

            <div class="modal-grid">
                <div class="modal-section">
                    <h4>Lead</h4>
                    <p id="modalLead">-</p>
                </div>
                <div class="modal-section">
                    <h4>Participants</h4>
                    <p id="modalParties">-</p>
                </div>
            </div>

            <div class="modal-grid">
                <div class="modal-section">
                    <h4>Start Date</h4>
                    <p id="modalStartDate">-</p>
                </div>
                <div class="modal-section">
                    <h4>Due Date</h4>
                    <p id="modalDueDate">-</p>
                </div>
            </div>

            <div class="modal-section" id="modalLogicSection" style="display: none;">
                <h4>Background / Logic</h4>
                <div id="modalLogic" class="modal-text-content"></div>
            </div>

            <div class="modal-section" id="modalNextStepSection" style="display: none;">
                <h4>Next Step</h4>
                <div id="modalNextStep" class="modal-text-content"></div>
            </div>

            <div class="modal-footer">
                <div class="modal-meta">
                    <small>Created: <span id="modalCreatedAt">-</span></small>
                    <small>Updated: <span id="modalUpdatedAt">-</span></small>
                </div>
                <div class="modal-actions">
                    <a id="modalEditLink" href="#" class="btn btn-primary">Edit Activity</a>
                    <button class="btn btn-secondary" onclick="emailParticipants()">Email Team</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentActivityId = null;

function openActivityModal(activityId) {
    currentActivityId = activityId;
    const modal = document.getElementById('activityModal');
    const loading = document.getElementById('modalLoading');
    const body = document.getElementById('modalBody');

    modal.style.display = 'flex';
    loading.style.display = 'block';
    body.style.display = 'none';

    fetch(`/activities/${activityId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalActivityName').textContent = data.name;
            document.getElementById('modalStatusBadge').textContent = data.status_label;
            document.getElementById('modalStatusBadge').className = `status-badge status-${data.status}`;
            document.getElementById('modalUrgencyBadge').innerHTML = data.urgency_badge;

            document.getElementById('modalLead').textContent = data.lead ? data.lead.full_name : 'No lead assigned';

            if (data.parties && data.parties.length > 0) {
                document.getElementById('modalParties').textContent = data.parties.map(p => p.full_name).join(', ');
            } else {
                document.getElementById('modalParties').textContent = 'No participants';
            }

            document.getElementById('modalStartDate').textContent = data.start_date || '-';
            document.getElementById('modalDueDate').textContent = data.due_date || 'No due date';

            const logicSection = document.getElementById('modalLogicSection');
            const logicContent = document.getElementById('modalLogic');
            if (data.logic) {
                logicContent.textContent = data.logic;
                logicSection.style.display = 'block';
            } else {
                logicSection.style.display = 'none';
            }

            const nextStepSection = document.getElementById('modalNextStepSection');
            const nextStepContent = document.getElementById('modalNextStep');
            if (data.next_step) {
                nextStepContent.textContent = data.next_step;
                nextStepSection.style.display = 'block';
            } else {
                nextStepSection.style.display = 'none';
            }

            document.getElementById('modalCreatedAt').textContent = data.created_at || '-';
            document.getElementById('modalUpdatedAt').textContent = data.updated_at || '-';
            document.getElementById('modalEditLink').href = `/activities/${activityId}/edit`;

            loading.style.display = 'none';
            body.style.display = 'block';
        })
        .catch(error => {
            console.error('Error loading activity:', error);
            loading.textContent = 'Error loading activity';
        });
}

function closeActivityModal() {
    document.getElementById('activityModal').style.display = 'none';
    currentActivityId = null;
}

function emailParticipants() {
    if (!currentActivityId) return;

    const message = prompt('Enter an optional message to include in the email (or leave blank):');

    fetch(`/activities/${currentActivityId}/email-participants`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => {
        console.error('Error sending email:', error);
        alert('Failed to send email');
    });
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeActivityModal();
    }
});
</script>
@endpush
