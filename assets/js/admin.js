/**
 * SKYCA DIGITAL PRIVATE LIMITED — ADMIN DASHBOARD CONTROLLER (admin.js)
 * Handles lead inspection modal, status updates, internal notes, settings, and password updates.
 */

(function () {
    'use strict';

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var csrfToken = document.body.getAttribute('data-csrf') || '';
        var modal = document.getElementById('lead-detail-modal');
        var modalBody = document.getElementById('modal-lead-body');
        var closeModalBtn = document.getElementById('close-lead-modal-btn');

        function closeModal() {
            if (modal) modal.classList.add('hidden');
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
        }

        // Open Lead Detail Modal
        var openBtns = document.querySelectorAll('.js-open-lead-modal');
        openBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var leadId = btn.getAttribute('data-lead-id');
                if (!leadId || !modal || !modalBody) return;

                modal.classList.remove('hidden');
                modalBody.innerHTML = '<p class="text-muted">Loading enquiry details...</p>';

                fetch('/api/admin?action=get_lead&id=' + encodeURIComponent(leadId), {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (!res.success || !res.lead) {
                            modalBody.innerHTML = '<div class="form-alert alert-error">' + escapeHtml(res.message || 'Failed to load lead.') + '</div>';
                            return;
                        }
                        var l = res.lead;
                        var notes = res.notes || [];
                        var statuses = ['New', 'Contacted', 'Qualified', 'Converted', 'Closed'];
                        var statusOptionsHtml = statuses.map(function (st) {
                            var sel = (l.status === st) ? ' selected' : '';
                            return '<option value="' + escapeHtml(st) + '"' + sel + '>' + escapeHtml(st) + '</option>';
                        }).join('');

                        var notesHtml = notes.length === 0
                            ? '<p class="text-muted text-sm">No internal notes recorded yet.</p>'
                            : notes.map(function (n) {
                                return '<div class="lead-note-item">' +
                                    '<div class="lead-note-meta"><strong>' + escapeHtml(n.admin_username) + '</strong> &bull; <span>' + escapeHtml(n.created_at) + '</span></div>' +
                                    '<div class="lead-note-text">' + escapeHtml(n.note_text) + '</div>' +
                                    '</div>';
                            }).join('');

                        modalBody.innerHTML =
                            '<div class="lead-modal-grid">' +
                                '<div class="lead-modal-info">' +
                                    '<div class="lead-detail-group">' +
                                        '<span class="detail-label">Reference ID</span>' +
                                        '<strong class="lead-ref-mono">' + escapeHtml(l.lead_reference) + '</strong>' +
                                    '</div>' +
                                    '<div class="lead-detail-group">' +
                                        '<span class="detail-label">Client Name &amp; Company</span>' +
                                        '<div><strong>' + escapeHtml(l.full_name) + '</strong> ' + (l.company_name ? '(' + escapeHtml(l.company_name) + ')' : '') + '</div>' +
                                    '</div>' +
                                    '<div class="lead-detail-group">' +
                                        '<span class="detail-label">Direct Contact</span>' +
                                        '<div>Email: <a href="mailto:' + escapeHtml(l.business_email) + '">' + escapeHtml(l.business_email) + '</a> | Phone: <a href="tel:' + escapeHtml(l.phone) + '">' + escapeHtml(l.phone) + '</a></div>' +
                                    '</div>' +
                                    '<div class="lead-detail-group">' +
                                        '<span class="detail-label">Service Required</span>' +
                                        '<div><strong>' + escapeHtml(l.requested_service) + '</strong></div>' +
                                    '</div>' +
                                    '<div class="lead-detail-group">' +
                                        '<span class="detail-label">Budget &amp; Timeline</span>' +
                                        '<div>Budget: ' + escapeHtml(l.estimated_budget || 'Not specified') + ' | Timeline: ' + escapeHtml(l.project_timeline || 'Not specified') + '</div>' +
                                    '</div>' +
                                    '<div class="lead-detail-group">' +
                                        '<span class="detail-label">Project Description</span>' +
                                        '<div class="lead-desc-box">' + escapeHtml(l.project_description) + '</div>' +
                                    '</div>' +
                                    '<div class="lead-detail-group">' +
                                        '<span class="detail-label">Attribution &amp; Consent</span>' +
                                        '<div class="text-sm text-muted">' +
                                            'Landing Page: ' + escapeHtml(l.landing_page || '/') + '<br>' +
                                            'Referrer: ' + escapeHtml(l.referrer || 'Direct') + '<br>' +
                                            'UTM Source: ' + escapeHtml(l.utm_source || '-') + ' | Medium: ' + escapeHtml(l.utm_medium || '-') + ' | Campaign: ' + escapeHtml(l.utm_campaign || '-') + '<br>' +
                                            'Consent Timestamp: ' + escapeHtml(l.consent_timestamp) +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="lead-modal-workflow">' +
                                    '<form id="modal-update-lead-form">' +
                                        '<input type="hidden" name="action" value="update_lead">' +
                                        '<input type="hidden" name="csrf_token" value="' + escapeHtml(csrfToken) + '">' +
                                        '<input type="hidden" name="lead_id" value="' + escapeHtml(l.id) + '">' +
                                        '<div id="modal-update-alert" class="form-alert hidden"></div>' +
                                        '<div class="form-group">' +
                                            '<label class="form-label">Lead Status</label>' +
                                            '<select name="status" class="form-control">' + statusOptionsHtml + '</select>' +
                                        '</div>' +
                                        '<div class="form-group">' +
                                            '<label class="form-label">Internal Summary Notes</label>' +
                                            '<textarea name="internal_notes" class="form-control" rows="3" placeholder="Summary notes for this enquiry...">' + escapeHtml(l.internal_notes || '') + '</textarea>' +
                                        '</div>' +
                                        '<div class="form-group">' +
                                            '<label class="form-label">Add Activity Log Entry</label>' +
                                            '<textarea name="new_note" class="form-control" rows="2" placeholder="Optional timestamped note (e.g., Spoke with client, sent proposal)..."></textarea>' +
                                        '</div>' +
                                        '<button type="submit" class="btn btn-primary btn-block">Save Status &amp; Notes</button>' +
                                    '</form>' +
                                    '<div class="lead-notes-history mt-6">' +
                                        '<h4>Activity History</h4>' +
                                        notesHtml +
                                    '</div>' +
                                '</div>' +
                            '</div>';

                        var updateForm = document.getElementById('modal-update-lead-form');
                        if (updateForm) {
                            updateForm.addEventListener('submit', function (ev) {
                                ev.preventDefault();
                                var alertEl = document.getElementById('modal-update-alert');
                                var fd = new FormData(updateForm);
                                fetch('/api/admin', {
                                    method: 'POST',
                                    body: fd
                                })
                                    .then(function (r) { return r.json(); })
                                    .then(function (updRes) {
                                        if (updRes.success) {
                                            if (alertEl) {
                                                alertEl.className = 'form-alert alert-success';
                                                alertEl.textContent = updRes.message || 'Saved!';
                                            }
                                            setTimeout(function () { window.location.reload(); }, 600);
                                        } else if (alertEl) {
                                            alertEl.className = 'form-alert alert-error';
                                            alertEl.textContent = updRes.message || 'Update failed.';
                                        }
                                    });
                            });
                        }
                    });
            });
        });

        // Settings Form Handler
        var settingsForm = document.getElementById('admin-settings-form');
        if (settingsForm) {
            settingsForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var alertEl = document.getElementById('admin-settings-alert');
                var fd = new FormData(settingsForm);
                fetch('/api/admin', { method: 'POST', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (alertEl) {
                            alertEl.className = 'form-alert ' + (res.success ? 'alert-success' : 'alert-error');
                            alertEl.textContent = res.message || 'Operation completed.';
                        }
                    });
            });
        }

        // Password Change Form Handler
        var passwordForm = document.getElementById('admin-password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var alertEl = document.getElementById('admin-password-alert');
                var fd = new FormData(passwordForm);
                fetch('/api/admin', { method: 'POST', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (alertEl) {
                            alertEl.className = 'form-alert ' + (res.success ? 'alert-success' : 'alert-error');
                            alertEl.textContent = res.message || 'Operation completed.';
                        }
                        if (res.success) {
                            passwordForm.reset();
                        }
                    });
            });
        }
    });
})();
