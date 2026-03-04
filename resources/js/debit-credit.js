(function () {

    function sync(row) {
        const debit  = row.querySelector('.debit');
        const credit = row.querySelector('.credit');

        if (!debit || !credit) return;

        const d = debit.value !== '' && parseFloat(debit.value) > 0;
        const c = credit.value !== '' && parseFloat(credit.value) > 0;

        if (d) {
            credit.disabled = true;
            debit.disabled = false;
        } else if (c) {
            debit.disabled = true;
            credit.disabled = false;
        } else {
            debit.disabled = false;
            credit.disabled = false;
        }
    }

    // Event delegation (works for dynamic rows)
    document.addEventListener('input', function (e) {
        if (!e.target.classList.contains('line-amount')) return;
        const row = e.target.closest('tr');
        if (row) sync(row);
    });

    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('line-amount')) return;
        const row = e.target.closest('tr');
        if (row) sync(row);
    });

    // Init on load (edit mode support)
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('tr').forEach(row => {
            if (row.querySelector('.debit') && row.querySelector('.credit')) {
                sync(row);
            }
        });
    });

})();
