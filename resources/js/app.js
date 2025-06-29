import "./libs/trix";
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver/theme';
import 'tinymce/icons/default/icons';
import 'tinymce/models/dom/model';

document.addEventListener('alpine:init', () => {
    Alpine.data('tinymce', (wire, options) => ({
        init() {
            tinymce.init({
                ...options,
                target: this.$el,
                setup: (editor) => {
                    editor.on('blur', () => {
                        wire.set(options.wireModel, editor.getContent());
                    });
                }
            });
        }
    }));
});