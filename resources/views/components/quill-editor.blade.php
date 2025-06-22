@props([
    'name' => 'content',
    'id' => 'quill-editor',
    'label' => '',
    'value' => '',
    'placeholder' => 'Tulis sesuatu...',
    'height' => '300px'
])

<div class="space-y-2">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <div wire:ignore>
        <div id="{{ $id }}" class="quill-editor" style="height: {{ $height }};">{!! $value !!}</div>
        <input type="hidden" name="{{ $name }}" id="{{ $id }}-input" value="{!! e($value) !!}">
    </div>
</div>

@once
    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const editors = document.querySelectorAll('.quill-editor');

                editors.forEach(function (editor) {
                    const id = editor.id;
                    const input = document.getElementById(id + '-input');

                    const quill = new Quill('#' + id, {
                        theme: 'snow',
                        placeholder: editor.getAttribute('placeholder') || 'Tulis sesuatu...',
                        modules: {
                            toolbar: [
                                [{ 'font': [] }, { 'size': [] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'script': 'sub' }, { 'script': 'super' }],
                                [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                                [{ 'direction': 'rtl' }, { 'align': [] }],
                                ['link', 'image', 'video'],
                                ['clean']
                            ]
                        }
                    });

                    // Image upload handler
                    const toolbar = quill.getModule('toolbar');
                    toolbar.addHandler('image', () => {
                        const inputImage = document.createElement('input');
                        inputImage.setAttribute('type', 'file');
                        inputImage.setAttribute('accept', 'image/*');
                        inputImage.click();

                        inputImage.onchange = () => {
                            const file = inputImage.files[0];
                            if (file) {
                                const formData = new FormData();
                                formData.append('image', file);

                                fetch("{{ route('layanan.uploadImage') }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(result => {
                                    if (result.success) {
                                        const range = quill.getSelection(true);
                                        quill.insertEmbed(range.index, 'image', result.url);
                                    } else {
                                        alert('Upload gagal: ' + result.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error upload:', error);
                                    alert('Upload gambar gagal.');
                                });
                            }
                        };
                    });

                    // Set initial content
                    if (input.value) {
                        quill.root.innerHTML = input.value;
                    }

                    // Sync to hidden input
                    quill.on('text-change', function () {
                        input.value = quill.root.innerHTML;
                    });
                });
            });
        </script>

        <style>
            .quill-editor img {
                max-width: 100%;
                height: auto;
                display: block;
                margin: 10px 0;
            }
        </style>
    @endpush
@endonce
