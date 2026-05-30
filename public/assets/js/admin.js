const sectionSelect = document.querySelector('#section-select');
const categorySelect = document.querySelector('#category-select');

function filterCategories() {
    if (!sectionSelect || !categorySelect) return;

    const active = sectionSelect.value;
    for (const option of categorySelect.options) {
        if (!option.dataset.section) {
            option.hidden = false;
            continue;
        }
        option.hidden = option.dataset.section !== active;
        if (option.hidden && option.selected) {
            categorySelect.value = '';
        }
    }
}

sectionSelect?.addEventListener('change', filterCategories);
filterCategories();

if (window.ClassicEditor && document.querySelector('#body')) {
    ClassicEditor
        .create(document.querySelector('#body'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                'blockQuote', 'insertTable', 'undo', 'redo'
            ]
        })
        .catch(error => console.error(error));
}
