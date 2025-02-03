document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('addForm');
    form.style.display = 'none';
});

document.getElementById('addFormBtn').addEventListener('click', function() {
    var form = document.getElementById('addForm');

    form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
});
