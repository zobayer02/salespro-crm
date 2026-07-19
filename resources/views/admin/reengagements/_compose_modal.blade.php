<div class="compose-backdrop" data-compose-backdrop>
    <form class="compose-card" method="POST" action="{{ route('admin.reengagements.store') }}" data-compose-form>
        @csrf
        <input type="hidden" name="customer_id" data-compose-customer-id>
        <input type="hidden" name="customer_assignment_id" data-compose-assignment-id>

        <div class="compose-header">
            <strong>New Message</strong>
            <button class="compose-close" type="button" data-compose-close>&times;</button>
        </div>

        <div class="compose-body">
            <div class="field-group">
                <label for="compose_to">To</label>
                <input id="compose_to" class="field-control" type="email" data-compose-to readonly>
            </div>

            <div class="field-group">
                <label for="compose_subject">Subject</label>
                <input id="compose_subject" class="field-control" type="text" name="subject" value="We have a special update for you from SalesPro" maxlength="160">
            </div>

            <div class="field-group">
                <label for="compose_message">Message</label>
                <textarea id="compose_message" class="field-control" name="message" maxlength="2000" data-compose-message>We noticed you have not purchased from us recently. We would like to reconnect and share new offers that may be useful for you.</textarea>
            </div>
        </div>

        <div class="compose-footer">
            <button class="secondary-button" type="button" data-compose-close>Cancel</button>
            <button class="primary-button" type="submit">Send</button>
        </div>
    </form>
</div>

<script>
    const composeBackdrop = document.querySelector('[data-compose-backdrop]');
    const composeCustomerId = document.querySelector('[data-compose-customer-id]');
    const composeAssignmentId = document.querySelector('[data-compose-assignment-id]');
    const composeTo = document.querySelector('[data-compose-to]');
    const composeMessage = document.querySelector('[data-compose-message]');

    const openCompose = (button) => {
        composeCustomerId.value = button.dataset.customerId || '';
        composeAssignmentId.value = button.dataset.assignmentId || '';
        composeTo.value = button.dataset.customerEmail || '';

        if (button.dataset.customerName) {
            composeMessage.value = `Hello ${button.dataset.customerName},\n\nWe noticed you have not purchased from us recently. We would like to reconnect and share new offers that may be useful for you.\n\nThank you,\nSalesPro Team`;
        }

        composeBackdrop.classList.add('open');
    };

    window.openSalesProCompose = openCompose;

    document.querySelectorAll('[data-compose-email]:not([data-bound])').forEach((button) => {
        button.dataset.bound = 'true';
        button.addEventListener('click', () => window.openSalesProCompose(button));
    });

    document.querySelectorAll('[data-compose-close]').forEach((button) => {
        button.addEventListener('click', () => composeBackdrop.classList.remove('open'));
    });

    composeBackdrop.addEventListener('click', (event) => {
        if (event.target === composeBackdrop) {
            composeBackdrop.classList.remove('open');
        }
    });

    window.addEventListener('resize', () => composeBackdrop.classList.remove('open'));
</script>
