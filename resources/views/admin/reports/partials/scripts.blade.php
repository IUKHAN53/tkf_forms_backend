<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cascading selector: changing the UC reloads to refresh fixed sites.
    var ucSelect = document.getElementById('fsrUc');
    var fixSelect = document.getElementById('fsrFixSite');
    var form = document.getElementById('fsrForm');

    if (ucSelect) {
        ucSelect.addEventListener('change', function () {
            if (fixSelect) fixSelect.value = '';
            form.submit();
        });
    }
    if (fixSelect) {
        fixSelect.addEventListener('change', function () {
            if (this.value) form.submit();
        });
    }

    // Expand / collapse individual records.
    document.querySelectorAll('.fsr-record-head').forEach(function (head) {
        head.addEventListener('click', function () {
            head.closest('.fsr-record').classList.toggle('open');
        });
    });

    // Expand / collapse all.
    var expandAll = document.getElementById('fsrExpandAll');
    var collapseAll = document.getElementById('fsrCollapseAll');
    if (expandAll) {
        expandAll.addEventListener('click', function () {
            document.querySelectorAll('.fsr-record').forEach(function (r) { r.classList.add('open'); });
        });
    }
    if (collapseAll) {
        collapseAll.addEventListener('click', function () {
            document.querySelectorAll('.fsr-record').forEach(function (r) { r.classList.remove('open'); });
        });
    }
});
</script>
